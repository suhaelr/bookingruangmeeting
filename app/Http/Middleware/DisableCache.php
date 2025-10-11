<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DisableCache
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Disable all caching
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
        $response->headers->set('ETag', '');
        $response->headers->set('Vary', '*');
        
        // Disable browser caching
        $response->headers->set('X-Accel-Expires', '0');
        $response->headers->set('X-Cache', 'DISABLED');
        $response->headers->set('X-Cache-Lookup', 'DISABLED');
        
        return $response;
    }
}
