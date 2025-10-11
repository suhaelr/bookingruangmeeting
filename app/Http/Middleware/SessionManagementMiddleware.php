<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SessionManagementMiddleware
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
        // Only process for login-related routes
        if ($request->routeIs('login') || $request->routeIs('logout')) {
            // Check if session is valid for login page
            if ($request->routeIs('login') && $request->isMethod('GET')) {
                // Clear any invalid session data on login page load
                if (Session::has('user_logged_in') && !Session::has('user_data')) {
                    Session::forget('user_logged_in');
                    Session::forget('user_data');
                    Session::regenerate();
                }
                
                // Check if user_data is corrupted
                if (Session::has('user_data')) {
                    $userData = Session::get('user_data');
                    if (!is_array($userData) || !isset($userData['id']) || !isset($userData['role'])) {
                        Session::forget('user_logged_in');
                        Session::forget('user_data');
                        Session::regenerate();
                    }
                }
            }
            
            // For POST requests, ensure clean session state
            if ($request->isMethod('POST') && $request->routeIs('login')) {
                // Don't interfere with login process, just ensure session is ready
                if (!Session::isStarted()) {
                    Session::start();
                }
            }
        }
        
        $response = $next($request);
        
        // Force session save after login
        if ($request->isMethod('POST') && $request->routeIs('login')) {
            try {
                Session::save();
            } catch (\Exception $e) {
                \Log::error('SessionManagementMiddleware: Error saving session', [
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        return $response;
    }
}
