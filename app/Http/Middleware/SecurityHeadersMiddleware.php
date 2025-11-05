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
        
        // More permissive CSP for development and external resources
        // Note: blob: and data: are needed for PDF.js worker
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' blob: data: https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://cdn.tailwindcss.com https://apis.google.com https://challenges.cloudflare.com https://accounts.google.com; " .
               "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://cdn.tailwindcss.com https://fonts.googleapis.com; " .
               "img-src 'self' data: blob: https: https://developers.google.com https://www.gstatic.com; " .
               "font-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.gstatic.com; " .
               "connect-src 'self' blob: data: https://accounts.google.com https://oauth2.googleapis.com https://www.googleapis.com https://challenges.cloudflare.com; " .
               "worker-src 'self' blob: data: https://cdnjs.cloudflare.com; " .
               "frame-src 'self' https://accounts.google.com https://challenges.cloudflare.com; " .
               "frame-ancestors 'none'; " .
               "base-uri 'self'; " .
               "form-action 'self';";
        
        // Only apply CSP in production, disable for development
        if (app()->environment('production')) {
            $response->headers->set('Content-Security-Policy', $csp);
        } else {
            // More permissive CSP for development
            // Note: blob: and data: are needed for PDF.js worker
            $devCsp = "default-src 'self' 'unsafe-inline' 'unsafe-eval' data: blob:; " .
                     "script-src 'self' 'unsafe-inline' 'unsafe-eval' blob: data: https:; " .
                     "style-src 'self' 'unsafe-inline' https:; " .
                     "img-src 'self' data: blob: https:; " .
                     "font-src 'self' https:; " .
                     "connect-src 'self' blob: data: https:; " .
                     "worker-src 'self' blob: data: https:; " .
                     "frame-src 'self' https:;";
            $response->headers->set('Content-Security-Policy', $devCsp);
        }
        
        // Force correct MIME types for CSS and JS files
        if (str_contains($request->path(), '.css')) {
            $response->headers->set('Content-Type', 'text/css; charset=utf-8');
        } elseif (str_contains($request->path(), '.js')) {
            $response->headers->set('Content-Type', 'application/javascript; charset=utf-8');
        }
        
        // HSTS header for HTTPS
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }
        
        return $response;
    }
}