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
        \Log::info('AdminAuth middleware called', [
            'url' => $request->url(),
            'method' => $request->method(),
            'session_id' => session()->getId(),
            'user_logged_in' => Session::has('user_logged_in') ? Session::get('user_logged_in') : false,
            'is_api_request' => $request->is('admin/users/api'),
            'is_ajax' => $request->ajax(),
            'wants_json' => $request->wantsJson()
        ]);

        if (!Session::has('user_logged_in') || !Session::get('user_logged_in')) {
            \Log::warning('AdminAuth: User not logged in', [
                'url' => $request->url(),
                'session_id' => session()->getId()
            ]);
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu!');
        }

        $user = Session::get('user_data');
        \Log::info('AdminAuth: User data', [
            'user_id' => $user['id'] ?? 'unknown',
            'user_role' => $user['role'] ?? 'unknown',
            'user_email' => $user['email'] ?? 'unknown'
        ]);

        // Validate user exists in database
        try {
            $userModel = \App\Models\User::find($user['id']);
            if (!$userModel) {
                \Log::warning('AdminAuth: User not found in database', [
                    'session_user_id' => $user['id'],
                    'session_user_data' => $user
                ]);
                Session::forget('user_logged_in');
                Session::forget('user_data');
                return redirect()->route('login')->with('error', 'Session tidak valid. Silakan login kembali!');
            }
        } catch (\Exception $e) {
            \Log::error('AdminAuth: Database error during user validation', [
                'error' => $e->getMessage(),
                'session_user_id' => $user['id'],
                'session_user_data' => $user
            ]);
            Session::forget('user_logged_in');
            Session::forget('user_data');
            return redirect()->route('login')->with('error', 'Terjadi kesalahan sistem. Silakan login kembali!');
        }

        if ($user['role'] !== 'admin') {
            \Log::warning('AdminAuth: User is not admin', [
                'user_role' => $user['role'],
                'url' => $request->url()
            ]);
            return redirect()->route('user.dashboard')->with('error', 'Akses ditolak!');
        }

        \Log::info('AdminAuth: Access granted', [
            'user_id' => $user['id'],
            'url' => $request->url()
        ]);

        return $next($request);
    }
}
