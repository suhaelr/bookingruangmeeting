<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Security headers to prevent deface and other attacks
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        
        // Content Security Policy to prevent XSS and injection attacks
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' " .
               "https://cdn.jsdelivr.net https://cdnjs.cloudflare.com " .
               "https://apis.google.com " .
               "https://challenges.cloudflare.com; " .
               "style-src 'self' 'unsafe-inline' " .
               "https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " .
               "img-src 'self' data: https: " .
               "https://developers.google.com; " .
               "font-src 'self' " .
               "https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " .
               "connect-src 'self' " .
               "https://challenges.cloudflare.com " .
               "https://oauth2.googleapis.com " .
               "https://www.googleapis.com; " .
               "frame-src 'self' " .
               "https://challenges.cloudflare.com; " .
               "frame-ancestors 'none'; " .
               "base-uri 'self'; " .
               "form-action 'self';";
        
        $response->headers->set('Content-Security-Policy', $csp);
        
        // HSTS header for HTTPS
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }
        
        return $response;
    }
}