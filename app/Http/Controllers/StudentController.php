<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Attendance;
use App\Models\Lecture;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    /**
     * Display student dashboard
     */
    public function dashboard()
    {
        $student = Auth::user();
        
        // Get enrolled courses
        $enrollments = $student->enrollments()
            ->with('course')
            ->where('status', 'active')
            ->get();

        // Get recent attendance records
        $recentAttendance = $student->attendedLectures()
            ->with(['lecture.course'])
            ->latest()
            ->take(5)
            ->get();

        // Calculate attendance statistics
        $totalLectures = $student->attendedLectures()->count();
        $presentCount = $student->attendedLectures()
            ->where('status', 'present')
            ->count();
        $lateCount = $student->attendedLectures()
            ->where('status', 'late')
            ->count();
            
        $attendancePercentage = $totalLectures > 0 ? round(($presentCount / $totalLectures) * 100, 2) : 0;

        return view('student.dashboard', compact(
            'enrollments', 
            'recentAttendance', 
            'attendancePercentage',
            'totalLectures',
            'presentCount',
            'lateCount'
        ));
    }

    /**
     * Display student's courses
     */
    public function courses()
    {
        $student = Auth::user();
        $enrollments = $student->enrollments()
            ->with(['course.lecturer', 'course.lectures'])
            ->where('status', 'active')
            ->get();
        
        // Calculate attendance for each course
        foreach ($enrollments as $enrollment) {
            $courseLectures = $enrollment->course->lectures()->count();
            $attendedLectures = Attendance::where('student_id', $student->id)
                ->where('course_id', $enrollment->course_id)
                ->count();
                
            $enrollment->course->attendance_percentage = $courseLectures > 0 
                ? round(($attendedLectures / $courseLectures) * 100, 2) 
                : 0;
            $enrollment->course->attended_lectures = $attendedLectures;
            $enrollment->course->total_lectures = $courseLectures;
        }
        
        return view('student.courses', compact('enrollments'));
    }

    /**
     * Display student's attendance history
     */
    public function attendance()
    {
        $student = Auth::user();
        $attendance = $student->attendedLectures()
            ->with(['lecture.course', 'course'])
            ->orderBy('date', 'desc')
            ->orderBy('marked_at', 'desc')
            ->get();
        
        // Get attendance statistics by course
        $courseStats = [];
        $enrollments = $student->enrollments()
            ->with('course')
            ->where('status', 'active')
            ->get();
            
        foreach ($enrollments as $enrollment) {
            $courseLectures = $enrollment->course->lectures()->count();
            $attendedLectures = Attendance::where('student_id', $student->id)
                ->where('course_id', $enrollment->course_id)
                ->count();
                
            $courseStats[$enrollment->course_id] = [
                'course' => $enrollment->course,
                'attended' => $attendedLectures,
                'total' => $courseLectures,
                'percentage' => $courseLectures > 0 ? round(($attendedLectures / $courseLectures) * 100, 2) : 0
            ];
        }
        
        return view('student.attendance', compact('attendance', 'courseStats'));
    }

    /**
     * Display QR code scanning page
     */
    public function scanQR()
    {
        return view('student.scan-qr');
    }

    /**
     * Mark attendance via QR code scan
     */
    

    /**
     * Validate QR code and return lecture
     */
    private function validateAndGetLecture($qrCode)
    {
        try {
            Log::debug('Validating QR code', ['qr_preview' => substr($qrCode, 0, 30)]);
            
            // Method 1: Try to find by QR code directly (most common)
            $lecture = Lecture::where('qr_code', $qrCode)
                ->with('course')
                ->first();
                
            if ($lecture) {
                Log::debug('Found lecture by direct QR code match', ['lecture_id' => $lecture->id]);
                return $lecture;
            }

            // Method 2: Try to decode as base64 JSON
            if (strlen($qrCode) > 20 && base64_decode($qrCode, true)) {
                $decoded = base64_decode($qrCode, true);
                if ($decoded !== false) {
                    $decodedData = json_decode($decoded, true);
                    
                    if ($decodedData && isset($decodedData['lecture_id']) && isset($decodedData['type']) && $decodedData['type'] === 'attendance') {
                        $lecture = Lecture::where('id', $decodedData['lecture_id'])
                            ->with('course')
                            ->first();
                            
                        if ($lecture) {
                            Log::debug('Found lecture by decoded QR data', ['lecture_id' => $lecture->id]);
                            return $lecture;
                        }
                    }
                }
            }

            // Method 3: Try to extract lecture ID from QR code string
            if (preg_match('/lecture[:_](\d+)/i', $qrCode, $matches)) {
                $lecture = Lecture::where('id', $matches[1])
                    ->with('course')
                    ->first();
                    
                if ($lecture) {
                    Log::debug('Found lecture by pattern match', ['lecture_id' => $lecture->id]);
                    return $lecture;
                }
            }

            // Method 4: Check if QR code contains lecture ID in any format
            if (is_numeric($qrCode)) {
                $lecture = Lecture::where('id', $qrCode)
                    ->with('course')
                    ->first();
                    
                if ($lecture) {
                    Log::debug('Found lecture by numeric ID', ['lecture_id' => $lecture->id]);
                    return $lecture;
                }
            }

            Log::warning('No lecture found for QR code', ['qr_preview' => substr($qrCode, 0, 30)]);
            return null;

        } catch (\Exception $e) {
            Log::error('QR code validation error', [
                'error' => $e->getMessage(),
                'qr_preview' => substr($qrCode, 0, 30)
            ]);
            return null;
        }
    }

    /**
     * Determine attendance status based on timing
     */
    private function determineAttendanceStatus($lecture)
    {
        $lectureTime = Carbon::parse($lecture->schedule);
        $currentTime = Carbon::now();
        
        // If student is more than 5 minutes late, mark as late
        $minutesLate = $currentTime->diffInMinutes($lectureTime, false);
        if ($minutesLate > 5) {
            return 'late';
        }
        
        return 'present';
    }

    /**
     * Get attendance statistics for a specific course
     */
    public function courseAttendance($courseId)
    {
        $student = Auth::user();
        
        // Verify student is enrolled in the course
        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('course_id', $courseId)
            ->where('status', 'active')
            ->firstOrFail();

        $course = Course::with('lectures')->findOrFail($courseId);
        
        $attendance = Attendance::where('student_id', $student->id)
            ->where('course_id', $courseId)
            ->with('lecture')
            ->orderBy('date', 'desc')
            ->get();

        // Calculate statistics
        $totalLectures = $course->lectures->count();
        $presentCount = $attendance->where('status', 'present')->count();
        $lateCount = $attendance->where('status', 'late')->count();
        $absentCount = $totalLectures - ($presentCount + $lateCount);
        $attendancePercentage = $totalLectures > 0 ? round((($presentCount + $lateCount) / $totalLectures) * 100, 2) : 0;

        return view('student.course-attendance', compact(
            'course',
            'attendance',
            'totalLectures',
            'presentCount',
            'lateCount',
            'absentCount',
            'attendancePercentage'
        ));
    }

    /**
     * Export attendance as PDF
     */
    public function exportAttendance()
    {
        $student = Auth::user();
        $attendance = $student->attendedLectures()
            ->with(['lecture.course'])
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $attendance
        ]);
    }

    /**
     * Get today's lectures for student
     */
    public function todaysLectures()
    {
        $student = Auth::user();
        
        $enrolledCourseIds = $student->enrollments()
            ->where('status', 'active')
            ->pluck('course_id');

        $todaysLectures = Lecture::whereIn('course_id', $enrolledCourseIds)
            ->whereDate('schedule', Carbon::today())
            ->with('course')
            ->orderBy('schedule')
            ->get();

        // Mark which lectures attendance is already taken
        foreach ($todaysLectures as $lecture) {
            $lecture->attendance_taken = Attendance::where('student_id', $student->id)
                ->where('lecture_id', $lecture->id)
                ->exists();
                
            // Add QR validity information
            $lecture->qr_valid = $lecture->isQRCodeValid();
            $lecture->qr_validity_window = $lecture->getQRValidityWindow();
        }

        return response()->json([
            'success' => true,
            'lectures' => $todaysLectures
        ]);
    }
    /**
 * Mark attendance via QR code scan
 */
public function markAttendance(Request $request)
{
    // Log the incoming request for debugging
    Log::info('Attendance marking request received', [
        'student_id' => Auth::id(),
        'qr_code_length' => $request->qr_code ? strlen($request->qr_code) : 0,
        'qr_code_preview' => $request->qr_code ? substr($request->qr_code, 0, 50) : 'empty',
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent()
    ]);

    try {
        // Validate input
        $validator = Validator::make($request->all(), [
            'qr_code' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            Log::warning('QR code validation failed', ['errors' => $validator->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code data. Please scan a valid QR code.'
            ], 400);
        }

        $student = Auth::user();
        
        DB::beginTransaction();

        // Validate and decode QR code
        $lecture = $this->validateAndGetLecture($request->qr_code);
        
        if (!$lecture) {
            Log::warning('Invalid QR code scanned', [
                'student_id' => $student->id,
                'qr_preview' => substr($request->qr_code, 0, 30)
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code. Please scan a valid lecture QR code.'
            ], 400);
        }

        // DEBUG: Log course status
        Log::debug('Course status check', [
            'course_id' => $lecture->course_id,
            'course_name' => $lecture->course->name,
            'is_active' => $lecture->course->is_active,
            'lecturer_id' => $lecture->course->lecturer_id
        ]);

        // TEMPORARY: Comment out course active check for testing
        // Check if lecture is active
        // if (!$lecture->course->is_active) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'This course is no longer active.'
        //     ], 400);
        // }

        // Check QR code validity (5 minutes before/after lecture start)
        if (!$lecture->isQRCodeValid()) {
            $validityWindow = $lecture->getQRValidityWindow();
            Log::warning('QR code scanned outside validity window', [
                'student_id' => $student->id,
                'lecture_id' => $lecture->id,
                'lecture_time' => $lecture->schedule,
                'validity_window' => $validityWindow
            ]);
            return response()->json([
                'success' => false,
                'message' => "QR code is not valid at this time. " . $validityWindow
            ], 400);
        }

        // Check if student is enrolled in the course
        $isEnrolled = Enrollment::where('student_id', $student->id)
            ->where('course_id', $lecture->course_id)
            ->where('status', 'active')
            ->exists();
            
        if (!$isEnrolled) {
            Log::warning('Student not enrolled in course', [
                'student_id' => $student->id,
                'course_id' => $lecture->course_id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'You are not enrolled in this course: ' . $lecture->course->name
            ], 403);
        }

        // Check if attendance already marked for this lecture
        $existingAttendance = Attendance::where('student_id', $student->id)
            ->where('lecture_id', $lecture->id)
            ->first();

        if ($existingAttendance) {
            Log::info('Attendance already marked', [
                'student_id' => $student->id,
                'lecture_id' => $lecture->id,
                'existing_attendance' => $existingAttendance->id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Attendance already marked for: ' . $lecture->title
            ], 400);
        }

        // Determine attendance status based on timing
        $status = $this->determineAttendanceStatus($lecture);

        // Create attendance record
        $attendance = Attendance::create([
            'student_id' => $student->id,
            'lecture_id' => $lecture->id,
            'course_id' => $lecture->course_id,
            'date' => Carbon::today(),
            'status' => $status,
            'notes' => 'Marked via QR code scan',
            'marked_at' => Carbon::now()
        ]);

        DB::commit();

        // Log successful attendance
        Log::info('Attendance marked successfully', [
            'student_id' => $student->id,
            'lecture_id' => $lecture->id,
            'course_id' => $lecture->course_id,
            'status' => $status,
            'attendance_id' => $attendance->id,
            'qr_validity' => $lecture->getQRValidityWindow()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attendance marked successfully for "' . $lecture->title . '"! Status: ' . ucfirst($status),
            'attendance' => [
                'lecture' => $lecture->title,
                'course' => $lecture->course->name,
                'status' => $status,
                'time' => Carbon::now()->format('h:i A'),
                'qr_valid_until' => Carbon::parse($lecture->schedule)->addMinutes(5)->format('h:i A')
            ]
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        
        Log::error('Attendance marking failed', [
            'student_id' => Auth::id(),
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'System error: ' . $e->getMessage()
        ], 500);
    }
}
}