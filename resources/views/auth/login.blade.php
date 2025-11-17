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
        /* Background white */
        .gradient-bg {
            background: #ffffff !important;
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
                color: rgba(0, 0, 0, 0.9) !important;
            }
            
            .glass-effect a:hover {
                color: rgba(0, 0, 0, 1) !important;
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
                color: rgba(0, 0, 0, 0.9) !important;
            }
            
            .glass-effect .text-center a:hover {
                color: rgba(0, 0, 0, 1) !important;
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
            background: #ffffff !important;
            backdrop-filter: blur(15px) !important;
            border: 1px solid rgba(0, 0, 0, 0.2) !important;
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
            color: rgba(0, 0, 0, 0.9) !important;
            text-decoration: underline;
            transition: color 0.3s ease;
        }
        
        .text-center a:hover {
            color: rgba(0, 0, 0, 1) !important;
        }
        
        /* Override any blue link colors */
        a:not(.btn):not(.button) {
            color: rgba(0, 0, 0, 0.9) !important;
        }
        
        a:not(.btn):not(.button):hover {
            color: rgba(0, 0, 0, 1) !important;
        }
        
        /* Specific footer links styling */
        .text-center p a {
            color: rgba(0, 0, 0, 0.9) !important;
            text-decoration: underline;
            transition: color 0.3s ease;
        }
        
        .text-center p a:hover {
            color: rgba(0, 0, 0, 1) !important;
        }
        
        /* Override Tailwind link colors */
        .underline {
            color: rgba(0, 0, 0, 0.9) !important;
        }
        
        .hover\:text-white:hover {
            color: rgba(0, 0, 0, 0.9) !important;
        }
        
        /* Ensure text is black */
        .text-white {
            color: black !important;
        }
        
        /* Ensure form styling is correct */
        .glass-effect h1,
        .glass-effect h2,
        .glass-effect p,
        .glass-effect label {
            color: black !important;
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
            <h1 class="text-3xl font-bold text-black mb-2">Sistem Pemesanan Ruang Meeting</h1>
            <p class="text-black">Silakan masuk untuk melanjutkan</p>
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
                    <label for="username" class="block text-base font-medium text-black mb-2 text-left" style="text-align: left !important;">
                        <i class="fas fa-user mr-2"></i>Nama Pengguna atau Email
                    </label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        value="{{ old('username') }}"
                        class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg text-black placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300"
                        placeholder="Masukkan nama pengguna atau email"
                        required
                        autofocus
                    >
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-base font-medium text-black mb-2 text-left" style="text-align: left !important;">
                        <i class="fas fa-lock mr-2"></i>Kata Sandi
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg text-black placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300 pr-12"
                            placeholder="Masukkan kata sandi"
                            required
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword()"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 transition-colors"
                        >
                            <i id="password-icon" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>


                <!-- Login Button -->
                <button 
                    type="submit" 
                    id="loginButton"
                    class="w-full bg-gray-200 text-black font-semibold py-3 px-4 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-all duration-300 transform hover:scale-105 shadow-lg border border-gray-300"
                >
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Masuk
                </button>
            </form>

            <!-- Additional Links -->
            <div class="mt-6 space-y-3">
                <div class="text-center">
                    <a href="{{ route('password.request') }}" class="text-black hover:text-gray-800 text-sm underline">
                        <i class="fas fa-key mr-1"></i>
                        Lupa Password?
                    </a>
                </div>
                <div class="text-center">
                    <a href="{{ route('register') }}" class="text-black hover:text-gray-800 text-sm underline">
                        <i class="fas fa-user-plus mr-1"></i>
                        Daftar Akun Baru
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8">
            <p class="text-black text-sm">
                <span onclick="showChangelogModal()" class="text-black font-medium cursor-pointer hover:text-gray-800 underline transition-colors duration-300">Versi Aplikasi v2.1.7</span><br>
                <span onclick="showGuideModal()" class="text-black font-medium cursor-pointer hover:text-gray-800 underline transition-colors duration-300">Lihat Panduan</span><br>
                © {{ date('Y') }} Sistem Pemesanan Ruang Meeting. Semua hak dilindungi.
            </p>
            
            <p class="text-black text-xs mt-3">
                <a href="{{ route('privacy.policy') }}" class="text-black hover:text-gray-800 underline transition-colors duration-300">
                    Kebijakan Privasi
                </a>
                <span class="mx-2">•</span>
                <a href="{{ route('terms.service') }}" class="text-black hover:text-gray-800 underline transition-colors duration-300">
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

        // Changelog Modal Functions
        window.showChangelogModal = function() {
            const modalHtml = `
                <div id="changelogModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-2 sm:p-4" onclick="closeChangelogModal()">
                    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
                        <div class="sticky top-0 bg-white border-b border-gray-200 z-10 p-4 sm:p-6 pb-4 flex justify-between items-center">
                            <h3 class="text-lg sm:text-xl font-bold text-gray-800">Changelog Aplikasi</h3>
                            <button type="button" onclick="closeChangelogModal()" class="text-gray-500 hover:text-gray-700 p-2 -mr-2">
                                <i class="fas fa-times text-xl sm:text-2xl"></i>
                            </button>
                        </div>
                        
                        <div class="p-4 sm:p-6">
                            <!-- v2.1.7 -->
                            <div class="mb-6">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-lg font-bold text-gray-800">v2.1.7 (2025) - Peningkatan UI dan Fitur Profil</h4>
                                    <span class="text-sm text-gray-500">November 2025</span>
                                </div>
                                <ul class="space-y-2 text-sm text-gray-700">
                                    <li class="flex items-start">
                                        <i class="fas fa-user-shield text-purple-500 mr-2 mt-1"></i>
                                        <span><strong>Menu Profil Admin</strong> - Tambah menu profil admin untuk edit email dan informasi lainnya, sama seperti di akun user</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-list text-blue-500 mr-2 mt-1"></i>
                                        <span><strong>Dropdown Unit Kerja</strong> - Field Unit Kerja sekarang berbentuk dropdown dengan 7 pilihan unit kerja standar (hardcoded)</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-eye-slash text-gray-500 mr-2 mt-1"></i>
                                        <span><strong>Perbaikan Tampilan Kapasitas</strong> - Kapasitas ruang tidak ditampilkan jika bernilai 0 atau kosong di form create booking</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-bug text-red-500 mr-2 mt-1"></i>
                                        <span><strong>Perbaikan Error Duplicate Email</strong> - Perbaiki error duplicate email saat update profil admin</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- v2.1.6 -->
                            <div class="mb-6 border-t border-gray-200 pt-6">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-lg font-bold text-gray-800">v2.1.6 (2025) - Email Notifikasi Otomatis</h4>
                                    <span class="text-sm text-gray-500">November 2025</span>
                                </div>
                                <ul class="space-y-2 text-sm text-gray-700">
                                    <li class="flex items-start">
                                        <i class="fas fa-envelope text-blue-500 mr-2 mt-1"></i>
                                        <span><strong>Email Notifikasi Otomatis</strong> - Setiap notifikasi di dashboard admin dan user sekarang otomatis dikirim ke email masing-masing</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Template Email Modern</strong> - Template email notifikasi dengan desain modern, responsif, dan berbahasa Indonesia</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Notifikasi ke Semua Admin</strong> - Notifikasi admin sekarang dikirim ke semua akun admin, bukan hanya admin pertama</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Detail Booking di Email</strong> - Email notifikasi menampilkan detail lengkap booking jika tersedia</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- v2.1.5 -->
                            <div class="mb-6 border-t border-gray-200 pt-6">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-lg font-bold text-gray-800">v2.1.5 (2025) - Penghapusan Fitur Preempt</h4>
                                    <span class="text-sm text-gray-500">November 2025</span>
                                </div>
                                <ul class="space-y-2 text-sm text-gray-700">
                                    <li class="flex items-start">
                                        <i class="fas fa-minus-circle text-orange-500 mr-2 mt-1"></i>
                                        <span><strong>Penghapusan Fitur Didahulukan Meeting</strong> - Fitur "Minta Didahulukan" (preempt request) telah dihapus dari sistem untuk menyederhanakan proses booking</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Penyederhanaan Alur Booking</strong> - Sistem booking sekarang lebih sederhana tanpa fitur preempt, fokus pada booking langsung</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Perbaikan Validasi Konflik</strong> - Validasi konflik jadwal tetap berfungsi untuk mencegah double booking</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- v2.1.4 -->
                            <div class="mb-6 border-t border-gray-200 pt-6">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-lg font-bold text-gray-800">v2.1.4 (2025) - Field Kapasitas Opsional</h4>
                                    <span class="text-sm text-gray-500">November 2025</span>
                                </div>
                                <ul class="space-y-2 text-sm text-gray-700">
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Field Kapasitas Menjadi Opsional</strong> - Field kapasitas di form create room sekarang opsional, admin bisa membuat room tanpa mengisi kapasitas</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Fleksibilitas Input Data</strong> - Admin memiliki lebih banyak fleksibilitas dalam membuat room meeting tanpa harus mengisi kapasitas</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- v2.1.3 -->
                            <div class="mb-6 border-t border-gray-200 pt-6">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-lg font-bold text-gray-800">v2.1.3 (2025) - Mobile Calendar Enhancement</h4>
                                    <span class="text-sm text-gray-500">November 2025</span>
                                </div>
                                <ul class="space-y-2 text-sm text-gray-700">
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Modal Daftar Meeting untuk Mobile</strong> - Klik box tanggal di kalender mobile menampilkan modal dengan semua meeting untuk hari tersebut</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Detail Meeting dari Modal</strong> - Klik item meeting di modal menampilkan detail lengkap seperti popup detail meeting</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Pengalaman Mobile yang Lebih Baik</strong> - Tidak perlu scroll di dalam kalender, semua meeting ditampilkan dalam modal yang mudah diakses</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Scroll Tetap Berfungsi</strong> - Scroll di dalam kalender tetap berfungsi normal, modal hanya muncul saat klik box tanggal</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- v2.1.2 -->
                            <div class="mb-6 border-t border-gray-200 pt-6">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-lg font-bold text-gray-800">v2.1.2 (2025) - Real-time Notifications</h4>
                                    <span class="text-sm text-gray-500">November 2025</span>
                                </div>
                                <ul class="space-y-2 text-sm text-gray-700">
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Notifikasi Real-time untuk Admin dan User</strong> - Notifikasi auto-refresh setiap 10 detik tanpa perlu refresh browser</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Peningkatan Responsivitas Notifikasi</strong> - Notifikasi masuk secara real-time dalam maksimal 10 detik</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Pengalaman Pengguna yang Lebih Baik</strong> - Admin dan user tidak perlu refresh browser untuk melihat notifikasi baru</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- v2.1.1 -->
                            <div class="mb-6 border-t border-gray-200 pt-6">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-lg font-bold text-gray-800">v2.1.1 (2025) - Bug Fixes</h4>
                                    <span class="text-sm text-gray-500">November 2025</span>
                                </div>
                                <ul class="space-y-2 text-sm text-gray-700">
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Perbaikan Bug Notifikasi Admin</strong> - Teks "Admin Notifikasis" diperbaiki menjadi "Admin Notifikasi"</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Perbaikan Mark as Read</strong> - Badge count berkurang dengan benar setelah klik notifikasi</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Perbaikan Mark All as Read</strong> - Fungsi mark all as read sekarang bekerja dengan benar</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Perbaikan Badge Count</strong> - Badge count ter-update secara real-time setelah mark as read</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span><strong>Perbaikan Error Handling</strong> - Menambahkan error handling dan logging yang lebih baik untuk notifikasi</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- v1.0.0 -->
                            <div class="mb-4 border-t border-gray-200 pt-6">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-lg font-bold text-gray-800">v1.0.0 (2025) - Initial Release</h4>
                                    <span class="text-sm text-gray-500">Oktober 2025</span>
                                </div>
                                <ul class="space-y-2 text-sm text-gray-700">
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span>Sistem authentication lengkap</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span>Dashboard user dan admin</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span>Sistem booking dengan validasi cerdas</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span>Manajemen user dan ruang meeting</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span>Sistem notifikasi real-time</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span>Export data dalam format CSV</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span>Responsive design</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                        <span>Bahasa Indonesia dasar</span>
                                    </li>
                                </ul>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <button type="button" onclick="closeChangelogModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 w-full sm:w-auto">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            
            // Add event listener for ESC key
            document.addEventListener('keydown', function escHandler(e) {
                if (e.key === 'Escape') {
                    const modal = document.getElementById('changelogModal');
                    if (modal && !modal.classList.contains('hidden')) {
                        closeChangelogModal();
                        document.removeEventListener('keydown', escHandler);
                    }
                }
            });
        };

        window.closeChangelogModal = function() {
            const modal = document.getElementById('changelogModal');
            if (modal) {
                modal.remove();
            }
        };

        // Guide Modal Functions
        window.showGuideModal = function() {
            const modalHtml = `
                <div id="guideModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-2 sm:p-4" onclick="closeGuideModal()">
                    <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
                        <div class="sticky top-0 bg-white border-b border-gray-200 z-10 p-4 sm:p-6 pb-4 flex justify-between items-center">
                            <h3 class="text-lg sm:text-xl font-bold text-gray-800">📖 Panduan Penggunaan Aplikasi</h3>
                            <button type="button" onclick="closeGuideModal()" class="text-gray-500 hover:text-gray-700 p-2 -mr-2">
                                <i class="fas fa-times text-xl sm:text-2xl"></i>
                            </button>
                        </div>
                        
                        <div class="p-4 sm:p-6">
                            <!-- 1. Pendahuluan -->
                            <div class="mb-6">
                                <h4 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                    1. Pendahuluan
                                </h4>
                                <p class="text-sm text-gray-700 mb-3">
                                    Sistem Pemesanan Ruang Meeting adalah aplikasi berbasis web yang memungkinkan pengguna untuk memesan ruang meeting secara online. Aplikasi ini memiliki dua jenis pengguna: <strong>User</strong> (pengguna biasa) dan <strong>Admin</strong> (superadmin).
                                </p>
                            </div>

                            <!-- 2. Menu dan Navigasi untuk User -->
                            <div class="mb-6 border-t border-gray-200 pt-6">
                                <h4 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-user text-green-500 mr-2"></i>
                                    2. Menu dan Navigasi untuk User
                                </h4>
                                <p class="text-sm text-gray-700 mb-3">
                                    Setelah login sebagai user, Anda akan memiliki akses ke menu-menu berikut:
                                </p>
                                <ul class="space-y-2 text-sm text-gray-700 ml-4">
                                    <li class="flex items-start">
                                        <i class="fas fa-tachometer-alt text-indigo-500 mr-2 mt-1"></i>
                                        <div>
                                            <strong>Beranda</strong> - Menampilkan dashboard dengan kalender ruang meeting, daftar ruang yang tersedia, dan informasi booking terbaru. Di sini Anda dapat melihat ketersediaan ruang meeting secara real-time.
                                        </div>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-calendar-check text-blue-500 mr-2 mt-1"></i>
                                        <div>
                                            <strong>Pemesanan Saya</strong> - Menampilkan semua booking yang telah Anda buat, termasuk statusnya (pending, confirmed, cancelled, completed). Anda dapat melihat detail, mengedit, atau membatalkan booking dari sini.
                                        </div>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-plus text-green-500 mr-2 mt-1"></i>
                                        <div>
                                            <strong>Pemesanan Baru</strong> - Form untuk membuat booking baru. Di sini Anda dapat memilih ruang meeting, tanggal, waktu, mengundang PIC (Person In Charge), dan mengisi detail meeting.
                                        </div>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-user text-purple-500 mr-2 mt-1"></i>
                                        <div>
                                            <strong>Profil</strong> - Halaman untuk mengelola informasi profil Anda, termasuk nama lengkap, email, nomor telepon, dan unit kerja. Anda juga dapat mengubah password dari sini.
                                        </div>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-bell text-yellow-500 mr-2 mt-1"></i>
                                        <div>
                                            <strong>Notifikasi</strong> - Ikon lonceng di pojok kanan atas menampilkan semua notifikasi terkait booking Anda, seperti konfirmasi admin, perubahan status, atau undangan sebagai PIC.
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            <!-- 3. Menu dan Navigasi untuk Admin -->
                            <div class="mb-6 border-t border-gray-200 pt-6">
                                <h4 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-user-shield text-red-500 mr-2"></i>
                                    3. Menu dan Navigasi untuk Admin
                                </h4>
                                <p class="text-sm text-gray-700 mb-3">
                                    Sebagai admin, Anda memiliki akses penuh untuk mengelola sistem dengan menu-menu berikut:
                                </p>
                                <ul class="space-y-2 text-sm text-gray-700 ml-4">
                                    <li class="flex items-start">
                                        <i class="fas fa-tachometer-alt text-indigo-500 mr-2 mt-1"></i>
                                        <div>
                                            <strong>Beranda</strong> - Dashboard admin menampilkan statistik booking, ruang meeting, dan pengguna. Di sini Anda dapat melihat ringkasan aktivitas sistem.
                                        </div>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-users text-blue-500 mr-2 mt-1"></i>
                                        <div>
                                            <strong>Pengguna</strong> - Mengelola semua pengguna sistem. Anda dapat melihat daftar user, menambah user baru, mengedit informasi user, mengubah role (user/admin), atau menghapus user.
                                        </div>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-door-open text-green-500 mr-2 mt-1"></i>
                                        <div>
                                            <strong>Ruang Meeting</strong> - Mengelola semua ruang meeting. Anda dapat menambah ruang baru, mengedit informasi ruang (nama, lokasi, kapasitas, fasilitas), mengaktifkan/nonaktifkan ruang, atau menghapus ruang.
                                        </div>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-calendar-check text-orange-500 mr-2 mt-1"></i>
                                        <div>
                                            <strong>Pemesanan</strong> - Halaman utama untuk mengelola semua booking. Di sini Anda dapat melihat semua pemesanan, mengubah status booking (pending, confirmed, cancelled, completed), dan melihat detail lengkap setiap booking.
                                        </div>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-user text-purple-500 mr-2 mt-1"></i>
                                        <div>
                                            <strong>Profil</strong> - Halaman untuk mengelola profil admin, termasuk nama, email, nomor telepon, dan unit kerja.
                                        </div>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-bell text-yellow-500 mr-2 mt-1"></i>
                                        <div>
                                            <strong>Notifikasi</strong> - Ikon lonceng menampilkan notifikasi tentang booking baru, perubahan status booking, dan aktivitas sistem lainnya.
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            <!-- 4. Flow Aplikasi: Membuat Booking -->
                            <div class="mb-6 border-t border-gray-200 pt-6">
                                <h4 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-route text-blue-500 mr-2"></i>
                                    4. Flow Aplikasi: Membuat Booking
                                </h4>
                                <div class="space-y-3 text-sm text-gray-700">
                                    <div class="bg-blue-50 p-4 rounded-lg">
                                        <h5 class="font-semibold mb-2">Langkah-langkah membuat booking:</h5>
                                        <ol class="list-decimal list-inside space-y-2 ml-2">
                                            <li><strong>Login ke akun</strong> - Masuk menggunakan email/username dan password Anda.</li>
                                            <li><strong>Klik menu "Pemesanan Baru"</strong> - Atau klik tombol "Pemesanan Baru" di dashboard.</li>
                                            <li><strong>Pilih Ruang Meeting</strong> - Pilih ruang meeting yang ingin Anda pesan dari dropdown. Sistem akan menampilkan nama ruang, lokasi, dan kapasitas (jika tersedia).</li>
                                            <li><strong>Pilih Tanggal</strong> - Klik pada kalender untuk memilih tanggal meeting. Tanggal yang sudah terbooking akan ditandai.</li>
                                            <li><strong>Pilih Waktu</strong> - Pilih waktu mulai dan waktu selesai meeting. Sistem akan memvalidasi apakah slot waktu tersebut tersedia.</li>
                                            <li><strong>Isi Detail Meeting</strong>:
                                                <ul class="list-disc list-inside ml-4 mt-1 space-y-1">
                                                    <li>Judul meeting (wajib)</li>
                                                    <li>Deskripsi meeting (opsional)</li>
                                                    <li>Unit Kerja (pilih dari dropdown)</li>
                                                    <li>Jumlah peserta</li>
                                                </ul>
                                            </li>
                                            <li><strong>Undang PIC (Person In Charge)</strong> - Pilih PIC yang ingin diundang dengan mencentang checkbox. Anda dapat mengundang beberapa PIC sekaligus. Klik "Hapus Semua Pilihan" untuk menghapus semua pilihan PIC.</li>
                                            <li><strong>Submit Booking</strong> - Klik tombol "Buat Pemesanan" untuk mengirim permintaan booking.</li>
                                        </ol>
                                    </div>
                                    <p class="text-sm text-gray-600 italic">
                                        <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                                        <strong>Catatan:</strong> Setelah submit, booking akan berstatus <strong>"pending"</strong> dan menunggu approval dari admin. Anda akan menerima notifikasi via email dan di dashboard ketika status booking berubah.
                                    </p>
                                </div>
                            </div>

                            <!-- 5. Proses Approval oleh Admin -->
                            <div class="mb-6 border-t border-gray-200 pt-6">
                                <h4 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    5. Proses Approval oleh Admin
                                </h4>
                                <div class="space-y-3 text-sm text-gray-700">
                                    <div class="bg-green-50 p-4 rounded-lg">
                                        <h5 class="font-semibold mb-2">Alur approval booking:</h5>
                                        <ol class="list-decimal list-inside space-y-2 ml-2">
                                            <li><strong>User membuat booking</strong> - Booking dibuat dengan status "pending".</li>
                                            <li><strong>Admin menerima notifikasi</strong> - Semua admin akan menerima notifikasi (di dashboard dan email) tentang booking baru yang perlu di-approve.</li>
                                            <li><strong>Admin membuka halaman Pemesanan</strong> - Admin masuk ke menu "Pemesanan" untuk melihat daftar semua booking.</li>
                                            <li><strong>Admin melihat detail booking</strong> - Klik pada booking untuk melihat detail lengkap, termasuk:
                                                <ul class="list-disc list-inside ml-4 mt-1 space-y-1">
                                                    <li>Informasi user yang membuat booking</li>
                                                    <li>Ruang meeting yang dipesan</li>
                                                    <li>Tanggal dan waktu meeting</li>
                                                    <li>Judul dan deskripsi meeting</li>
                                                    <li>Daftar PIC yang diundang</li>
                                                    <li>Status booking saat ini</li>
                                                </ul>
                                            </li>
                                            <li><strong>Admin mengubah status booking</strong>:
                                                <ul class="list-disc list-inside ml-4 mt-1 space-y-1">
                                                    <li><strong>Confirmed</strong> - Menyetujui booking, booking akan dikonfirmasi dan ruang meeting ter-reserve.</li>
                                                    <li><strong>Cancelled</strong> - Menolak booking, booking akan dibatalkan dengan alasan (opsional).</li>
                                                    <li><strong>Pending</strong> - Membiarkan status tetap pending jika masih perlu review lebih lanjut.</li>
                                                    <li><strong>Completed</strong> - Menandai booking sebagai selesai setelah meeting berlangsung.</li>
                                                </ul>
                                            </li>
                                            <li><strong>User menerima notifikasi</strong> - User akan menerima notifikasi (di dashboard dan email) tentang perubahan status booking.</li>
                                        </ol>
                                    </div>
                                    <p class="text-sm text-gray-600 italic">
                                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-1"></i>
                                        <strong>Penting:</strong> Hanya admin yang dapat mengubah status booking. User tidak dapat mengubah status booking sendiri setelah dibuat, kecuali membatalkan booking yang masih pending atau confirmed (dengan batasan waktu).
                                    </p>
                                </div>
                            </div>

                            <!-- 6. Sistem Notifikasi Email -->
                            <div class="mb-6 border-t border-gray-200 pt-6">
                                <h4 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-envelope text-purple-500 mr-2"></i>
                                    6. Sistem Notifikasi Email
                                </h4>
                                <div class="space-y-3 text-sm text-gray-700">
                                    <p class="mb-3">
                                        Sistem ini dilengkapi dengan notifikasi email otomatis yang dikirim ke email terdaftar pengguna. Notifikasi email akan dikirim untuk:
                                    </p>
                                    <div class="bg-purple-50 p-4 rounded-lg">
                                        <h5 class="font-semibold mb-2">Notifikasi untuk User:</h5>
                                        <ul class="list-disc list-inside space-y-1 ml-2">
                                            <li><strong>Booking Baru Dibuat</strong> - Konfirmasi bahwa booking Anda telah dibuat dan sedang menunggu approval.</li>
                                            <li><strong>Status Booking Diperbarui</strong> - Notifikasi ketika admin mengubah status booking Anda (confirmed, cancelled, completed).</li>
                                            <li><strong>Booking Dibatalkan</strong> - Notifikasi ketika booking Anda dibatalkan oleh admin atau oleh Anda sendiri.</li>
                                            <li><strong>Undangan sebagai PIC</strong> - Notifikasi ketika Anda diundang sebagai PIC dalam sebuah meeting.</li>
                                        </ul>
                                    </div>
                                    <div class="bg-red-50 p-4 rounded-lg mt-3">
                                        <h5 class="font-semibold mb-2">Notifikasi untuk Admin:</h5>
                                        <ul class="list-disc list-inside space-y-1 ml-2">
                                            <li><strong>Permintaan Booking Baru</strong> - Notifikasi ketika ada user yang membuat booking baru yang perlu di-approve.</li>
                                            <li><strong>Booking Diperbarui</strong> - Notifikasi ketika user mengubah atau memperbarui booking yang sudah ada.</li>
                                            <li><strong>Booking Dibatalkan</strong> - Notifikasi ketika user membatalkan booking mereka.</li>
                                            <li><strong>Status Booking Diperbarui</strong> - Notifikasi ketika admin lain mengubah status booking.</li>
                                        </ul>
                                    </div>
                                    <p class="text-sm text-gray-600 italic mt-3">
                                        <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                                        <strong>Catatan:</strong> Semua notifikasi email menggunakan template modern dan responsif, serta ditulis dalam bahasa Indonesia. Email akan dikirim ke alamat email yang terdaftar di profil Anda.
                                    </p>
                                </div>
                            </div>

                            <!-- 7. Fitur Tambahan -->
                            <div class="mb-6 border-t border-gray-200 pt-6">
                                <h4 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-star text-yellow-500 mr-2"></i>
                                    7. Fitur Tambahan
                                </h4>
                                <div class="space-y-3 text-sm text-gray-700">
                                    <div class="bg-yellow-50 p-4 rounded-lg">
                                        <ul class="space-y-2">
                                            <li class="flex items-start">
                                                <i class="fas fa-calendar-alt text-indigo-500 mr-2 mt-1"></i>
                                                <div>
                                                    <strong>Kalender Interaktif</strong> - Dashboard user menampilkan kalender yang menunjukkan ketersediaan ruang meeting. Warna berbeda menunjukkan status booking (hijau = confirmed, kuning = pending, merah = cancelled).
                                                </div>
                                            </li>
                                            <li class="flex items-start">
                                                <i class="fas fa-search text-blue-500 mr-2 mt-1"></i>
                                                <div>
                                                    <strong>Pencarian dan Filter</strong> - Admin dapat mencari dan memfilter booking berdasarkan status, tanggal, ruang meeting, atau nama user.
                                                </div>
                                            </li>
                                            <li class="flex items-start">
                                                <i class="fas fa-users text-green-500 mr-2 mt-1"></i>
                                                <div>
                                                    <strong>Undangan PIC</strong> - User dapat mengundang beberapa PIC (Person In Charge) dalam satu booking. PIC yang diundang akan melihat booking tersebut di dashboard mereka.
                                                </div>
                                            </li>
                                            <li class="flex items-start">
                                                <i class="fas fa-eye text-purple-500 mr-2 mt-1"></i>
                                                <div>
                                                    <strong>Visibilitas Deskripsi</strong> - User dapat mengatur siapa yang dapat melihat deskripsi meeting (public atau hanya PIC yang diundang).
                                                </div>
                                            </li>
                                            <li class="flex items-start">
                                                <i class="fas fa-bell text-red-500 mr-2 mt-1"></i>
                                                <div>
                                                    <strong>Notifikasi Real-time</strong> - Notifikasi di dashboard akan ter-update secara otomatis setiap 10 detik tanpa perlu refresh halaman.
                                                </div>
                                            </li>
                                            <li class="flex items-start">
                                                <i class="fas fa-mobile-alt text-gray-500 mr-2 mt-1"></i>
                                                <div>
                                                    <strong>Responsive Design</strong> - Aplikasi dapat diakses dengan baik dari desktop, tablet, maupun smartphone.
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- 8. Tips dan Best Practices -->
                            <div class="mb-6 border-t border-gray-200 pt-6">
                                <h4 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                                    8. Tips dan Best Practices
                                </h4>
                                <div class="space-y-3 text-sm text-gray-700">
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <ul class="space-y-2">
                                            <li class="flex items-start">
                                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                                <div>
                                                    <strong>Buat booking lebih awal</strong> - Buat booking beberapa hari sebelum meeting untuk memastikan ruang tersedia dan admin memiliki waktu untuk approve.
                                                </div>
                                            </li>
                                            <li class="flex items-start">
                                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                                <div>
                                                    <strong>Periksa ketersediaan</strong> - Gunakan kalender di dashboard untuk melihat ketersediaan ruang sebelum membuat booking.
                                                </div>
                                            </li>
                                            <li class="flex items-start">
                                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                                <div>
                                                    <strong>Batalkan booking jika tidak digunakan</strong> - Jika meeting dibatalkan, segera batalkan booking agar ruang dapat digunakan oleh user lain.
                                                </div>
                                            </li>
                                            <li class="flex items-start">
                                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                                <div>
                                                    <strong>Periksa email secara berkala</strong> - Pastikan email Anda aktif dan periksa secara berkala untuk notifikasi penting tentang status booking.
                                                </div>
                                            </li>
                                            <li class="flex items-start">
                                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                                <div>
                                                    <strong>Isi detail meeting dengan lengkap</strong> - Isi judul dan deskripsi meeting dengan jelas agar admin dapat memahami tujuan meeting.
                                                </div>
                                            </li>
                                            <li class="flex items-start">
                                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                                <div>
                                                    <strong>Admin: Approve booking dengan cepat</strong> - Sebagai admin, approve atau reject booking secepat mungkin untuk memberikan pengalaman terbaik bagi user.
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- 9. Bantuan dan Kontak -->
                            <div class="mb-4 border-t border-gray-200 pt-6">
                                <h4 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-question-circle text-blue-500 mr-2"></i>
                                    9. Bantuan dan Kontak
                                </h4>
                                <div class="bg-blue-50 p-4 rounded-lg text-sm text-gray-700">
                                    <p class="mb-2">
                                        Jika Anda mengalami kesulitan atau memiliki pertanyaan tentang penggunaan aplikasi, silakan hubungi administrator sistem melalui:
                                    </p>
                                    <ul class="list-disc list-inside space-y-1 ml-2">
                                        <li>Email: Hubungi admin melalui email yang terdaftar</li>
                                        <li>WhatsApp: Gunakan tombol WhatsApp yang tersedia di pojok kanan bawah halaman</li>
                                        <li>Dashboard: Periksa notifikasi di dashboard untuk informasi penting</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <button type="button" onclick="closeGuideModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 w-full sm:w-auto">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            
            // Add event listener for ESC key
            document.addEventListener('keydown', function escHandler(e) {
                if (e.key === 'Escape') {
                    const modal = document.getElementById('guideModal');
                    if (modal && !modal.classList.contains('hidden')) {
                        closeGuideModal();
                        document.removeEventListener('keydown', escHandler);
                    }
                }
            });
        };

        window.closeGuideModal = function() {
            const modal = document.getElementById('guideModal');
            if (modal) {
                modal.remove();
            }
        };
    </script>
</body>
</html>

