@extends('layouts.auth')

@php
    $pageTitle = 'Login';
    $showLogo = true;
    $showFooter = true;
    $versionText = 'Versi Aplikasi v2.1.7';
    $guideText = 'Lihat Panduan';
    $showFooterLinks = true;
@endphp

@push('seo-meta')
    @include('components.seo-meta', [
        'page' => 'login',
        'title' => 'Masuk - SIRUPAT BGN - Sistem Informasi Ruang Rapat Badan Gizi Nasional',
        'description' => 'Masuk ke sistem pemesanan ruang meeting untuk mengelola jadwal meeting Anda. Akses mudah dan aman dengan berbagai metode login.',
        'keywords' => 'login, masuk, sistem pemesanan, ruang meeting, autentikasi',
        'canonical' => '/login',
        'robots' => 'noindex, nofollow'
    ])
@endpush

@push('head')
    <!-- Prevent caching of login page -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
@endpush

@section('auth-content')
    <!-- Login Form -->
    <div class="border border-gray-200 rounded-2xl p-6 w-[450px] max-w-full">
        <p class="text-black font-bold text-left">Silakan masuk untuk melanjutkan</p>
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
                <label for="username" class="block text-black mb-2 text-left" style="text-align: left !important;">
                    Nama Pengguna atau Email
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
                <label for="password" class="block text-black mb-2 text-left" style="text-align: left !important;">
                    Kata Sandi
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
                class="w-full bg-blue-500 !text-white font-semibold py-3 px-4 rounded-lg hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition-all duration-300 transform border border-blue-300"
            >
                <i class="fas fa-sign-in-alt mr-2"></i>
                Masuk
            </button>
        </form>

        <!-- Additional Links -->
        <div class="mt-6 flex justify-between items-center">
            <div class="text-center m-0">
                <a href="{{ route('password.request') }}" class="text-black hover:text-gray-800 text-sm underline">
                    Lupa Password?
                </a>
            </div>
            <div class="text-center m-0">
                <a href="{{ route('register') }}" class="text-black hover:text-gray-800 text-sm underline">
                    Daftar Akun Baru
                </a>
            </div>
        </div>
    </div>
@endsection

@push('auth-scripts')
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
@endpush
