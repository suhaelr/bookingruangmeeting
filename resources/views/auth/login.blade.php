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
    <script>
    </script>
    
    
    <!-- Prevent caching of login page -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <script>
        // BGN Logo Animation Control
        document.addEventListener('DOMContentLoaded', function() {
            const logo = document.querySelector('.logo-glow');
            const logoWrapper = document.querySelector('.logo-wrapper');
            const gifBackground = document.querySelector('.gif-background');
            
            // Debug GIF loading
            if (gifBackground) {
                gifBackground.addEventListener('load', function() {
                    console.log('GIF loaded successfully');
                    this.style.opacity = '0.4';
                });
                
                gifBackground.addEventListener('error', function() {
                    console.log('GIF failed to load, checking path...');
                    console.log('Current src:', this.src);
                    // Try alternative path
                    this.src = '/3708555zcov227jtb.gif';
                });
            }
            
            if (logo && logoWrapper) {
                // Add hover effects
                logoWrapper.addEventListener('mouseenter', function() {
                    logo.style.animationDuration = '0.5s';
                    logoWrapper.style.transform = 'scale(1.1)';
                });
                
                logoWrapper.addEventListener('mouseleave', function() {
                    logo.style.animationDuration = '2s';
                    logoWrapper.style.transform = 'scale(1)';
                });
                
                // Add click effect
                logoWrapper.addEventListener('click', function() {
                    logo.style.animation = 'none';
                    logo.offsetHeight; // Trigger reflow
                    logo.style.animation = 'logoGlow 0.3s ease-in-out, logoBlink 1.5s ease-in-out infinite';
                });
                
                // Random glow intensity
                setInterval(function() {
                    const randomIntensity = Math.random() * 0.5 + 0.5; // 0.5 to 1.0
                    logo.style.filter = `drop-shadow(0 0 ${20 * randomIntensity}px rgba(255, 215, 0, ${0.6 + randomIntensity * 0.4}))`;
                }, 2000);
            }
        });
    </script>
    <style>
        /* Background gradient */
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            min-height: 100vh !important;
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            box-sizing: border-box;
        }
        
        /* GIF Background Overlay */
        .gif-background {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            opacity: 0.4;
            mix-blend-mode: screen;
            z-index: 0;
            pointer-events: none;
            filter: hue-rotate(20deg) saturate(1.2) brightness(0.8);
            animation: gifFade 8s ease-in-out infinite alternate;
            /* Fallback if GIF fails to load */
            background: linear-gradient(45deg, rgba(102, 126, 234, 0.3), rgba(118, 75, 162, 0.3));
        }
        
        /* Ensure GIF is visible when loaded */
        .gif-background[src] {
            background: none;
        }
        
        /* Mobile responsive adjustments */
        @media (max-width: 768px) {
            .gif-background {
                width: 100vw;
                height: 100vh;
                min-width: 100%;
                min-height: 100%;
                object-fit: cover;
                object-position: center center;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
            }
            
            .login-container {
                min-height: 100vh;
                padding: 0.5rem;
                align-items: center;
                justify-content: center;
                padding-top: 0;
            }
            
            .content-overlay {
                padding: 0.5rem;
                max-width: 100%;
                width: 100%;
                margin: 0;
                box-sizing: border-box;
            }
            
            /* Fix logo positioning on mobile - smaller */
            .logo-wrapper {
                margin: 0 auto 0.5rem auto;
                display: flex !important;
                justify-content: center;
                align-items: center;
                width: 3rem;
                height: 3rem;
            }
            
            .logo-wrapper img {
                width: 2.5rem;
                height: 2.5rem;
            }
            
            /* Center the form container */
            .glass-effect {
                margin: 0;
                width: 100%;
                max-width: 100%;
                box-sizing: border-box;
                padding: 0.5rem;
            }
            
            /* Fix header text alignment - smaller */
            .text-center h1 {
                font-size: 1.25rem !important;
                line-height: 1.2 !important;
                margin-bottom: 0.25rem !important;
            }
            
            .text-center p {
                font-size: 0.75rem !important;
                margin-bottom: 0.5rem !important;
            }
            
            /* Ensure proper spacing - reduced */
            .mb-8 {
                margin-bottom: 0.5rem !important;
            }
            
            /* Form elements smaller */
            .glass-effect .mb-6 {
                margin-bottom: 0.75rem !important;
            }
            
            .glass-effect .mb-4 {
                margin-bottom: 0.5rem !important;
            }
            
            /* Input fields smaller */
            .glass-effect input {
                padding: 0.5rem !important;
                font-size: 0.875rem !important;
            }
            
            .glass-effect label {
                font-size: 0.75rem !important;
                margin-bottom: 0.25rem !important;
            }
            
            /* Buttons smaller */
            .glass-effect button {
                padding: 0.5rem 1rem !important;
                font-size: 0.875rem !important;
            }
            
            /* Links smaller */
            .glass-effect a {
                font-size: 0.75rem !important;
                color: rgba(255, 255, 255, 0.7) !important;
            }
            
            .glass-effect a:hover {
                color: rgba(255, 255, 255, 0.9) !important;
            }
            
            /* Ensure footer is visible and compact */
            .glass-effect .text-center {
                margin-top: 0.75rem !important;
            }
            
            .glass-effect .text-center p {
                font-size: 0.625rem !important;
                line-height: 1.2 !important;
                margin-bottom: 0.25rem !important;
            }
            
            /* Make sure all content fits in one screen */
            .glass-effect {
                max-height: 100vh;
                overflow-y: auto;
            }
        }
        
        @media (max-width: 480px) {
            .gif-background {
                width: 100vw;
                height: 100vh;
                object-fit: cover;
                object-position: center center;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
            }
            
            .login-container {
                min-height: 100vh;
                padding: 0.25rem;
                align-items: center;
                justify-content: center;
                padding-top: 0;
            }
            
            .content-overlay {
                padding: 0.25rem;
                max-width: 100%;
                width: 100%;
                margin: 0;
                box-sizing: border-box;
            }
            
            /* Fix logo positioning on small mobile - very small */
            .logo-wrapper {
                margin: 0 auto 0.25rem auto;
                display: flex !important;
                justify-content: center;
                align-items: center;
                width: 2.5rem;
                height: 2.5rem;
            }
            
            .logo-wrapper img {
                width: 2rem;
                height: 2rem;
            }
            
            /* Center the form container */
            .glass-effect {
                margin: 0;
                width: 100%;
                max-width: 100%;
                box-sizing: border-box;
                padding: 0.5rem;
            }
            
            /* Fix header text alignment - very small */
            .text-center h1 {
                font-size: 1rem !important;
                line-height: 1.1 !important;
                margin-bottom: 0.125rem !important;
            }
            
            .text-center p {
                font-size: 0.625rem !important;
                margin-bottom: 0.25rem !important;
            }
            
            /* Ensure proper spacing - minimal */
            .mb-8 {
                margin-bottom: 0.25rem !important;
            }
            
            /* Form elements very small */
            .glass-effect .mb-6 {
                margin-bottom: 0.5rem !important;
            }
            
            .glass-effect .mb-4 {
                margin-bottom: 0.25rem !important;
            }
            
            /* Input fields very small */
            .glass-effect input {
                padding: 0.375rem !important;
                font-size: 0.75rem !important;
            }
            
            .glass-effect label {
                font-size: 0.625rem !important;
                margin-bottom: 0.125rem !important;
            }
            
            /* Buttons very small */
            .glass-effect button {
                padding: 0.375rem 0.75rem !important;
                font-size: 0.75rem !important;
            }
            
            /* Links very small */
            .glass-effect a {
                font-size: 0.625rem !important;
            }
            
            /* Footer links very small */
            .glass-effect .text-center a {
                font-size: 0.5rem !important;
                color: rgba(255, 255, 255, 0.7) !important;
            }
            
            .glass-effect .text-center a:hover {
                color: rgba(255, 255, 255, 0.9) !important;
            }
            
            /* Ensure footer is visible and compact */
            .glass-effect .text-center {
                margin-top: 0.5rem !important;
            }
            
            .glass-effect .text-center p {
                font-size: 0.5rem !important;
                line-height: 1.2 !important;
                margin-bottom: 0.125rem !important;
            }
            
            /* Make sure all content fits in one screen */
            .glass-effect {
                max-height: 100vh;
                overflow-y: auto;
            }
        }
        
        /* Ensure GIF is always centered and visible */
        @media (orientation: landscape) {
            .gif-background {
                width: 100vw;
                height: 100vh;
                object-fit: cover;
                object-position: center center;
            }
        }
        
        @media (orientation: portrait) {
            .gif-background {
                width: 100vw;
                height: 100vh;
                object-fit: cover;
                object-position: center center;
            }
        }
        
        @keyframes gifFade {
            0% {
                opacity: 0.3;
                filter: hue-rotate(20deg) saturate(1.2) brightness(0.8);
            }
            50% {
                opacity: 0.5;
                filter: hue-rotate(30deg) saturate(1.4) brightness(0.9);
            }
            100% {
                opacity: 0.4;
                filter: hue-rotate(25deg) saturate(1.3) brightness(0.85);
            }
        }
        
        /* Content overlay */
        .content-overlay {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 28rem;
            margin: 0 auto;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        
        /* Ensure form is scrollable */
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            width: 100%;
            box-sizing: border-box;
        }
        
        /* Glass effect */
        .glass-effect {
            background: rgba(255, 255, 255, 0.15) !important;
            backdrop-filter: blur(15px) !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1) !important;
        }
        
        /* Override any conflicting styles */
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            overflow-x: hidden;
            overflow-y: auto;
            height: 100%;
            margin: 0;
            padding: 0;
            width: 100%;
            box-sizing: border-box;
        }
        
        html {
            height: 100%;
            overflow-x: hidden;
            overflow-y: auto;
            margin: 0;
            padding: 0;
            width: 100%;
            box-sizing: border-box;
        }
        
        * {
            box-sizing: border-box;
        }
        
        /* Footer links color fix */
        .text-center a {
            color: rgba(255, 255, 255, 0.7) !important;
            text-decoration: underline;
            transition: color 0.3s ease;
        }
        
        .text-center a:hover {
            color: rgba(255, 255, 255, 0.9) !important;
        }
        
        /* Override any blue link colors */
        a:not(.btn):not(.button) {
            color: rgba(255, 255, 255, 0.7) !important;
        }
        
        a:not(.btn):not(.button):hover {
            color: rgba(255, 255, 255, 0.9) !important;
        }
        
        /* Specific footer links styling */
        .text-center p a {
            color: rgba(255, 255, 255, 0.7) !important;
            text-decoration: underline;
            transition: color 0.3s ease;
        }
        
        .text-center p a:hover {
            color: rgba(255, 255, 255, 0.9) !important;
        }
        
        /* Override Tailwind link colors */
        .underline {
            color: rgba(255, 255, 255, 0.7) !important;
        }
        
        .hover\:text-white:hover {
            color: rgba(255, 255, 255, 0.9) !important;
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
        
        /* BGN Logo Animation */
        .logo-container {
            position: relative;
            display: inline-block;
        }
        
        .logo-glow {
            animation: logoGlow 2s ease-in-out infinite alternate;
            filter: drop-shadow(0 0 20px rgba(255, 215, 0, 0.8));
        }
        
        .logo-blink {
            animation: logoBlink 1.5s ease-in-out infinite;
        }
        
        .logo-pulse {
            animation: logoPulse 3s ease-in-out infinite;
        }
        
        @keyframes logoGlow {
            0% {
                filter: drop-shadow(0 0 10px rgba(255, 215, 0, 0.6));
                transform: scale(1);
            }
            50% {
                filter: drop-shadow(0 0 25px rgba(255, 215, 0, 1));
                transform: scale(1.05);
            }
            100% {
                filter: drop-shadow(0 0 15px rgba(255, 215, 0, 0.8));
                transform: scale(1);
            }
        }
        
        @keyframes logoBlink {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }
        
        @keyframes logoPulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(255, 215, 0, 0.7);
            }
            50% {
                box-shadow: 0 0 0 20px rgba(255, 215, 0, 0);
            }
        }
        
        /* Enhanced logo container */
        .logo-wrapper {
            position: relative;
            display: inline-block;
            border-radius: 50%;
            padding: 4px;
            background: linear-gradient(45deg, #FFD700, #FFA500, #FFD700);
            animation: logoPulse 3s ease-in-out infinite;
            margin: 0 auto;
            text-align: center;
        }
        
        .logo-wrapper::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #FFD700, #FFA500, #FFD700);
            border-radius: 50%;
            z-index: -1;
            animation: logoGlow 2s ease-in-out infinite alternate;
        }
        

    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="gradient-bg">
    <!-- GIF Background -->
    <img src="{{ asset('3708555zcov227jtb.gif') }}" alt="Background Animation" class="gif-background" onerror="console.log('GIF failed to load'); this.style.display='none';">
    
    <!-- Mobile Sidebar -->
    @include('components.mobile-sidebar', [
        'userRole' => 'guest',
        'pageTitle' => 'Login'
    ])
    
    <div class="login-container">
        <div class="content-overlay">
        <!-- Logo/Header -->
        <div class="text-center mb-8">
            <div class="logo-wrapper inline-flex items-center justify-center w-16 h-16 bg-white rounded-full shadow-lg mb-4">
                <img src="{{ asset('logo-bgn.png') }}" alt="BGN Logo" class="w-12 h-12 object-contain logo-glow logo-blink">
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Sistem Pemesanan Ruang Meeting</h1>
            <p class="text-white/80">Silakan masuk untuk melanjutkan</p>
        </div>

        <!-- Login Form -->
        <div class="glass-effect rounded-2xl p-6 shadow-2xl">
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
                    <label for="username" class="block text-base font-medium text-white mb-2 text-left" style="text-align: left !important;">
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
                    <label for="password" class="block text-base font-medium text-white mb-2 text-left" style="text-align: left !important;">
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

