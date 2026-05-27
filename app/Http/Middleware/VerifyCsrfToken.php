<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Add API endpoints that don't need CSRF protection
    ];
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        // Add CSRF token to all responses
        $response = $next($request);
        
        // If it's a JSON request and we're adding CSRF token
        if ($request->expectsJson()) {
            $response->headers->set('X-CSRF-Token', csrf_token());
        }
        
        return $response;
    }
}