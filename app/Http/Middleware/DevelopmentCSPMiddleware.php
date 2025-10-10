<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DevelopmentCSPMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Very permissive CSP for development to allow all CDNs
        if ($request->is('login') || $request->is('auth/*') || $request->is('admin/*') || $request->is('user/*')) {
            $csp = "default-src 'self' 'unsafe-inline' 'unsafe-eval' data: blob:; " .
                   "script-src 'self' 'unsafe-inline' 'unsafe-eval' " .
                   "https: http: data: blob:; " .
                   "style-src 'self' 'unsafe-inline' " .
                   "https: http: data: blob:; " .
                   "img-src 'self' data: blob: " .
                   "https: http:; " .
                   "font-src 'self' data: " .
                   "https: http:; " .
                   "connect-src 'self' " .
                   "https: http: ws: wss:; " .
                   "frame-src 'self' " .
                   "https: http:; " .
                   "frame-ancestors 'none'; " .
                   "base-uri 'self'; " .
                   "form-action 'self';";
            
            $response->headers->set('Content-Security-Policy', $csp);
        }
        
        return $response;
    }
}
