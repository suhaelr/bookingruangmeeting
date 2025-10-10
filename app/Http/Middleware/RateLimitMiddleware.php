<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $key = 'rate_limit_' . $ip;
        $maxRequests = 100; // Maximum requests per minute
        $decayMinutes = 1; // Time window in minutes
        
        // Get current request count
        $currentRequests = Cache::get($key, 0);
        
        // Check if rate limit exceeded
        if ($currentRequests >= $maxRequests) {
            Log::warning('Rate limit exceeded', [
                'ip' => $ip,
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'requests' => $currentRequests
            ]);
            
            return response()->json([
                'error' => 'Too Many Requests',
                'message' => 'Rate limit exceeded. Please try again later.',
                'retry_after' => 60
            ], 429);
        }
        
        // Increment request count
        Cache::put($key, $currentRequests + 1, now()->addMinutes($decayMinutes));
        
        return $next($request);
    }
}