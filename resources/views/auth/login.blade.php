<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Meta Tags -->
    @include('components.seo-meta', [
        'page' => 'login',
        'title' => 'Masuk - Sistem Pemesanan Ruang Meeting',
        'description' => 'Masuk ke sistem pemesanan ruang meeting untuk mengelola jadwal meeting Anda. Akses mudah dan aman dengan berbagai metode login.',
        'keywords' => 'login, masuk, sistem pemesanan, ruang meeting, autentikasi',
        'canonical' => '/login',
        'robots' => 'noindex, nofollow'
    ])
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <script>
        // Alternative loading method for Turnstile
        window.addEventListener('load', function() {
            if (typeof window.turnstile === 'undefined') {
                console.log('Loading Turnstile script manually...');
                const script = document.createElement('script');
                script.src = 'https://challenges.cloudflare.com/turnstile/v0/api.js';
                script.async = true;
                script.defer = true;
                document.head.appendChild(script);
            }
        });
    </script>
    <script src="https://apis.google.com/js/platform.js" async defer></script>
    <meta name="google-signin-client_id" content="{{ env('GOOGLE_CLIENT_ID') }}">
    <style>
        /* Background gradient */
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            min-height: 100vh !important;
        }
        
        /* Glass effect */
        .glass-effect {
            background: rgba(255, 255, 255, 0.1) !important;
            backdrop-filter: blur(10px) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
        }
        
        /* Override any conflicting styles */
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }
        
        /* Ensure text is white */
        .text-white {
            color: white !important;
        }
        
        /* Ensure form styling is correct */
        .glass-effect h1,
        .glass-effect h2,
        .glass-effect p,
        .glass-effect label {
            color: white !important;
        }
        
        /* Cloudflare Turnstile styling */
        .cf-turnstile {
            border-radius: 8px !important;
        }
        
        /* Google Sign-In button styling */
        .google-signin-button {
            width: 100%;
            height: 48px;
            background: #fff;
            border: 1px solid #dadce0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            font-weight: 500;
            color: #3c4043;
            text-decoration: none;
        }
        
        .google-signin-button:hover {
            background: #f8f9fa;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .google-signin-button:active {
            background: #f1f3f4;
        }
        
        .google-signin-button img {
            width: 20px;
            height: 20px;
            margin-right: 12px;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    <!-- Mobile Sidebar -->
    @include('components.mobile-sidebar', [
        'userRole' => 'guest',
        'pageTitle' => 'Login'
    ])
    <div class="w-full max-w-md">
        <!-- Logo/Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-full shadow-lg mb-4">
                <img src="{{ asset('logo-bgn.png') }}" alt="BGN Logo" class="w-12 h-12 object-contain">
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Sistem Pemesanan Ruang Meeting</h1>
            <p class="text-white/80">Silakan masuk untuk melanjutkan</p>
        </div>

        <!-- Login Form -->
        <div class="glass-effect rounded-2xl p-8 shadow-2xl">
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span class="text-sm">{{ $errors->first() }}</span>
                    </div>
                </div>
            @endif

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span class="text-sm">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                
                <!-- Username/Email Field -->
                <div>
                    <label for="username" class="block text-sm font-medium text-white mb-2">
                        <i class="fas fa-user mr-2"></i>Nama Pengguna atau Email
                    </label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        value="{{ old('username') }}"
                        class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all duration-300"
                        placeholder="Masukkan nama pengguna atau email"
                        required
                        autofocus
                    >
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-white mb-2">
                        <i class="fas fa-lock mr-2"></i>Kata Sandi
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all duration-300 pr-12"
                            placeholder="Masukkan kata sandi"
                            required
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword()"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-white/60 hover:text-white transition-colors"
                        >
                            <i id="password-icon" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Cloudflare Turnstile -->
                <div class="flex justify-center mb-4">
                    <div class="cf-turnstile" 
                         data-sitekey="0x4AAAAAAB56ltjhELoBWYew" 
                         data-callback="onTurnstileSuccess"
                         data-error-callback="onTurnstileError"
                         data-theme="light"
                         style="min-height: 65px; border: 1px solid #ccc; border-radius: 8px; background: #f9f9f9;">
                    </div>
                </div>
                
                <!-- Fallback message if Turnstile doesn't load -->
                <div id="turnstile-fallback" style="display: none; text-align: center; padding: 10px; background: #f0f0f0; border-radius: 8px; margin-bottom: 10px;">
                    <p style="color: #666; font-size: 14px;">Security verification loading...</p>
                </div>
                
                <!-- Hidden input for Turnstile response -->
                <input type="hidden" name="cf-turnstile-response" id="cf-turnstile-response" value="">

                <!-- Login Button -->
                <button 
                    type="submit" 
                    id="loginButton"
                    class="w-full bg-white text-indigo-600 font-semibold py-3 px-4 rounded-lg hover:bg-white/90 focus:outline-none focus:ring-2 focus:ring-white/50 transition-all duration-300 transform hover:scale-105 shadow-lg"
                >
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Masuk
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-white/20"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-transparent text-white/60">atau</span>
                </div>
            </div>

            <!-- Google Sign-In Button -->
            <div class="mb-6">
                <a href="{{ route('auth.google') }}" class="google-signin-button">
                    <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google">
                    Masuk dengan Google
                </a>
            </div>

            <!-- Additional Links -->
            <div class="mt-6 space-y-3">
                <div class="text-center">
                    <a href="{{ route('password.request') }}" class="text-white/80 hover:text-white text-sm underline">
                        <i class="fas fa-key mr-1"></i>
                        Lupa Password?
                    </a>
                </div>
                <div class="text-center">
                    <a href="{{ route('register') }}" class="text-white/80 hover:text-white text-sm underline">
                        <i class="fas fa-user-plus mr-1"></i>
                        Daftar Akun Baru
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8">
            <p class="text-white/60 text-sm">
                © {{ date('Y') }} Sistem Pemesanan Ruang Meeting. Semua hak dilindungi.
            </p>
            <p class="text-white/40 text-xs mt-2">
                Dibuat dengan ❤️ oleh eL PUSDATIN
            </p>
            <p class="text-white/50 text-xs mt-3">
                <a href="{{ route('privacy.policy') }}" class="hover:text-white underline transition-colors duration-300">
                    Kebijakan Privasi
                </a>
                <span class="mx-2">•</span>
                <a href="{{ route('terms.service') }}" class="hover:text-white underline transition-colors duration-300">
                    Syarat dan Ketentuan
                </a>
            </p>
        </div>
    </div>

    <!-- WhatsApp Floating Button -->
    @include('components.whatsapp-float')

    <script>
        // Cloudflare Turnstile callbacks
        function onTurnstileSuccess(token) {
            console.log('Turnstile success:', token);
            document.getElementById('cf-turnstile-response').value = token;
            document.getElementById('turnstile-fallback').style.display = 'none';
        }
        
        function onTurnstileError(error) {
            console.error('Turnstile error:', error);
            document.getElementById('cf-turnstile-response').value = '';
            document.getElementById('turnstile-fallback').style.display = 'block';
        }
        
        // Initialize Turnstile with real site key
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Cloudflare Turnstile initialized with site key: 0x4AAAAAAB56ltjhELoBWYew');
            
            // Check if Turnstile script loaded
            if (typeof window.turnstile === 'undefined') {
                console.error('Turnstile script not loaded');
                document.getElementById('turnstile-fallback').style.display = 'block';
            } else {
                console.log('Turnstile script loaded successfully');
            }
            
            // Show fallback after 3 seconds if Turnstile doesn't load
            setTimeout(function() {
                const turnstileElement = document.querySelector('.cf-turnstile');
                if (turnstileElement && turnstileElement.children.length === 0) {
                    console.warn('Turnstile widget not rendered, showing fallback');
                    document.getElementById('turnstile-fallback').style.display = 'block';
                }
            }, 3000);
        });
        
        // Password toggle function
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('password-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                passwordIcon.className = 'fas fa-eye';
            }
        }
    </script>
</body>
</html>

