@extends('layouts.admin')

@section('title', 'Tambah Pengguna - Panel Admin')

@php
    $pageTitle = 'Tambah Pengguna';
@endphp

@push('styles')
<style>
    /* Form controls uniform styling */
    .form-control {
        background-color: #ffffff !important;
        color: #000000 !important;
        border: 1px solid #d1d5db !important;
        border-radius: 0.5rem;
    }
    .form-control:focus {
        outline: none !important;
        box-shadow: 0 0 0 2px rgba(99,102,241,0.2) !important;
        border-color: #6366f1 !important;
    }
    .form-control::placeholder { 
        color: #000000 !important; 
        opacity: 1; 
    }
    select.form-control option { 
        background: #ffffff; 
        color: #000000; 
    }
</style>
@endpush

@section('main-content')
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="border border-gray-200 rounded-2xl p-6 mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-black mb-2">Tambah Pengguna Baru</h2>
                    <p class="text-black">Buat akun pengguna baru</p>
                </div>
                <a href="{{ route('admin.users') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors duration-300 flex items-center">
                    <i data-feather="arrow-left" class="mr-2" style="width: 18px; height: 18px;"></i>Kembali ke Pengguna
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="border border-gray-200 rounded-2xl p-6">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                
                @if ($errors->any())
                    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-black mb-2">
                            Nama Pengguna
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" 
                               class="w-full px-3 py-2 form-control" 
                               placeholder="Masukkan nama pengguna" required>
                    </div>

                    <!-- Nama Lengkap -->
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-black mb-2">
                            Nama Lengkap     
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" 
                               class="w-full px-3 py-2 form-control" 
                               placeholder="Masukkan nama lengkap" required>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-black mb-2">
                            Email
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" 
                               class="w-full px-3 py-2 form-control" 
                               placeholder="Masukkan alamat email" required>
                    </div>

                    <!-- Telepon -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-black mb-2">Telepon</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" 
                               class="w-full px-3 py-2 form-control" 
                               placeholder="Masukkan nomor telepon">
                    </div>

                    <!-- Unit Kerja -->
                    <div>
                        <label for="unit_kerja" class="block text-sm font-medium text-black mb-2">Unit Kerja</label>
                        <select id="unit_kerja" name="unit_kerja" 
                                class="w-full px-3 py-2 form-control">
                            <option value="">Pilih Unit Kerja</option>
                            <option value="SEKRETARIAT UTAMA" {{ old('unit_kerja') == 'SEKRETARIAT UTAMA' ? 'selected' : '' }}>SEKRETARIAT UTAMA</option>
                            <option value="DEPUTI BIDANG PENYEDIAAN DAN PENYALURAN" {{ old('unit_kerja') == 'DEPUTI BIDANG PENYEDIAAN DAN PENYALURAN' ? 'selected' : '' }}>DEPUTI BIDANG PENYEDIAAN DAN PENYALURAN</option>
                            <option value="DEPUTI BIDANG PROMOSI DAN KERJA SAMA" {{ old('unit_kerja') == 'DEPUTI BIDANG PROMOSI DAN KERJA SAMA' ? 'selected' : '' }}>DEPUTI BIDANG PROMOSI DAN KERJA SAMA</option>
                            <option value="DEPUTI BIDANG SISTEM DAN TATA KELOLA" {{ old('unit_kerja') == 'DEPUTI BIDANG SISTEM DAN TATA KELOLA' ? 'selected' : '' }}>DEPUTI BIDANG SISTEM DAN TATA KELOLA</option>
                            <option value="DEPUTI BIDANG PEMANTAUAN DAN PENGAWASAN" {{ old('unit_kerja') == 'DEPUTI BIDANG PEMANTAUAN DAN PENGAWASAN' ? 'selected' : '' }}>DEPUTI BIDANG PEMANTAUAN DAN PENGAWASAN</option>
                            <option value="INSPEKTORAT UTAMA" {{ old('unit_kerja') == 'INSPEKTORAT UTAMA' ? 'selected' : '' }}>INSPEKTORAT UTAMA</option>
                            <option value="PUSAT DATA DAN SISTEM INFORMASI" {{ old('unit_kerja') == 'PUSAT DATA DAN SISTEM INFORMASI' ? 'selected' : '' }}>PUSAT DATA DAN SISTEM INFORMASI</option>
                            <option value="Pusdatin BGN" {{ old('unit_kerja') == 'Pusdatin BGN' ? 'selected' : '' }}>Pusdatin BGN</option>
                        </select>
                    </div>

                    <!-- Peran -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-black mb-2">
                            Role/Peran
                            <span class="text-red-500">*</span>
                        </label>
                        <select id="role" name="role" 
                                class="w-full px-3 py-2 form-control" required>
                            <option value="">Pilih peran</option>
                            <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Pengguna</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                        </select>
                    </div>
                </div>

                <!-- Kata Sandi -->
                <div class="mt-6">
                    <label for="password" class="block text-sm font-medium text-black mb-2">
                        Kata Sandi
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password" name="password" 
                           class="w-full px-3 py-2 form-control" 
                           placeholder="Masukkan kata sandi (minimal 8 karakter)" required>
                </div>

                <!-- Tombol Submit -->
                <div class="flex justify-end space-x-4 mt-8">
                    <a href="{{ route('admin.users') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors duration-300">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-300 flex items-center">
                        <i data-feather="save" class="mr-2" style="width: 18px; height: 18px;"></i>
                        Buat Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
