<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CloudflareBypass
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
        // Add Cloudflare bypass headers for OAuth routes
        if (str_contains($request->path(), 'auth/google/callback') || str_contains($request->path(), 'auth/google')) {
            $request->headers->set('CF-Connecting-IP', $request->ip());
            $request->headers->set('X-Forwarded-For', $request->ip());
            $request->headers->set('X-Real-IP', $request->ip());
            $request->headers->set('CF-Ray', 'bypass-' . uniqid());
            $request->headers->set('CF-Visitor', '{"scheme":"https"}');
            $request->headers->set('CF-Cache-Status', 'BYPASS');
            $request->headers->set('CF-IPCountry', 'ID');
            $request->headers->set('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
            $request->headers->set('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8');
            $request->headers->set('Accept-Language', 'en-US,en;q=0.5');
            $request->headers->set('Accept-Encoding', 'gzip, deflate, br');
            $request->headers->set('DNT', '1');
            $request->headers->set('Connection', 'keep-alive');
            $request->headers->set('Upgrade-Insecure-Requests', '1');
            $request->headers->set('Sec-Fetch-Dest', 'document');
            $request->headers->set('Sec-Fetch-Mode', 'navigate');
            $request->headers->set('Sec-Fetch-Site', 'same-origin');
        }

        $response = $next($request);

        // Add no-cache headers for OAuth responses
        if (str_contains($request->path(), 'auth/google/callback') || str_contains($request->path(), 'auth/google')) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            $response->headers->set('CF-Cache-Status', 'BYPASS');
        }

        return $response;
    }
}
