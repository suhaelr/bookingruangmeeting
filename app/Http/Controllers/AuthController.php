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
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'cf-turnstile-response' => 'required|string',
        ]);

        $credentials = $request->only(['username', 'password']);

        // Verify Cloudflare Turnstile
        $turnstileResponse = $request->input('cf-turnstile-response');
        if (!$this->verifyTurnstile($turnstileResponse, $request->ip())) {
            return back()->withErrors(['cf-turnstile-response' => 'Verifikasi keamanan gagal. Silakan coba lagi.'])->withInput();
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
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        $clientId = env('GOOGLE_CLIENT_ID');
        // Use original callback route that matches Google Cloud Console
        $redirectUri = env('GOOGLE_REDIRECT_URI', 'https://www.pusdatinbgn.web.id/auth/google/callback');
        $state = bin2hex(random_bytes(16));
        
        // Store state in session for CSRF protection
        session(['google_oauth_state' => $state]);
        
        // Define scopes with proper justification
        $scopes = [
            'openid',
            'email',
            'profile'
        ];
        
        $authUrl = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'scope' => implode(' ', $scopes),
            'response_type' => 'code',
            'state' => $state,
            'access_type' => 'offline', // Request refresh token
            'prompt' => 'consent', // Force consent screen for new scopes
            'include_granted_scopes' => 'true' // Include previously granted scopes
        ]);
        
        return redirect($authUrl);
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback(Request $request)
    {
        // Log all request data for debugging
        \Log::info('Google OAuth callback accessed', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->url(),
            'method' => $request->method(),
            'all_params' => $request->all(),
            'headers' => $request->headers->all(),
            'session_id' => session()->getId()
        ]);

        // Check if this is a direct access (no OAuth parameters)
        if (!$request->has('code') && !$request->has('error')) {
            \Log::warning('OAuth callback accessed without OAuth parameters');
            return view('auth.oauth-callback-debug');
        }

        // Aggressive Cloudflare bypass headers
        $request->headers->set('CF-Connecting-IP', $request->ip());
        $request->headers->set('X-Forwarded-For', $request->ip());
        $request->headers->set('X-Real-IP', $request->ip());
        $request->headers->set('CF-Ray', 'bypass-' . uniqid());
        $request->headers->set('CF-Visitor', '{"scheme":"https"}');
        $request->headers->set('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
        $request->headers->set('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8');
        $request->headers->set('Accept-Language', 'en-US,en;q=0.5');
        $request->headers->set('Accept-Encoding', 'gzip, deflate, br');
        $request->headers->set('DNT', '1');
        $request->headers->set('Connection', 'keep-alive');
        $request->headers->set('Upgrade-Insecure-Requests', '1');

        // Check for OAuth errors
        if ($request->has('error')) {
            $error = $request->get('error');
            $errorDescription = $request->get('error_description', 'Unknown error');
            
            \Log::error('Google OAuth error', [
                'error' => $error,
                'error_description' => $errorDescription
            ]);
            
            return redirect()->route('login')->with('error', 'OAuth error: ' . $errorDescription);
        }

        // Verify state parameter for CSRF protection
        $state = $request->get('state');
        if (!$state || $state !== session('google_oauth_state')) {
            return redirect()->route('login')->with('error', 'Invalid OAuth state parameter.');
        }

        $code = $request->get('code');
        if (!$code) {
            return redirect()->route('login')->with('error', 'Authorization code not received from Google.');
        }

        try {
            // Exchange authorization code for access token
            $tokenResponse = $this->getGoogleAccessToken($code);
            
            if (!$tokenResponse || !isset($tokenResponse['access_token'])) {
                return redirect()->route('login')->with('error', 'Failed to obtain access token from Google.');
            }

            // Check if required scopes were granted
            $grantedScopes = explode(' ', $tokenResponse['scope'] ?? '');
            $requiredScopes = ['openid', 'email', 'profile'];
            $missingScopes = array_diff($requiredScopes, $grantedScopes);
            
            if (!empty($missingScopes)) {
                \Log::warning('Missing required scopes', [
                    'missing_scopes' => $missingScopes,
                    'granted_scopes' => $grantedScopes
                ]);
                return redirect()->route('login')->with('error', 'Required permissions were not granted. Please try again and grant all requested permissions.');
            }

            // Get user information from Google
            $userInfo = $this->getGoogleUserInfo($tokenResponse['access_token']);
            
            if (!$userInfo || !isset($userInfo['email'])) {
                return redirect()->route('login')->with('error', 'Failed to retrieve user information from Google.');
            }

            // Check if user exists in database
            $user = User::where('email', $userInfo['email'])->first();
            
            if (!$user) {
                // Create new user
                $user = User::create([
                    'username' => $userInfo['email'], // Use email as username
                    'name' => $userInfo['name'] ?? $userInfo['given_name'] . ' ' . $userInfo['family_name'],
                    'full_name' => $userInfo['name'] ?? $userInfo['given_name'] . ' ' . $userInfo['family_name'],
                    'email' => $userInfo['email'],
                    'password' => Hash::make(Str::random(32)), // Random password for OAuth users
                    'role' => 'user',
                    'email_verified_at' => now(), // Google users are pre-verified
                    'google_id' => $userInfo['id'] ?? null,
                ]);
            } else {
                // Update existing user with Google ID if not set
                if (!$user->google_id) {
                    $user->update(['google_id' => $userInfo['id'] ?? null]);
                }
            }

            // Store refresh token for future use (if provided)
            if (isset($tokenResponse['refresh_token'])) {
                // Store refresh token securely (you might want to encrypt this)
                session(['google_refresh_token' => $tokenResponse['refresh_token']]);
                \Log::info('Refresh token stored for user', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            }

            // Log user in
            Session::put('user_logged_in', true);
            $userData = [
                'id' => $user->id,
                'username' => $user->username,
                'full_name' => $user->full_name ?? $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'department' => $user->department
            ];
            Session::put('user_data', $userData);

            // Update last login
            $user->update(['last_login_at' => now()]);

            \Log::info('Google OAuth login successful', [
                'user_id' => $user->id,
                'email' => $user->email,
                'google_id' => $userInfo['id'] ?? null,
                'role' => $user->role,
                'user_data' => $userData,
                'session_id' => session()->getId()
            ]);

            // Force session to be saved before redirect
            Session::save();

            // Verify session data after save
            \Log::info('Session verification after save', [
                'user_logged_in' => Session::get('user_logged_in'),
                'user_data' => Session::get('user_data'),
                'session_id' => session()->getId()
            ]);

            // Redirect based on user role
            if ($user->role === 'admin') {
                \Log::info('Redirecting to admin dashboard');
                $redirectUrl = route('admin.dashboard');
            } else {
                \Log::info('Redirecting to user dashboard');
                $redirectUrl = route('user.dashboard');
            }

            // Use JavaScript redirect to completely bypass Cloudflare
            \Log::info('Using JavaScript redirect to bypass Cloudflare completely', [
                'redirect_url' => $redirectUrl,
                'user_role' => $user->role,
                'user_email' => $user->email
            ]);
            
            // Create HTML page with immediate JavaScript redirect
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <meta http-equiv="refresh" content="0;url=' . $redirectUrl . '">
                <title>Redirecting...</title>
                <style>
                    body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
                    .spinner { border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; width: 40px; height: 40px; animation: spin 2s linear infinite; margin: 20px auto; }
                    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
                </style>
            </head>
            <body>
                <h2>Login Berhasil!</h2>
                <div class="spinner"></div>
                <p>Mengarahkan ke dashboard...</p>
                <p>Jika tidak otomatis redirect, <a href="' . $redirectUrl . '">klik di sini</a></p>
                <script>
                    console.log("OAuth redirect for user: ' . $user->email . '");
                    console.log("Redirect URL: ' . $redirectUrl . '");
                    
                    // Immediate redirect
                    window.location.href = "' . $redirectUrl . '";
                    
                    // Fallback redirects
                    setTimeout(() => {
                        if (window.location.href !== "' . $redirectUrl . '") {
                            console.log("Fallback redirect 1");
                            window.location.replace("' . $redirectUrl . '");
                        }
                    }, 500);
                    
                    setTimeout(() => {
                        if (window.location.href !== "' . $redirectUrl . '") {
                            console.log("Fallback redirect 2");
                            document.location.href = "' . $redirectUrl . '";
                        }
                    }, 1000);
                    
                    setTimeout(() => {
                        if (window.location.href !== "' . $redirectUrl . '") {
                            console.log("Final fallback redirect");
                            window.location = "' . $redirectUrl . '";
                        }
                    }, 2000);
                </script>
            </body>
            </html>';
            
            return response($html, 200, [
                'Content-Type' => 'text/html; charset=UTF-8',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
                'X-Frame-Options' => 'DENY',
                'X-Content-Type-Options' => 'nosniff'
            ]);

        } catch (\Exception $e) {
            \Log::error('Google OAuth error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('login')->with('error', 'Gagal login dengan Google: ' . $e->getMessage());
        }
    }

    /**
     * Exchange authorization code for access token
     */
    private function getGoogleAccessToken($code)
    {
        $clientId = env('GOOGLE_CLIENT_ID');
        $clientSecret = env('GOOGLE_CLIENT_SECRET');
        $redirectUri = env('GOOGLE_REDIRECT_URI', 'https://www.pusdatinbgn.web.id/auth/google/callback');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $redirectUri,
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            throw new \Exception('Failed to obtain access token from Google');
        }

        return json_decode($response, true);
    }

    /**
     * Get user information from Google
     */
    private function getGoogleUserInfo($accessToken)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . $accessToken);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            throw new \Exception('Failed to retrieve user information from Google');
        }

        return json_decode($response, true);
    }

    /**
     * Handle refresh token revocation
     */
    public function revokeGoogleToken()
    {
        $refreshToken = session('google_refresh_token');
        
        if (!$refreshToken) {
            return response()->json(['error' => 'No refresh token found'], 400);
        }

        $clientId = env('GOOGLE_CLIENT_ID');
        $clientSecret = env('GOOGLE_CLIENT_SECRET');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/revoke');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'token' => $refreshToken,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            session()->forget('google_refresh_token');
            \Log::info('Google refresh token revoked successfully');
            return response()->json(['success' => true]);
        }

        \Log::error('Failed to revoke Google refresh token', [
            'http_code' => $httpCode,
            'response' => $response
        ]);

        return response()->json(['error' => 'Failed to revoke token'], 500);
    }

    /**
     * Refresh Google access token using refresh token
     */
    private function refreshGoogleToken($refreshToken)
    {
        $clientId = env('GOOGLE_CLIENT_ID');
        $clientSecret = env('GOOGLE_CLIENT_SECRET');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token',
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            \Log::error('Failed to refresh Google token', [
                'http_code' => $httpCode,
                'response' => $response
            ]);
            return false;
        }

        $result = json_decode($response, true);
        
        if (!$result || !isset($result['access_token'])) {
            \Log::error('Invalid refresh token response', [
                'response' => $response
            ]);
            return false;
        }

        // Update refresh token if provided
        if (isset($result['refresh_token'])) {
            session(['google_refresh_token' => $result['refresh_token']]);
        }

        return $result;
    }

    /**
     * Update user role (Admin only)
     */
    public function updateUserRole(Request $request, $userId)
    {
        // Check if current user is admin
        $currentUser = session('user_data');
        if (!$currentUser || $currentUser['role'] !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'role' => 'required|in:admin,user'
        ]);

        try {
            $user = User::findOrFail($userId);
            
            // Prevent admin from changing their own role
            if ($user->id == $currentUser['id']) {
                return response()->json(['error' => 'Cannot change your own role'], 400);
            }

            $oldRole = $user->role;
            $user->update(['role' => $request->role]);

            \Log::info('User role updated by admin', [
                'admin_id' => $currentUser['id'],
                'admin_email' => $currentUser['email'],
                'target_user_id' => $user->id,
                'target_user_email' => $user->email,
                'old_role' => $oldRole,
                'new_role' => $request->role
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User role updated successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->full_name ?? $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'google_id' => $user->google_id
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to update user role', [
                'admin_id' => $currentUser['id'],
                'target_user_id' => $userId,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to update user role'], 500);
        }
    }

    /**
     * Get all users for admin management
     */
    public function getAllUsers()
    {
        // Check if current user is admin
        $currentUser = session('user_data');
        if (!$currentUser || $currentUser['role'] !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $users = User::select('id', 'username', 'name', 'full_name', 'email', 'role', 'google_id', 'created_at', 'last_login_at')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'username' => $user->username,
                        'name' => $user->full_name ?? $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'google_id' => $user->google_id ? 'Yes' : 'No',
                        'created_at' => $user->created_at->format('d/m/Y H:i'),
                        'last_login_at' => $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Never'
                    ];
                });

            return response()->json([
                'success' => true,
                'users' => $users
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to get users list', [
                'admin_id' => $currentUser['id'],
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to get users list'], 500);
        }
    }

    /**
     * Verify Cloudflare Turnstile token
     */
    private function verifyTurnstile($token, $remoteIp = null)
    {
        $secretKey = env('CLOUDFLARE_SECRET_KEY');
        
        if (!$secretKey) {
            \Log::error('Cloudflare secret key not configured');
            return false;
        }

        $data = [
            'secret' => $secretKey,
            'response' => $token,
            'remoteip' => $remoteIp
        ];

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

        if ($httpCode !== 200) {
            \Log::error('Turnstile verification failed', [
                'http_code' => $httpCode,
                'response' => $response
            ]);
            return false;
        }

        $result = json_decode($response, true);
        
        \Log::info('Turnstile verification result', [
            'success' => $result['success'] ?? false,
            'error_codes' => $result['error-codes'] ?? []
        ]);

        return $result['success'] ?? false;
    }






}
