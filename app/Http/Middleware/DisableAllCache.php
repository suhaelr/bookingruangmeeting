<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DisableAllCache
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Disable all caching
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, private, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
        $response->headers->set('ETag', '');
        
        // Disable browser caching
        $response->headers->set('Vary', '*');
        $response->headers->set('X-Accel-Expires', '0');
        
        // Force session regeneration on every request
        if (session()->isStarted()) {
            session()->regenerate(true);
        }
        
        return $response;
    }
}
