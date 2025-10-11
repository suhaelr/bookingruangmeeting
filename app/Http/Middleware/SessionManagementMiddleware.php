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
        // Check if session is valid
        if (Session::has('user_logged_in') && !Session::has('user_data')) {
            // Clear invalid session
            Session::flush();
            Session::regenerate();
        }
        
        // Check if user_data is corrupted
        if (Session::has('user_data')) {
            $userData = Session::get('user_data');
            if (!is_array($userData) || !isset($userData['id']) || !isset($userData['role'])) {
                // Clear corrupted session data
                Session::flush();
                Session::regenerate();
            }
        }
        
        // Ensure session is started
        if (!Session::isStarted()) {
            Session::start();
        }
        
        $response = $next($request);
        
        // Force session save for important operations
        if ($request->isMethod('POST') && $request->routeIs('login')) {
            Session::save();
        }
        
        return $response;
    }
}
