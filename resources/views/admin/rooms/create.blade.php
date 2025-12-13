@extends('layouts.admin')

@section('title', 'Tambah Ruang - Admin Panel')

@php
    $pageTitle = 'Tambah Ruang';
@endphp

@push('head')
<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
<link href="{{ asset('css/dropdown-fix.css') }}" rel="stylesheet">
@endpush

@push('styles')
<style>
    /* Uniform form control styling */
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
        <div class="glass-effect rounded-2xl p-6 mb-8 shadow-2xl">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-black mb-2">Tambah Ruang Meeting Baru</h2>
                    <p class="text-black">Buat ruang meeting baru</p>
                </div>
                <a href="{{ route('admin.rooms') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors duration-300 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke Ruang
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="glass-effect rounded-2xl p-6 shadow-2xl">
            <form method="POST" action="{{ route('admin.rooms.store') }}">
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
                    <!-- Room Nama -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-black mb-2">Nama Ruang *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" 
                               class="w-full px-3 py-2 form-control" 
                               placeholder="Masukkan nama ruang" required>
                    </div>

                    <!-- Kapasitas -->
                    <div>
                        <label for="capacity" class="block text-sm font-medium text-black mb-2">Kapasitas</label>
                        <input type="number" id="capacity" name="capacity" value="{{ old('capacity') }}" 
                               class="w-full px-3 py-2 form-control" 
                               placeholder="Masukkan kapasitas (opsional)" min="1">
                    </div>

                    <!-- Lokasi -->
                    <div>
                        <label for="location" class="block text-sm font-medium text-black mb-2">Lokasi *</label>
                        <input type="text" id="location" name="location" value="{{ old('location') }}" 
                               class="w-full px-3 py-2 form-control" 
                               placeholder="Masukkan lokasi" required>
                    </div>
                </div>

                <!-- Deskripsi -->
                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-black mb-2">Deskripsi</label>
                    <textarea id="description" name="description" rows="3" 
                              class="w-full px-3 py-2 form-control" 
                              placeholder="Masukkan deskripsi ruang">{{ old('description') }}</textarea>
                </div>

                <!-- Amenities -->
                <div class="mt-6">
                    <label for="amenities" class="block text-sm font-medium text-black mb-2">Fasilitas</label>
                    <input type="text" id="amenities" name="amenities" value="{{ old('amenities') }}" 
                           class="w-full px-3 py-2 form-control" 
                           placeholder="projector, whiteboard, wifi, ac, sound_system">
                    <p class="text-black text-sm mt-1">Pisahkan fasilitas dengan koma</p>
                </div>

                <!-- Status -->
                <div class="mt-6">
                    <label for="is_active" class="block text-sm font-medium text-black mb-2">Status *</label>
                    <select id="is_active" name="is_active" 
                            class="w-full px-3 py-2 form-control" required>
                        <option value="">Pilih status</option>
                        <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>

                <!-- Kirim Button -->
                <div class="flex justify-end space-x-4 mt-8">
                    <a href="{{ route('admin.rooms') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors duration-300">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-300 flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Buat Ruang
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
