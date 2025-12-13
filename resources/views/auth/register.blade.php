@extends('layouts.auth')

@php
    $pageTitle = 'Daftar';
    $showLogo = true;
    $showFooter = false;
@endphp

@push('seo-meta')
    @include('components.seo-meta', [
        'page' => 'register',
        'title' => 'Daftar - Sistem Pemesanan Ruang Meeting',
        'description' => 'Daftar akun baru untuk mengakses sistem pemesanan ruang meeting. Proses pendaftaran mudah dan cepat.',
        'keywords' => 'daftar, registrasi, akun baru, sistem pemesanan, ruang meeting',
        'canonical' => '/register',
        'robots' => 'noindex, nofollow'
    ])
@endpush

@section('auth-content')
    <div class="rounded-2xl p-8 border border-gray-200 w-[700px] max-w-full">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-user-plus text-2xl text-indigo-600"></i>
            </div>
            <h1 class="text-2xl font-bold text-black mb-2">Daftar Akun Baru</h1>
            <p class="text-black">Buat akun untuk mengakses sistem booking ruang meeting</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" class="text-left" action="{{ route('register') }}">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6 w-full">
                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-black mb-2">
                        Username
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" 
                           class="w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="Masukkan username" required>
                </div>

                <!-- Full Name -->
                <div>
                    <label for="full_name" class="block text-sm font-medium text-black mb-2">
                        Nama Lengkap
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" 
                           class="w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="Masukkan nama lengkap" required>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-black mb-2">
                        Email
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" 
                           class="w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="Masukkan email" required>
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-black mb-2">No. Telepon</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" 
                           class="w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="Masukkan nomor telepon">
                </div>

                <!-- Unit Kerja -->
                <div>
                    <label for="unit_kerja" class="block text-sm font-medium text-black mb-2">
                        Unit Kerja
                    </label>
                    <div class="relative">
                        <select id="unit_kerja" name="unit_kerja"
                                class="w-full px-3 py-2 pr-10 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none cursor-pointer bg-white border border-gray-300">
                            <option value="">Pilih Unit Kerja</option>
                            <option value="SEKRETARIAT UTAMA" {{ old('unit_kerja') == 'SEKRETARIAT UTAMA' ? 'selected' : '' }}>SEKRETARIAT UTAMA</option>
                            <option value="DEPUTI BIDANG PENYEDIAAN DAN PENYALURAN" {{ old('unit_kerja') == 'DEPUTI BIDANG PENYEDIAAN DAN PENYALURAN' ? 'selected' : '' }}>DEPUTI BIDANG PENYEDIAAN DAN PENYALURAN</option>
                            <option value="DEPUTI BIDANG PROMOSI DAN KERJA SAMA" {{ old('unit_kerja') == 'DEPUTI BIDANG PROMOSI DAN KERJA SAMA' ? 'selected' : '' }}>DEPUTI BIDANG PROMOSI DAN KERJA SAMA</option>
                            <option value="DEPUTI BIDANG SISTEM DAN TATA KELOLA" {{ old('unit_kerja') == 'DEPUTI BIDANG SISTEM DAN TATA KELOLA' ? 'selected' : '' }}>DEPUTI BIDANG SISTEM DAN TATA KELOLA</option>
                            <option value="DEPUTI BIDANG PEMANTAUAN DAN PENGAWASAN" {{ old('unit_kerja') == 'DEPUTI BIDANG PEMANTAUAN DAN PENGAWASAN' ? 'selected' : '' }}>DEPUTI BIDANG PEMANTAUAN DAN PENGAWASAN</option>
                            <option value="INSPEKTORAT UTAMA" {{ old('unit_kerja') == 'INSPEKTORAT UTAMA' ? 'selected' : '' }}>INSPEKTORAT UTAMA</option>
                            <option value="PUSAT DATA DAN SISTEM INFORMASI" {{ old('unit_kerja') == 'PUSAT DATA DAN SISTEM INFORMASI' ? 'selected' : '' }}>PUSAT DATA DAN SISTEM INFORMASI</option>
                        </select>
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-500"></i>
                        </div>
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-black mb-2">
                        Password
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password" name="password" 
                           class="w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="Minimal 8 karakter" required>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-black mb-2">
                        Konfirmasi Password
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation" 
                           class="w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="Ulangi password" required>
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-800 !text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-300 mt-6 flex items-center justify-center border border-blue-300">
                <i class="fas fa-user-plus mr-2"></i>
                Daftar Sekarang
            </button>
        </form>

        <div class="text-center mt-6">
            <p class="text-black text-sm">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="text-black hover:text-gray-800 font-semibold underline">
                    Login di sini
                </a>
            </p>
        </div>
    </div>
@endsection
