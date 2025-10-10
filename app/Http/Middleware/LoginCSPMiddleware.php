<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoginCSPMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Relaxed CSP for login page to allow external CDNs
        if ($request->is('login') || $request->is('auth/*')) {
            $csp = "default-src 'self'; " .
                   "script-src 'self' 'unsafe-inline' 'unsafe-eval' " .
                   "https://cdn.tailwindcss.com " .
                   "https://apis.google.com " .
                   "https://challenges.cloudflare.com " .
                   "https://cdn.jsdelivr.net " .
                   "https://cdnjs.cloudflare.com; " .
                   "style-src 'self' 'unsafe-inline' " .
                   "https://cdn.tailwindcss.com " .
                   "https://cdn.jsdelivr.net " .
                   "https://cdnjs.cloudflare.com; " .
                   "img-src 'self' data: https: " .
                   "https://developers.google.com " .
                   "https://challenges.cloudflare.com; " .
                   "font-src 'self' " .
                   "https://cdn.tailwindcss.com " .
                   "https://cdn.jsdelivr.net " .
                   "https://cdnjs.cloudflare.com; " .
                   "connect-src 'self' " .
                   "https://challenges.cloudflare.com " .
                   "https://oauth2.googleapis.com " .
                   "https://www.googleapis.com " .
                   "https://accounts.google.com; " .
                   "frame-src 'self' " .
                   "https://challenges.cloudflare.com; " .
                   "frame-ancestors 'none'; " .
                   "base-uri 'self'; " .
                   "form-action 'self';";
            
            $response->headers->set('Content-Security-Policy', $csp);
        }
        
        return $response;
    }
}
