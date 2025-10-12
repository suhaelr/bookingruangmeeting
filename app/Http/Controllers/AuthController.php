<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\User;
use App\Mail\WelcomeEmail;
use App\Mail\PasswordResetEmail;
use App\Mail\EmailVerificationMail;

class AuthController extends Controller
{
    /**
     * Add no-cache headers to response
     */
    private function addNoCacheHeaders($response)
    {
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
        $response->headers->set('ETag', '');
        $response->headers->set('Vary', '*');
        $response->headers->set('X-Accel-Expires', '0');
        $response->headers->set('X-Cache', 'DISABLED');
        $response->headers->set('X-Cache-Lookup', 'DISABLED');
        return $response;
    }
    public function showLogin()
    {
        // Jika sudah login, redirect ke dashboard sesuai role
        if (Session::has('user_logged_in') && Session::has('user_data')) {
            $user = Session::get('user_data');
            if (is_array($user) && isset($user['role'])) {
                return $user['role'] === 'admin' 
                    ? redirect()->route('admin.dashboard')
                    : redirect()->route('user.dashboard');
            }
        }
        
        // Clear any invalid session data
        if (Session::has('user_logged_in') && !Session::has('user_data')) {
            Session::forget('user_logged_in');
            Session::forget('user_data');
            Session::regenerate();
        }
        
        $response = response()->view('auth.login');
        return $this->addNoCacheHeaders($response);
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['username', 'password']);

        // Check hardcoded credentials first
        if ($credentials['username'] === 'admin' && $credentials['password'] === 'admin') {
            // Clear only user-related session data, not all session
            Session::forget('user_logged_in');
            Session::forget('user_data');
            
            // Regenerate session ID for security
            Session::regenerate();
            
            // Get fresh user data from database to ensure latest role
            $user = User::where('username', 'admin')->first();
            if ($user) {
                // Update last login
                $user->update(['last_login_at' => now()]);
                
                Session::put('user_logged_in', true);
                Session::put('user_data', [
                    'id' => $user->id,
                    'username' => $user->username,
                    'full_name' => $user->full_name ?? $user->name,
                    'email' => $user->email,
                    'role' => $user->role, // Get latest role from database
                    'department' => $user->department ?? 'IT'
                ]);
            } else {
                // Fallback for hardcoded admin
                Session::put('user_logged_in', true);
                Session::put('user_data', [
                    'id' => 1,
                    'username' => 'admin',
                    'full_name' => 'Super Administrator',
                    'email' => 'admin@pusdatinbgn.web.id',
                    'role' => 'admin',
                    'department' => 'IT'
                ]);
            }
            
            // Force session regeneration and save
            Session::regenerate(true);
            Session::save();
            
            // Add delay to ensure session is saved
            usleep(200000); // 200ms delay
            
            // Redirect based on current role
            $userData = Session::get('user_data');
            $redirectRoute = $userData['role'] === 'admin' 
                ? redirect()->route('admin.dashboard')
                : redirect()->route('user.dashboard');
            
            $response = $redirectRoute->with('success', 'Login berhasil!');
            
            // Add no-cache headers
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, private, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
            $response->headers->set('ETag', '');
            
            return $response;
        }

        // Remove hardcoded user credentials for security

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

            \Log::info('User login attempt', [
                'user_id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'current_role' => $user->role,
                'session_id' => session()->getId()
            ]);

            // Clear only user-related session data, not all session
            Session::forget('user_logged_in');
            Session::forget('user_data');
            
            // Regenerate session ID for security
            Session::regenerate();

            // Get fresh user data from database to ensure latest role
            $freshUser = User::find($user->id);
            \Log::info('Fresh user data from database', [
                'user_id' => $freshUser->id,
                'username' => $freshUser->username,
                'email' => $freshUser->email,
                'role' => $freshUser->role,
                'last_login_at' => $freshUser->last_login_at
            ]);

            Session::put('user_logged_in', true);
            Session::put('user_data', [
                'id' => $freshUser->id,
                'username' => $freshUser->username,
                'full_name' => $freshUser->full_name ?? $freshUser->name,
                'email' => $freshUser->email,
                'role' => $freshUser->role, // Use fresh role from database
                'department' => $freshUser->department
            ]);

            // Force session regeneration and save
            Session::regenerate(true);
            Session::save();
            
            // Add delay to ensure session is saved
            usleep(200000); // 200ms delay

            // Update last login
            $freshUser->update(['last_login_at' => now()]);

            \Log::info('Session data set', [
                'session_user_data' => Session::get('user_data'),
                'session_id' => session()->getId()
            ]);

            $response = $freshUser->role === 'admin' 
                ? redirect()->route('admin.dashboard')->with('success', 'Login berhasil!')
                : redirect()->route('user.dashboard')->with('success', 'Login berhasil!');
            
            // Add no-cache headers
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, private, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
            $response->headers->set('ETag', '');
            
            return $response;
        }

        return back()->withErrors([
            'username' => 'Username/email atau password salah.',
        ])->withInput($request->only('username'));
    }

    public function logout()
    {
        // Clear only user-related session data, preserve other session data
        Session::forget('user_logged_in');
        Session::forget('user_data');
        
        // Regenerate session ID to prevent session fixation
        Session::regenerate();
        
        $response = redirect()->route('login')->with('success', 'Logout berhasil!');
        
        // Add no-cache headers to prevent browser caching
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        
        return $response;
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
        // Try config first, fallback to env() if config fails
        $clientId = config('services.google.client_id') ?: env('GOOGLE_CLIENT_ID');
        $clientSecret = config('services.google.client_secret') ?: env('GOOGLE_CLIENT_SECRET');
        $redirectUri = config('services.google.redirect') ?: env('GOOGLE_REDIRECT_URI', 'https://www.pusdatinbgn.web.id/auth/google/callback');
        
        // Debug: Check if configuration is loaded
        \Log::info('Google OAuth Configuration Check', [
            'client_id' => $clientId ? substr($clientId, 0, 20) . '...' : 'NULL',
            'client_secret' => $clientSecret ? 'SET' : 'NULL',
            'redirect_uri' => $redirectUri ?: 'NULL',
            'env_client_id' => env('GOOGLE_CLIENT_ID') ? substr(env('GOOGLE_CLIENT_ID'), 0, 20) . '...' : 'NULL',
            'config_cache_cleared' => true,
            'timestamp' => now()->toISOString()
        ]);
        
        // Validate configuration
        if (!$clientId || !$clientSecret || !$redirectUri) {
            \Log::error('Google OAuth configuration missing', [
                'client_id_set' => !empty($clientId),
                'client_secret_set' => !empty($clientSecret),
                'redirect_uri_set' => !empty($redirectUri)
            ]);
            return redirect()->route('login')->with('error', 'Google OAuth configuration error. Please contact administrator.');
        }
        
        $state = bin2hex(random_bytes(16));
        
        // Store state in session for CSRF protection
        session(['google_oauth_state' => $state]);
        
        // Define scopes with proper justification
        $scopes = [
            'openid',
            'email',
            'profile'
        ];
        
        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'scope' => implode(' ', $scopes),
            'response_type' => 'code',
            'state' => $state,
            'access_type' => 'offline', // Request refresh token
            'prompt' => 'consent', // Force consent screen for new scopes
            'include_granted_scopes' => 'true' // Include previously granted scopes
        ];
        
        $authUrl = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query($params);
        
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

            // Check if required scopes were granted (Google returns full scope URLs)
            $grantedScopes = explode(' ', $tokenResponse['scope'] ?? '');
            $requiredScopes = ['openid', 'email', 'profile'];
            $missingScopes = [];
            
            // Check for both short and full scope names
            foreach ($requiredScopes as $requiredScope) {
                $found = false;
                foreach ($grantedScopes as $grantedScope) {
                    if ($grantedScope === $requiredScope || 
                        str_contains($grantedScope, $requiredScope) ||
                        str_contains($grantedScope, 'userinfo.' . $requiredScope)) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $missingScopes[] = $requiredScope;
                }
            }
            
            if (!empty($missingScopes)) {
                \Log::warning('Missing required scopes', [
                    'missing_scopes' => $missingScopes,
                    'granted_scopes' => $grantedScopes
                ]);
                // Don't fail, just log the warning and continue
                \Log::info('Continuing with OAuth despite missing scopes');
            }

            // Get user information from Google
            $userInfo = $this->getGoogleUserInfo($tokenResponse['access_token']);
            
            if (!$userInfo || !isset($userInfo['email'])) {
                return redirect()->route('login')->with('error', 'Failed to retrieve user information from Google.');
            }

            // Check if user exists in database
            $user = User::where('email', $userInfo['email'])->first();
            
            if (!$user) {
                // Create new user with role 'user' (Google users are always regular users)
                $user = User::create([
                    'username' => $userInfo['email'], // Use email as username
                    'name' => $userInfo['name'] ?? $userInfo['given_name'] . ' ' . $userInfo['family_name'],
                    'full_name' => $userInfo['name'] ?? $userInfo['given_name'] . ' ' . $userInfo['family_name'],
                    'email' => $userInfo['email'],
                    'password' => Hash::make(Str::random(32)), // Random password for OAuth users
                    'role' => 'user', // Google users are always regular users
                    'email_verified_at' => now(), // Google users are pre-verified
                    'google_id' => $userInfo['id'] ?? null,
                ]);
            } else {
                // Update existing user with Google ID if not set
                if (!$user->google_id) {
                    $user->update(['google_id' => $userInfo['id'] ?? null]);
                }
                // DO NOT override role - respect admin's role assignment
                // Admin can change user's role and it should persist
                \Log::info('Existing Google user logged in', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'current_role' => $user->role,
                    'role_preserved' => true
                ]);
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

            // Prepare user data
            $userData = [
                'id' => $user->id,
                'username' => $user->username,
                'full_name' => $user->full_name ?? $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'department' => $user->department
            ];
            
            // Check if this is a mobile device
            $userAgent = $request->userAgent();
            $isMobile = $this->isMobileDevice($userAgent);
            
            \Log::info('Google OAuth session setup', [
                'user_id' => $user->id,
                'email' => $user->email,
                'is_mobile' => $isMobile,
                'user_agent' => $userAgent,
                'session_id_before' => session()->getId()
            ]);
            
            // Clear any existing session data first
            Session::forget('user_logged_in');
            Session::forget('user_data');
            Session::forget('google_oauth_state');
            
            // Regenerate session ID for security
            Session::regenerate(true);
            
            // Set new session data
            Session::put('user_logged_in', true);
            Session::put('user_data', $userData);
            
            // Update last login
            $user->update(['last_login_at' => now()]);
            
            // Force session save and wait for completion
            Session::save();
            
            // Add longer delay for mobile devices to ensure session persistence
            $delay = $isMobile ? 1000000 : 500000; // 1 second for mobile, 500ms for desktop
            usleep($delay);
            
            // Additional session verification for mobile
            if ($isMobile) {
                // Force another session save for mobile
                Session::save();
                usleep(200000); // Additional 200ms delay
                
                \Log::info('Mobile session double-save completed', [
                    'session_id' => session()->getId(),
                    'user_logged_in' => Session::get('user_logged_in'),
                    'has_user_data' => Session::has('user_data')
                ]);
            }
            
            // Verify session data is properly saved
            \Log::info('Google OAuth login successful - Final verification', [
                'user_id' => $user->id,
                'email' => $user->email,
                'google_id' => $userInfo['id'] ?? null,
                'role' => $user->role,
                'is_mobile' => $isMobile,
                'session_id' => session()->getId(),
                'user_logged_in' => Session::get('user_logged_in'),
                'user_data' => Session::get('user_data')
            ]);

            // Redirect based on role (admin goes to admin dashboard, user goes to user dashboard)
            $dashboardUrl = $user->role === 'admin' 
                ? url('/admin/dashboard') 
                : url('/user/dashboard');
            
            \Log::info('Redirecting to dashboard based on role', [
                'url' => $dashboardUrl,
                'user_id' => $user->id,
                'user_email' => $user->email,
                'role' => $user->role,
                'session_id' => session()->getId(),
                'user_logged_in' => Session::get('user_logged_in'),
                'user_data' => Session::get('user_data')
            ]);
            
            // Direct redirect based on role with absolute URL
            $response = redirect($dashboardUrl)->with('success', 'Berhasil masuk dengan Google!');
            return $this->addNoCacheHeaders($response);

        } catch (\Exception $e) {
            \Log::error('Google OAuth error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'session_id' => session()->getId(),
                'user_logged_in' => Session::get('user_logged_in'),
                'user_data' => Session::get('user_data')
            ]);
            
            // Clear any partial session data
            Session::forget('user_logged_in');
            Session::forget('user_data');
            Session::forget('google_oauth_state');
            
            return redirect()->route('login')->with('error', 'Gagal login dengan Google: ' . $e->getMessage());
        }
    }

    /**
     * Exchange authorization code for access token
     */
    private function getGoogleAccessToken($code)
    {
        // Try config first, fallback to env() if config fails
        $clientId = config('services.google.client_id') ?: env('GOOGLE_CLIENT_ID');
        $clientSecret = config('services.google.client_secret') ?: env('GOOGLE_CLIENT_SECRET');
        $redirectUri = config('services.google.redirect') ?: env('GOOGLE_REDIRECT_URI', 'https://www.pusdatinbgn.web.id/auth/google/callback');

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
        \Log::info('updateUserRole method called', [
            'timestamp' => now(),
            'session_id' => session()->getId(),
            'user_id' => $userId,
            'request_data' => $request->all(),
            'csrf_token' => $request->input('_token'),
            'session_token' => session()->token()
        ]);

        // Check if current user is admin
        $currentUser = session('user_data');
        if (!$currentUser || $currentUser['role'] !== 'admin') {
            \Log::warning('Unauthorized access to updateUserRole', [
                'current_user' => $currentUser,
                'session_id' => session()->getId()
            ]);
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'role' => 'required|in:admin,user'
        ]);

        try {
            $user = User::findOrFail($userId);
            
            \Log::info('Role update attempt', [
                'admin_id' => $currentUser['id'],
                'target_user_id' => $userId,
                'target_user_email' => $user->email,
                'current_role' => $user->role,
                'new_role' => $request->role,
                'request_data' => $request->all()
            ]);
            
            // Allow admin to change their own role (with session update)
            $oldRole = $user->role;
            
            // Clear any cached data for this user
            \Cache::forget("user_{$userId}");
            \Cache::forget("user_role_{$userId}");
            
            // Use direct database update to ensure persistence
            $updateResult = DB::table('users')
                ->where('id', $userId)
                ->update([
                    'role' => $request->role,
                    'updated_at' => now()
                ]);
            
            \Log::info('Direct database update result', [
                'user_id' => $userId,
                'update_result' => $updateResult,
                'new_role' => $request->role
            ]);
            
            // Clear all cache to ensure fresh data
            \Cache::flush();
            
            // Refresh user data to ensure update
            $user = User::find($userId);
            
            \Log::info('Role update completed', [
                'user_id' => $user->id,
                'old_role' => $oldRole,
                'new_role' => $user->role,
                'updated_successfully' => $user->role === $request->role
            ]);
            
            // Double-check database after update
            $userFromDB = User::find($user->id);
            \Log::info('Database verification after update', [
                'user_id' => $userFromDB->id,
                'role_in_db' => $userFromDB->role,
                'role_in_model' => $user->role
            ]);

            // If the user being updated is the current logged-in user, update their session
            if ($user->id == $currentUser['id']) {
                $updatedUserData = $currentUser;
                $updatedUserData['role'] = $request->role;
                Session::put('user_data', $updatedUserData);
                Session::save();
                
                \Log::info('Session updated for current user', [
                    'user_id' => $user->id,
                    'old_role' => $oldRole,
                    'new_role' => $request->role,
                    'session_data' => Session::get('user_data')
                ]);
            }

            \Log::info('User role updated by admin', [
                'admin_id' => $currentUser['id'],
                'admin_email' => $currentUser['email'],
                'target_user_id' => $user->id,
                'target_user_email' => $user->email,
                'old_role' => $oldRole,
                'new_role' => $request->role,
                'session_updated' => $user->id == $currentUser['id']
            ]);

            $response = response()->json([
                'success' => true,
                'message' => 'User role updated successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->full_name ?? $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'google_id' => $user->google_id
                ],
                'redirect_required' => $user->id == $currentUser['id'] && $request->role === 'admin'
            ]);
            
            return $this->addNoCacheHeaders($response);

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
        \Log::info('getAllUsers method called', [
            'timestamp' => now(),
            'session_id' => session()->getId()
        ]);
        
        // Check if current user is admin
        $currentUser = session('user_data');
        if (!$currentUser || $currentUser['role'] !== 'admin') {
            \Log::warning('Unauthorized access to getAllUsers', [
                'current_user' => $currentUser,
                'session_id' => session()->getId()
            ]);
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            \Log::info('Getting all users for admin', [
                'admin_id' => $currentUser['id'],
                'admin_email' => $currentUser['email']
            ]);

            // Get all users without any filters first
            $allUsers = User::all();
            \Log::info('All users in database (raw)', [
                'total_count' => $allUsers->count(),
                'user_ids' => $allUsers->pluck('id')->toArray(),
                'users' => $allUsers->map(function($u) {
                    return [
                        'id' => $u->id,
                        'username' => $u->username,
                        'name' => $u->name,
                        'email' => $u->email,
                        'role' => $u->role
                    ];
                })->toArray()
            ]);

            // Try without orderBy to get all users
            $users = User::all();
                
            // Also try without orderBy to see if that's the issue
            $usersNoOrder = User::all();
            \Log::info('Users without orderBy', [
                'count' => $usersNoOrder->count(),
                'user_ids' => $usersNoOrder->pluck('id')->toArray()
            ]);
                
            \Log::info('Users before mapping', [
                'count' => $users->count(),
                'user_ids' => $users->pluck('id')->toArray(),
                'users_raw' => $users->map(function($u) {
                    return [
                        'id' => $u->id,
                        'username' => $u->username,
                        'name' => $u->name,
                        'email' => $u->email,
                        'role' => $u->role,
                        'created_at' => $u->created_at,
                        'last_login_at' => $u->last_login_at
                    ];
                })->toArray()
            ]);
            
            $users = $users->map(function ($user) {
                // Handle users with NULL name field
                $displayName = $user->full_name ?? $user->name ?? $user->username ?? 'Unknown User';
                
                return [
                    'id' => $user->id,
                    'username' => $user->username,
                    'name' => $displayName,
                    'email' => $user->email,
                    'role' => $user->role ?? 'user', // Default role if NULL
                    'google_id' => $user->google_id ? 'Yes' : 'No',
                    'created_at' => $user->created_at ? $user->created_at->format('d/m/Y H:i') : 'Unknown',
                    'last_login_at' => $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Never'
                ];
            });

            \Log::info('Users retrieved successfully', [
                'user_count' => $users->count(),
                'users' => $users->toArray()
            ]);
            
            // Log each user individually for debugging
            foreach ($users as $user) {
                \Log::info('User data', [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ]);
            }
            
            // Log total count for verification
            \Log::info('Total users returned by API', [
                'count' => $users->count(),
                'expected_count' => User::count()
            ]);

            \Log::info('Returning users to frontend', [
                'user_count' => $users->count(),
                'user_ids' => $users->pluck('id')->toArray(),
                'response_data' => [
                    'success' => true,
                    'users_count' => $users->count()
                ]
            ]);

            $response = response()->json([
                'success' => true,
                'users' => $users
            ]);
            
            return $this->addNoCacheHeaders($response);

        } catch (\Exception $e) {
            \Log::error('Failed to get users list', [
                'admin_id' => $currentUser['id'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Failed to get users list'], 500);
        }
    }







}
