<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminAuth
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
        if (!Session::has('user_logged_in') || !Session::get('user_logged_in')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu!');
        }

        $user = Session::get('user_data');
        if ($user['role'] !== 'admin') {
            return redirect()->route('user.dashboard')->with('error', 'Akses ditolak!');
        }

        return $next($request);
    }
}
