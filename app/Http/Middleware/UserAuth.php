<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UserAuth
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
        // Add Cloudflare bypass headers
        $request->headers->set('CF-Connecting-IP', $request->ip());
        $request->headers->set('X-Forwarded-For', $request->ip());
        $request->headers->set('X-Real-IP', $request->ip());
        
        \Log::info('UserAuth middleware check with Cloudflare bypass', [
            'url' => $request->url(),
            'session_id' => session()->getId(),
            'user_logged_in' => Session::get('user_logged_in'),
            'user_data' => Session::get('user_data'),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        if (!Session::has('user_logged_in') || !Session::get('user_logged_in')) {
            \Log::warning('UserAuth: User not logged in, redirecting to login');
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu!');
        }

        $user = Session::get('user_data');
        if (!$user || !isset($user['role'])) {
            \Log::warning('UserAuth: No user data or role found', [
                'user_data' => $user
            ]);
            return redirect()->route('login')->with('error', 'Data pengguna tidak ditemukan!');
        }

        if ($user['role'] !== 'user') {
            \Log::info('UserAuth: User role is not user, redirecting to admin dashboard', [
                'user_role' => $user['role']
            ]);
            return redirect()->route('admin.dashboard')->with('error', 'Akses ditolak!');
        }

        \Log::info('UserAuth: Access granted to user dashboard');
        return $next($request);
    }
}
