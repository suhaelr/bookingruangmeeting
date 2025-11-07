<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profil - Sistem Pemesanan Ruang Meeting</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/dropdown-fix.css') }}" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .form-control { background: #ffffff !important; color: #000000 !important; border: 1px solid #d1d5db !important; border-radius: 0.5rem; }
        .form-control:focus { outline: none !important; box-shadow: 0 0 0 2px rgba(99,102,241,0.2) !important; border-color: #6366f1 !important; }
        .form-control::placeholder { color: #000000 !important; opacity: 1; }
        select.form-control option { background: #ffffff; color: #000000; }
    </style>
</head>
<body class="min-h-screen bg-white">
    <!-- Navigation -->
        <nav class="glass-effect shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <button onclick="toggleMobileSidebar()" class="mobile-menu-btn mr-4">
                            <i class="fas fa-bars"></i>
                        </button>
                        <div class="flex-shrink-0">
                            <i class="fas fa-calendar-alt text-2xl text-white"></i>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('logout') }}" 
                           class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors duration-300 flex items-center">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Keluar
                        </a>
                    </div>
                </div>
            </div>
        </nav>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Profile Card -->
            <div class="lg:col-span-1">
                <div class="glass-effect rounded-2xl p-6 shadow-2xl">
                    <div class="text-center">
                        <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user text-black text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-black mb-2">{{ $user['full_name'] }}</h3>
                        <p class="text-black mb-1">{{ $user['email'] }}</p>
                        <p class="text-black text-sm">{{ $user['unit_kerja'] ?? 'N/A' }}</p>
                        <span class="inline-block px-3 py-1 bg-blue-500/20 text-blue-300 text-xs rounded-full mt-2">
                            {{ ucfirst($user['role']) }}
                        </span>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="glass-effect rounded-2xl p-6 shadow-2xl mt-6">
                    <h4 class="text-lg font-bold text-black mb-4">Statistik Singkat</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-black text-sm">Anggota Sejak</span>
                            <span class="text-black text-sm">Jan 2024</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-black text-sm">Login Terakhir</span>
                            <span class="text-black text-sm">{{ now()->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-black text-sm">Total Pemesanan</span>
                            <span class="text-black text-sm">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Form -->
            <div class="lg:col-span-2">
                <div class="glass-effect rounded-2xl p-8 shadow-2xl">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-black mb-2">Edit Profil</h2>
                        <p class="text-black">Perbarui informasi personal Anda</p>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                {{ session('success') }}
                            </div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                <div>
                                    <strong>Silakan perbaiki kesalahan berikut:</strong>
                                    <ul class="mt-1 list-disc list-inside">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('user.profile.update') }}" class="space-y-6">
                        @csrf
                        
                        <!-- Full Nama -->
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-black mb-2">
                                <i class="fas fa-user mr-2"></i>Nama Lengkap *
                            </label>
                            <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $user['full_name']) }}" required
                                   class="w-full px-4 py-3 form-control"
                                   placeholder="Masukkan nama lengkap Anda">
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-black mb-2">
                                <i class="fas fa-envelope mr-2"></i>Alamat Email *
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user['email']) }}" required
                                   class="w-full px-4 py-3 form-control"
                                   placeholder="Masukkan alamat email Anda">
                        </div>

                        <!-- Telepon -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-black mb-2">
                                <i class="fas fa-phone mr-2"></i>Nomor Telepon
                            </label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone', $user['phone'] ?? '') }}"
                                   class="w-full px-4 py-3 form-control"
                                   placeholder="Masukkan nomor telepon Anda">
                        </div>

                        <!-- Unit Kerja -->
                        <div>
                            <label for="unit_kerja" class="block text-sm font-medium text-black mb-2">
                                <i class="fas fa-building mr-2"></i>Unit Kerja
                            </label>
                            <input type="text" id="unit_kerja" name="unit_kerja" value="{{ old('unit_kerja', $user['unit_kerja'] ?? '') }}"
                                   class="w-full px-4 py-3 form-control"
                                   placeholder="Masukkan unit kerja Anda">
                        </div>

                        <!-- Kirim Button -->
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('user.dashboard') }}" 
                               class="px-6 py-3 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-colors duration-300">
                                Batal
                            </a>
                            <button type="submit" 
                                    class="px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-300 flex items-center">
                                <i class="fas fa-save mr-2"></i>
                                Perbarui Profile
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>


    <script>
        // Auto-hide success message
        setTimeout(() => {
            const successMessage = document.querySelector('.bg-green-100');
            if (successMessage) {
                successMessage.style.transition = 'opacity 0.5s';
                successMessage.style.opacity = '0';
                setTimeout(() => successMessage.remove(), 500);
            }
        }, 5000);

        // Form validation
        document.querySelector('form[action="{{ route("user.profile.update") }}"]').addEventListener('submit', function(e) {
            const fullNama = document.getElementById('full_name').value.trim();
            const email = document.getElementById('email').value.trim();
            
            if (!fullNama) {
                e.preventDefault();
                alert('Silakan masukkan nama lengkap Anda');
                return;
            }
            
            if (!email || !email.includes('@')) {
                e.preventDefault();
                alert('Silakan masukkan alamat email yang valid');
                return;
            }
        });
    </script>

    <!-- Mobile Sidebar -->
    @include('components.mobile-sidebar', [
        'userRole' => 'user',
        'userName' => session('user_data.full_name'),
        'userEmail' => session('user_data.email'),
        'pageTitle' => 'Profil'
    ])

    <!-- WhatsApp Floating Button -->
    @include('components.whatsapp-float')
</body>
</html>
