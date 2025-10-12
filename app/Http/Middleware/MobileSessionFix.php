<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class MobileSessionFix
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
        $userAgent = $request->userAgent();
        $isMobile = $this->isMobileDevice($userAgent);
        
        if ($isMobile) {
            Log::info('MobileSessionFix: Mobile device detected', [
                'user_agent' => $userAgent,
                'url' => $request->url(),
                'session_id' => session()->getId(),
                'session_started' => session()->isStarted()
            ]);
            
            // Ensure session is started for mobile devices
            if (!session()->isStarted()) {
                session()->start();
                Log::info('MobileSessionFix: Session started for mobile device');
            }
            
            // Add mobile-specific headers to prevent caching issues
            $request->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, private, max-age=0');
            $request->headers->set('Pragma', 'no-cache');
            $request->headers->set('Expires', '0');
            
            // Add mobile-specific session cookie settings
            $request->headers->set('Set-Cookie', 'laravel_session=' . session()->getId() . '; Path=/; HttpOnly; SameSite=Lax');
        }
        
        $response = $next($request);
        
        // Add mobile-specific response headers
        if ($isMobile) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, private, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
            
            // Force session save for mobile devices
            try {
                Session::save();
                Log::info('MobileSessionFix: Session saved for mobile device', [
                    'session_id' => session()->getId(),
                    'user_logged_in' => Session::get('user_logged_in'),
                    'has_user_data' => Session::has('user_data')
                ]);
            } catch (\Exception $e) {
                Log::error('MobileSessionFix: Error saving session for mobile', [
                    'error' => $e->getMessage(),
                    'session_id' => session()->getId()
                ]);
            }
        }
        
        return $response;
    }
    
    /**
     * Check if the request is from a mobile device
     */
    private function isMobileDevice($userAgent)
    {
        $mobileKeywords = [
            'Mobile', 'Android', 'iPhone', 'iPad', 'iPod', 'BlackBerry', 
            'Windows Phone', 'Opera Mini', 'IEMobile', 'Mobile Safari'
        ];
        
        foreach ($mobileKeywords as $keyword) {
            if (stripos($userAgent, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }
}
