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

@push('head')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@push('styles')
<style>
    /* Select2 styling to match form design */
    .select2-container--default .select2-selection--single {
        height: 42px;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        padding: 0;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 42px;
        padding-left: 12px;
        color: #000000;
    }
    
    
    
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #3b82f6;
        color: white;
    }
    
    .select2-dropdown {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
    }
    
    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #e5e7eb;
    }
    
    .select2-container--default .select2-results__option[aria-selected=true]:hover {
        background-color: #3b82f6;
        color: white;
    }
</style>
@endpush

@section('auth-content')
    <div class="rounded-2xl bg-white p-8 border border-gray-200 w-[700px] max-w-full">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-user-plus text-2xl text-indigo-600"></i>
            </div>
            <h1 class="text-2xl font-bold text-black mb-2">Daftar Akun Baru</h1>
            <p class="text-black">Buat akun untuk mengakses sistem booking ruang meeting</p>
        </div>

        @if ($errors->any())
            <div class="mt-3 mb-6 bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="mt-3 mb-6 bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" class="text-left" action="{{ route('register') }}" id="registerForm">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 mb-6 gap-x-4 gap-y-6 w-full">
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
            </div>
            <div class="grid grid-cols-1 gap-x-4 gap-y-6 w-full">
                <!-- Unit Kerja -->
                <div>
                    <label for="unit_kerja" class="block text-sm font-medium text-black mb-2">
                        Unit Kerja
                    </label>
                    <select id="unit_kerja" name="unit_kerja" class="w-full">
                        <option value="">Pilih Unit Kerja</option>
                        @foreach(($unitKerjaOptions ?? []) as $unitKerja)
                            <option value="{{ $unitKerja }}" {{ old('unit_kerja') == $unitKerja ? 'selected' : '' }}>{{ $unitKerja }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-black mb-2">
                        Password
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password" name="password" 
                           class="w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="Minimal 8 karakter, kombinasi huruf dan angka" required>
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

            <button type="submit" id="registerButton" class="w-full bg-blue-500 hover:bg-blue-800 !text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-300 mt-6 flex items-center justify-center border border-blue-300 disabled:opacity-50 disabled:cursor-not-allowed">
                <span id="registerButtonText">
                    <i class="fas fa-user-plus mr-2"></i>
                    Daftar Sekarang
                </span>
                <span id="registerButtonLoading" class="hidden">
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    Memproses...
                </span>
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

@push('scripts')
    <script>
        $(document).ready(function() {
            initSelect2();
        });

        function initSelect2() {
            if (typeof $ === 'undefined') {
                console.error('jQuery tidak dimuat.');
                return;
            }

            $('#unit_kerja').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih Unit Kerja',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "Tidak ada hasil";
                    },
                    searching: function() {
                        return "Mencari...";
                    }
                }
            });

            // Set the selected value if old input exists
            @if(old('unit_kerja'))
                $('#unit_kerja').val('{{ old('unit_kerja') }}').trigger('change');
            @endif
        }

    </script>
@endpush
