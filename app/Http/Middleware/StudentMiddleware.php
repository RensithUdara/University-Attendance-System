<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StudentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please log in.'
                ], 401);
            }
            
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        $user = Auth::user();

        // Check if user is active
        if (isset($user->is_active) && !$user->is_active) {
            Auth::logout();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account has been deactivated.'
                ], 403);
            }
            
            return redirect()->route('login')->with('error', 'Your account has been deactivated.');
        }

        // Check if user has student role
        if ($user->role !== 'student') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Student privileges required.'
                ], 403);
            }

            // Redirect based on user role
            return $this->redirectByRole($user->role, $request);
        }

        // Additional security checks
        if (!$this->passesSecurityChecks($user)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Security verification failed.'
                ], 403);
            }
            
            Auth::logout();
            return redirect()->route('login')->with('error', 'Security verification failed. Please log in again.');
        }

        // Set user timezone if available
        if ($request->hasHeader('Timezone')) {
            config(['app.timezone' => $request->header('Timezone')]);
        }

        // Share student data with all views
        if ($request->is('student/*') || $request->is('student')) {
            view()->share('currentStudent', $user);
        }

        return $next($request);
    }

    /**
     * Redirect user based on their role
     *
     * @param  string  $role
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    private function redirectByRole(string $role, Request $request)
    {
        $redirectRoutes = [
            'admin' => 'admin.dashboard',
            'lecturer' => 'lecturer.dashboard',
            'staff' => 'staff.dashboard',
        ];

        $route = $redirectRoutes[$role] ?? 'dashboard';
        
        return redirect()->route($route)->with('info', 'Redirected to your dashboard.');
    }

    /**
     * Perform additional security checks
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    private function passesSecurityChecks($user)
    {
        // Check if user email is verified (if required)
        if (config('auth.verify_email') && isset($user->email_verified_at) && !$user->hasVerifiedEmail()) {
            return false;
        }

        // Check if student profile is complete (optional)
        if (method_exists($user, 'studentProfile') && $user->studentProfile) {
            $profile = $user->studentProfile;
            if (isset($profile->is_complete) && !$profile->is_complete) {
                // Allow access but could redirect to profile completion page
                // For now, we'll just log it
                \Log::info("Student {$user->id} has incomplete profile");
            }
        }

        // Check for suspicious activity (basic example)
        if ($user->login_attempts > 5) {
            \Log::warning("Suspicious login attempts for student {$user->id}");
            // Could implement additional checks here
        }

        return true;
    }

    /**
     * Terminate the request and log student activity
     *
     * @param  Request  $request
     * @param  Response  $response
     * @return void
     */
    public function terminate(Request $request, Response $response)
    {
        // Log student activity for important actions
        if (Auth::check() && Auth::user()->role === 'student') {
            $this->logStudentActivity($request, $response);
        }
    }

    /**
     * Log student activity for monitoring and analytics
     *
     * @param  Request  $request
     * @param  Response  $response
     * @return void
     */
    private function logStudentActivity(Request $request, Response $response)
    {
        $user = Auth::user();
        $routeName = $request->route() ? $request->route()->getName() : null;
        
        // Only log specific important actions to avoid too much logging
        $importantRoutes = [
            'student.mark-attendance',
            'student.scan-qr',
            'student.courses',
            'student.attendance',
        ];

        if (in_array($routeName, $importantRoutes)) {
            \Log::channel('student_activity')->info('Student activity recorded', [
                'student_id' => $user->id,
                'route' => $routeName,
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'response_status' => $response->getStatusCode(),
                'timestamp' => now()->toISOString(),
            ]);
        }

        // Log QR code scan attempts specifically
        if ($routeName === 'student.mark-attendance') {
            $this->logAttendanceAttempt($request, $user, $response->getStatusCode());
        }
    }

    /**
     * Log attendance attempt details
     *
     * @param  Request  $request
     * @param  \App\Models\User  $user
     * @param  int  $statusCode
     * @return void
     */
    private function logAttendanceAttempt(Request $request, $user, $statusCode)
    {
        $logData = [
            'student_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status_code' => $statusCode,
            'timestamp' => now()->toISOString(),
        ];

        // Add QR code data if available (masked for security)
        if ($request->has('qr_code')) {
            $qrCode = $request->input('qr_code');
            $logData['qr_code_prefix'] = substr($qrCode, 0, 8) . '...';
            $logData['qr_code_length'] = strlen($qrCode);
        }

        \Log::channel('attendance')->info('Attendance attempt', $logData);
    }
}