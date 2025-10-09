<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Jika sudah login, redirect ke dashboard sesuai role
        if (Session::has('user_logged_in')) {
            $user = Session::get('user_data');
            return $user['role'] === 'admin' 
                ? redirect()->route('admin.dashboard')
                : redirect()->route('user.dashboard');
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Check hardcoded credentials first
        if ($credentials['username'] === 'admin' && $credentials['password'] === 'admin') {
            Session::put('user_logged_in', true);
            Session::put('user_data', [
                'id' => 1,
                'username' => 'admin',
                'full_name' => 'Super Administrator',
                'email' => 'admin@jadixpert.com',
                'role' => 'admin',
                'department' => 'IT'
            ]);
            
            return redirect()->route('admin.dashboard')->with('success', 'Login berhasil!');
        }

        if ($credentials['username'] === 'user' && $credentials['password'] === 'user') {
            Session::put('user_logged_in', true);
            Session::put('user_data', [
                'id' => 2,
                'username' => 'user',
                'full_name' => 'Regular User',
                'email' => 'user@jadixpert.com',
                'role' => 'user',
                'department' => 'General'
            ]);
            
            return redirect()->route('user.dashboard')->with('success', 'Login berhasil!');
        }

        // Check database users
        $user = User::where('username', $credentials['username'])->first();
        
        if ($user && Hash::check($credentials['password'], $user->password)) {
            Session::put('user_logged_in', true);
            Session::put('user_data', [
                'id' => $user->id,
                'username' => $user->username,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'role' => $user->role,
                'department' => $user->department
            ]);

            // Update last login
            $user->update(['last_login_at' => now()]);

            return $user->role === 'admin' 
                ? redirect()->route('admin.dashboard')->with('success', 'Login berhasil!')
                : redirect()->route('user.dashboard')->with('success', 'Login berhasil!');
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->withInput($request->only('username'));
    }

    public function logout()
    {
        Session::flush();
        return redirect()->route('login')->with('success', 'Logout berhasil!');
    }
}
