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
use App\Mail\EmailVerificationMail;

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
            'cf-turnstile-response' => 'required|string',
        ]);

        // Verify Cloudflare Turnstile
        $turnstileResponse = $this->verifyTurnstile($request->input('cf-turnstile-response'), $request->ip());
        if (!$turnstileResponse) {
            return back()->withErrors([
                'cf-turnstile-response' => 'Verifikasi keamanan gagal. Silakan coba lagi.',
            ])->withInput($request->only('username'));
        }

        // Check hardcoded credentials first
        if ($credentials['username'] === 'admin' && $credentials['password'] === 'admin') {
            Session::put('user_logged_in', true);
            Session::put('user_data', [
                'id' => 1,
                'username' => 'admin',
                'full_name' => 'Super Administrator',
                'email' => 'admin@pusdatinbgn.web.id',
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
                'email' => 'user@pusdatinbgn.web.id',
                'role' => 'user',
                'department' => 'General'
            ]);
            
            return redirect()->route('user.dashboard')->with('success', 'Login berhasil!');
        }

        // Check database users - support both username and email
        $user = User::where('username', $credentials['username'])
                   ->orWhere('email', $credentials['username'])
                   ->first();
        
        if ($user && Hash::check($credentials['password'], $user->password)) {
            // Check if email is verified
            if (!$user->email_verified_at) {
                return back()->withErrors([
                    'username' => 'Email belum diverifikasi. Silakan cek email Anda dan klik link verifikasi.',
                ])->withInput($request->only('username'));
            }

            Session::put('user_logged_in', true);
            Session::put('user_data', [
                'id' => $user->id,
                'username' => $user->username,
                'full_name' => $user->full_name ?? $user->name,
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
            'username' => 'Username/email atau password salah.',
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
            'username' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
        ]);

        try {
            $verificationToken = Str::random(64);
            
            $user = User::create([
                'username' => $request->username,
                'name' => $request->full_name,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'department' => $request->department,
                'role' => 'user',
                'email_verified_at' => null, // Not verified yet
                'email_verification_token' => Hash::make($verificationToken),
            ]);

            // Send verification email
            $verificationUrl = route('email.verify', ['token' => $verificationToken]);
            Mail::to($user->email)->send(new EmailVerificationMail($user, $verificationUrl));

            \Log::info('User registered successfully, verification email sent', [
                'user_id' => $user->id,
                'username' => $user->username,
                'email' => $user->email
            ]);

            return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan cek email Anda untuk verifikasi akun.');
        } catch (\Exception $e) {
            \Log::error('Registration error', [
                'message' => $e->getMessage(),
                'input' => $request->all(),
                'trace' => $e->getTraceAsString()
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

            // Update password and ensure email is verified (no need to send verification email)
            $user = User::where('email', $request->email)->first();
            $user->update([
                'password' => Hash::make($request->password),
                'email_verified_at' => now() // Mark as verified after password reset
            ]);

            // Delete token
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            \Log::info('Password reset successfully', [
                'user_id' => $user->id,
                'email' => $request->email
            ]);

            return redirect()->route('login')->with('success', 'Password berhasil direset! Silakan login dengan username/email dan password baru.');
        } catch (\Exception $e) {
            \Log::error('Password reset error', [
                'message' => $e->getMessage(),
                'email' => $request->email
            ]);
            return back()->with('error', 'Gagal reset password: ' . $e->getMessage());
        }
    }

    public function verifyEmail($token)
    {
        try {
            $user = User::whereNotNull('email_verification_token')
                ->get()
                ->first(function ($user) use ($token) {
                    return Hash::check($token, $user->email_verification_token);
                });

            if (!$user) {
                return redirect()->route('login')->with('error', 'Token verifikasi tidak valid atau sudah kadaluarsa.');
            }

            // Check if token is not older than 24 hours
            if ($user->created_at->diffInHours(now()) > 24) {
                $user->delete(); // Delete unverified user
                return redirect()->route('login')->with('error', 'Token verifikasi sudah kadaluarsa. Silakan daftar ulang.');
            }

            // Verify email
            $user->update([
                'email_verified_at' => now(),
                'email_verification_token' => null
            ]);

            \Log::info('Email verified successfully', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return redirect()->route('login')->with('success', 'Email berhasil diverifikasi! Silakan login dengan akun Anda.');
        } catch (\Exception $e) {
            \Log::error('Email verification error', [
                'message' => $e->getMessage(),
                'token' => $token,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('login')->with('error', 'Gagal verifikasi email: ' . $e->getMessage());
        }
    }

    public function resendVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        try {
            $user = User::where('email', $request->email)
                ->whereNull('email_verified_at')
                ->first();

            if (!$user) {
                return back()->with('error', 'Email sudah diverifikasi atau tidak ditemukan.');
            }

            // Generate new token
            $verificationToken = Str::random(64);
            $user->update(['email_verification_token' => Hash::make($verificationToken)]);

            // Send verification email
            $verificationUrl = route('email.verify', ['token' => $verificationToken]);
            Mail::to($user->email)->send(new EmailVerificationMail($user, $verificationUrl));

            \Log::info('Verification email resent', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return back()->with('success', 'Email verifikasi telah dikirim ulang!');
        } catch (\Exception $e) {
            \Log::error('Resend verification error', [
                'message' => $e->getMessage(),
                'email' => $request->email,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Gagal mengirim ulang email verifikasi: ' . $e->getMessage());
        }
    }

    /**
     * Verify Cloudflare Turnstile response
     */
    private function verifyTurnstile($token, $remoteIp = null)
    {
        $secretKey = '0x4AAAAAAB56ljRNTob9cGtXsqh8c-ZuxxE';
        
        $data = [
            'secret' => $secretKey,
            'response' => $token,
        ];
        
        if ($remoteIp) {
            $data['remoteip'] = $remoteIp;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://challenges.cloudflare.com/turnstile/v0/siteverify');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            \Log::error('Turnstile verification failed', [
                'http_code' => $httpCode,
                'response' => $response,
                'token' => $token
            ]);
            return false;
        }

        $result = json_decode($response, true);
        
        if (!$result || !isset($result['success'])) {
            \Log::error('Invalid Turnstile response', [
                'response' => $response,
                'token' => $token
            ]);
            return false;
        }

        if (!$result['success']) {
            \Log::warning('Turnstile verification failed', [
                'result' => $result,
                'token' => $token
            ]);
            return false;
        }

        \Log::info('Turnstile verification successful', [
            'token' => $token,
            'remote_ip' => $remoteIp
        ]);

        return true;
    }

}
