<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Mail\WelcomeEmail;
use App\Mail\PasswordResetEmail;

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

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
        ]);

        try {
            $user = User::create([
                'username' => $request->username,
                'name' => $request->full_name,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'department' => $request->department,
                'role' => 'user',
                'email_verified_at' => now(),
            ]);

            // Send welcome email
            Mail::to($user->email)->send(new WelcomeEmail($user));

            \Log::info('User registered successfully', [
                'user_id' => $user->id,
                'username' => $user->username,
                'email' => $user->email
            ]);

            return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login dengan akun Anda.');
        } catch (\Exception $e) {
            \Log::error('Registration error', [
                'message' => $e->getMessage(),
                'input' => $request->all()
            ]);
            return back()->with('error', 'Gagal mendaftar: ' . $e->getMessage())->withInput();
        }
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        try {
            $user = User::where('email', $request->email)->first();
            $token = Str::random(64);

            // Store token in database
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                [
                    'email' => $request->email,
                    'token' => Hash::make($token),
                    'created_at' => now()
                ]
            );

            // Send reset email
            Mail::to($user->email)->send(new PasswordResetEmail($user, $token));

            \Log::info('Password reset link sent', [
                'email' => $request->email,
                'user_id' => $user->id
            ]);

            return back()->with('success', 'Link reset password telah dikirim ke email Anda!');
        } catch (\Exception $e) {
            \Log::error('Password reset error', [
                'message' => $e->getMessage(),
                'email' => $request->email
            ]);
            return back()->with('error', 'Gagal mengirim email reset password: ' . $e->getMessage());
        }
    }

    public function showResetPassword($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed'
        ]);

        try {
            $resetToken = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->first();

            if (!$resetToken || !Hash::check($request->token, $resetToken->token)) {
                return back()->with('error', 'Token reset password tidak valid atau sudah kadaluarsa.');
            }

            // Check if token is not older than 1 hour
            if (now()->diffInMinutes($resetToken->created_at) > 60) {
                DB::table('password_reset_tokens')->where('email', $request->email)->delete();
                return back()->with('error', 'Token reset password sudah kadaluarsa. Silakan request ulang.');
            }

            // Update password
            $user = User::where('email', $request->email)->first();
            $user->update(['password' => Hash::make($request->password)]);

            // Delete token
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            \Log::info('Password reset successfully', [
                'user_id' => $user->id,
                'email' => $request->email
            ]);

            return redirect()->route('login')->with('success', 'Password berhasil direset! Silakan login dengan password baru.');
        } catch (\Exception $e) {
            \Log::error('Password reset error', [
                'message' => $e->getMessage(),
                'email' => $request->email
            ]);
            return back()->with('error', 'Gagal reset password: ' . $e->getMessage());
        }
    }
}
