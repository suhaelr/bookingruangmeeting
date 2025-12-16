@extends('layouts.app')

@section('body-class', 'gradient-bg min-h-screen')

@push('styles')
<style>
    /* Background white */
    .gradient-bg {
        background: #ffffff !important;
        background: rgba(255, 255, 255, 0.9) !important;
        min-height: 100vh !important;
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
        
        .auth-container {
            min-height: 100vh;
            padding: 100px 0.5rem;
            align-items: center;
            justify-content: center;
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
        
        .auth-container {
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
    .auth-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 100px 0.5rem;
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
    
    /* Input styling */
    input[type="text"], input[type="email"], input[type="password"] {
        background-color: #ffffff !important;
        color: #000000 !important;
        border: 1px solid #d1d5db !important;
    }
    
    input::placeholder {
        color: #9ca3af !important;
    }
    
    input:focus {
        background-color: #ffffff !important;
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2) !important;
    }
    
    select {
        background-color: #ffffff !important;
        color: #000000 !important;
        border: 1px solid #d1d5db !important;
    }
</style>
@endpush

@section('content')
    <!-- GIF Background - Lazy loaded for performance -->
    {{-- <img src="{{ asset('3708555zcov227jtb.gif') }}" alt="Background Animation" class="gif-background" loading="lazy" onerror="console.log('GIF failed to load'); this.style.display='none';"> --}}
    
    <div class="auth-container">
        <div class="content-overlay w-[700px] max-w-full">
            @if(isset($showLogo) && $showLogo !== false)
            <!-- Logo/Header -->
            <div class="text-center mb-5 md:mb-8">
                <div class="logo-wrapper inline-flex items-center justify-center w-16 h-16 bg-white rounded-full shadow-lg mb-4">
                    <img src="{{ asset('logo-bgn.png') }}" alt="BGN Logo" class="w-12 h-12 object-contain logo-glow logo-blink">
                </div>
                <h1 class="text-3xl font-bold text-black mb-2">
                    <span class="font-medium block">Sistem Informasi Ruang Rapat (SIRUPAT)</span>
                    <span class="font-bold">Badan Gizi Nasional</span>
                </h1>
            </div>
            @endif

            @yield('auth-content')

            @if(isset($showFooter) && $showFooter !== false)
            <!-- Footer -->
            <div class="text-center mt-8">
                @if(isset($showFooterLinks) && $showFooterLinks !== false)
                <p class="text-black text-xs">
                    @if(isset($versionText))
                        <span onclick="showChangelogModal()" class="text-black font-medium cursor-pointer hover:text-gray-800 underline transition-colors duration-300">{{ $versionText }}</span>
                        <span class="mx-2">•</span>
                    @endif
                    @if(isset($guideText))
                        <span onclick="showGuideModal()" class="text-black font-medium cursor-pointer hover:text-gray-800 underline transition-colors duration-300">{{ $guideText }}</span>
                        <span class="mx-2">•</span>
                    @endif
                    <a href="{{ route('privacy.policy') }}" class="text-black font-medium hover:text-gray-800 underline transition-colors duration-300">
                        Kebijakan Privasi
                    </a>
                    <span class="mx-2">•</span>
                    <a href="{{ route('terms.service') }}" class="text-black font-medium hover:text-gray-800 underline transition-colors duration-300">
                        Syarat dan Ketentuan
                    </a>
                </p>
                @endif
                
                <p class="text-black text-sm mt-3">
                    @if(isset($copyrightText))
                        {{ $copyrightText }}
                    @else
                        © {{ date('Y') }} SIRUPAT BGN - Semua hak dilindungi.
                    @endif
                </p>
            </div>
            @endif
        </div>
    </div>

    <!-- WhatsApp Floating Button -->
    @include('components.whatsapp-float')

    @stack('modals')
@stop

@push('scripts')
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
                        <p class="text-sm text-gray-700 mb-4">
                            Panduan lengkap penggunaan aplikasi SIRUPAT BGN. Silakan baca dengan seksama untuk memahami fitur-fitur yang tersedia.
                        </p>
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-700">
                                <strong>Catatan:</strong> Panduan lengkap dapat diakses setelah login ke dalam sistem.
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end p-4 sm:p-6">
                        <button type="button" onclick="closeGuideModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 w-full sm:w-auto">
                            Tutup
                        </button>
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

