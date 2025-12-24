@extends("layouts.auth")

@php
    $pageTitle = "Login";
    $showLogo = true;
    $showFooter = true;
    $versionText = "Versi Aplikasi v2.1.7";
    $guideText = "Lihat Panduan";
    $showFooterLinks = true;
@endphp

@push("seo-meta")
    @include("components.seo-meta", [
        "page" => "login",
        "title" => "Masuk - SIRUPAT BGN - Sistem Informasi Ruang Rapat Badan Gizi Nasional",
        "description" =>
            "Masuk ke sistem pemesanan ruang meeting untuk mengelola jadwal meeting Anda. Akses mudah dan aman dengan berbagai metode login.",
        "keywords" => "login, masuk, sistem pemesanan, ruang meeting, autentikasi",
        "canonical" => "/login",
        "robots" => "noindex, nofollow",
    ])
@endpush

@push("head")
    <!-- Prevent caching of login page -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
@endpush

@section("auth-content")
    <!-- Login Form -->
    <div class="border bg-white border-gray-200 rounded-2xl p-6 w-[450px] max-w-full">
        <p class="text-black font-bold text-left">Silakan masuk untuk melanjutkan</p>
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mt-3 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span class="text-sm">{{ $errors->first() }}</span>
                </div>
            </div>
        @endif

        @if (session("success"))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mt-3 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span class="text-sm">{{ session("success") }}</span>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route("login") }}" class="space-y-6" id="loginForm">
            @csrf

            <!-- Username/Email Field -->
            <div>
                <label for="username" class="block text-black mb-2 text-left" style="text-align: left !important;">
                    Nama Pengguna atau Email
                </label>
                <input type="text" id="username" name="username" value="{{ old("username") }}"
                    class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg text-black placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300"
                    placeholder="Masukkan nama pengguna atau email" required autofocus>
            </div>

            <!-- Password Field -->
            <div>
                <label for="password" class="block text-black mb-2 text-left" style="text-align: left !important;">
                    Kata Sandi
                </label>
                <div class="relative">
                    <input type="password" id="password" name="password"
                        class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg text-black placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300 pr-12"
                        placeholder="Masukkan kata sandi" required>
                    <button type="button" onclick="togglePassword()"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 transition-colors">
                        <i id="password-icon" class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Login Button -->
            <button type="submit" id="loginButton"
                class="w-full bg-blue-500 !text-white font-semibold py-3 px-4 rounded-lg hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition-all duration-300 transform border border-blue-300 disabled:opacity-50 disabled:cursor-not-allowed">
                <span id="loginButtonText">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Masuk
                </span>
                <span id="loginButtonLoading" class="hidden">
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    Memproses...
                </span>
            </button>
        </form>

        <!-- Additional Links -->
        <div class="mt-6 flex justify-between items-center">
            <div class="text-center m-0">
                <a href="{{ route("password.request") }}" class="text-black hover:text-gray-800 text-sm underline">
                    Lupa Password?
                </a>
            </div>
            <div class="text-center m-0">
                <a href="{{ route("register") }}" class="text-black hover:text-gray-800 text-sm underline">
                    Daftar Akun Baru
                </a>
            </div>
        </div>
    </div>
@endsection

@push("scripts")
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
@endpush
