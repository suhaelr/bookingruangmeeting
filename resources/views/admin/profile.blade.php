@extends('layouts.admin')

@section('title', 'Profil Admin - Sistem Pemesanan Ruang Meeting')

@php
    $pageTitle = 'Profil Admin';
@endphp

@push('styles')
<style>
    .form-control { 
        background: #ffffff !important; 
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
    
    /* Select2 styling to match form design */
    .select2-container--default .select2-selection--single {
        height: 48px;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        padding: 0;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 48px;
        padding-left: 16px;
        color: #000000;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px;
        right: 10px;
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

@section('main-content')
    <div class="max-w-4xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Profile Card -->
            <div class="lg:col-span-1">
                <div class="border border-gray-200 rounded-2xl p-6">
                    <div class="text-center">
                        <div class="w-24 h-24 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-feather="user" class="text-black" style="width: 48px; height: 48px;"></i>
                        </div>
                        <h3 class="text-xl font-bold text-black mb-2">{{ $user['full_name'] ?? 'Administrator' }}</h3>
                        <p class="text-black mb-1">{{ $user['email'] ?? 'N/A' }}</p>
                        <p class="text-black text-sm">{{ $user['unit_kerja'] ?? $user['department'] ?? 'N/A' }}</p>
                        <span class="inline-block px-3 py-1 bg-purple-500/20 text-purple-700 text-xs rounded-full mt-2">
                            Administrator
                        </span>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="border border-gray-200 rounded-2xl p-6 mt-6">
                    <h4 class="text-lg font-bold text-black mb-4">Statistik Singkat</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-black text-sm">Anggota Sejak</span>
                            <span class="text-black text-sm">{{ now()->format('M Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-black text-sm">Login Terakhir</span>
                            <span class="text-black text-sm">{{ now()->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-black text-sm">Role</span>
                            <span class="text-black text-sm">Admin</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Form -->
            <div class="lg:col-span-2">
                <div class="border border-gray-200 rounded-2xl p-8">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-black mb-2">Edit Profil</h2>
                        <p class="text-black">Perbarui informasi personal Anda</p>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                            <div class="flex items-center">
                                <i data-feather="check-circle" class="mr-2" style="width: 20px; height: 20px;"></i>
                                {{ session('success') }}
                            </div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                            <div class="flex items-center">
                                <i data-feather="alert-circle" class="mr-2" style="width: 20px; height: 20px;"></i>
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

                    <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-6">
                        @csrf
                        
                        <!-- Full Nama -->
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-black mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $user['full_name'] ?? '') }}" required
                                   class="w-full px-4 py-3 form-control"
                                   placeholder="Masukkan nama lengkap Anda">
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-black mb-2">
                                Alamat Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user['email'] ?? '') }}" required
                                   class="w-full px-4 py-3 form-control"
                                   placeholder="Masukkan alamat email Anda">
                        </div>

                        <!-- Telepon -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-black mb-2">
                                Nomor Telepon
                            </label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone', $user['phone'] ?? '') }}"
                                   class="w-full px-4 py-3 form-control"
                                   placeholder="Masukkan nomor telepon Anda">
                        </div>

                        <!-- Unit Kerja -->
                        <div>
                            <label for="unit_kerja" class="block text-sm font-medium text-black mb-2">
                                Unit Kerja
                            </label>
                            <select id="unit_kerja" name="unit_kerja" class="w-full">
                                <option value="">Pilih Unit Kerja</option>
                                <option value="SEKRETARIAT UTAMA" {{ old('unit_kerja', $user['unit_kerja'] ?? $user['department'] ?? '') == 'SEKRETARIAT UTAMA' ? 'selected' : '' }}>SEKRETARIAT UTAMA</option>
                                <option value="DEPUTI BIDANG PENYEDIAAN DAN PENYALURAN" {{ old('unit_kerja', $user['unit_kerja'] ?? $user['department'] ?? '') == 'DEPUTI BIDANG PENYEDIAAN DAN PENYALURAN' ? 'selected' : '' }}>DEPUTI BIDANG PENYEDIAAN DAN PENYALURAN</option>
                                <option value="DEPUTI BIDANG PROMOSI DAN KERJA SAMA" {{ old('unit_kerja', $user['unit_kerja'] ?? $user['department'] ?? '') == 'DEPUTI BIDANG PROMOSI DAN KERJA SAMA' ? 'selected' : '' }}>DEPUTI BIDANG PROMOSI DAN KERJA SAMA</option>
                                <option value="DEPUTI BIDANG SISTEM DAN TATA KELOLA" {{ old('unit_kerja', $user['unit_kerja'] ?? $user['department'] ?? '') == 'DEPUTI BIDANG SISTEM DAN TATA KELOLA' ? 'selected' : '' }}>DEPUTI BIDANG SISTEM DAN TATA KELOLA</option>
                                <option value="DEPUTI BIDANG PEMANTAUAN DAN PENGAWASAN" {{ old('unit_kerja', $user['unit_kerja'] ?? $user['department'] ?? '') == 'DEPUTI BIDANG PEMANTAUAN DAN PENGAWASAN' ? 'selected' : '' }}>DEPUTI BIDANG PEMANTAUAN DAN PENGAWASAN</option>
                                <option value="INSPEKTORAT UTAMA" {{ old('unit_kerja', $user['unit_kerja'] ?? $user['department'] ?? '') == 'INSPEKTORAT UTAMA' ? 'selected' : '' }}>INSPEKTORAT UTAMA</option>
                                <option value="PUSAT DATA DAN SISTEM INFORMASI" {{ old('unit_kerja', $user['unit_kerja'] ?? $user['department'] ?? '') == 'PUSAT DATA DAN SISTEM INFORMASI' ? 'selected' : '' }}>PUSAT DATA DAN SISTEM INFORMASI</option>
                            </select>
                        </div>

                        <!-- Kirim Button -->
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('admin.dashboard') }}" 
                               class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-300">
                                Batal
                            </a>
                            <button type="submit" 
                                    id="submitBtn"
                                    class="px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-300 flex items-center disabled:opacity-50 disabled:cursor-not-allowed">
                                <i data-feather="save" class="mr-2" id="saveIcon" style="width: 18px; height: 18px;"></i>
                                <i data-feather="loader" class="mr-2 hidden animate-spin" id="loadingIcon" style="width: 18px; height: 18px;"></i>
                                <span id="buttonText">Perbarui Profil</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
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
        @if(old('unit_kerja', $user['unit_kerja'] ?? $user['department'] ?? ''))
            $('#unit_kerja').val('{{ old('unit_kerja', $user['unit_kerja'] ?? $user['department'] ?? '') }}').trigger('change');
        @endif
    }

    // Auto-hide success message
    setTimeout(() => {
        const successMessage = document.querySelector('.bg-green-100');
        if (successMessage) {
            successMessage.style.transition = 'opacity 0.5s';
            successMessage.style.opacity = '0';
            setTimeout(() => successMessage.remove(), 500);
        }
    }, 5000);

    // Form validation and loading state
    document.querySelector('form[action="{{ route("admin.profile.update") }}"]').addEventListener('submit', function(e) {
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
        
        // Show loading state
        const submitBtn = document.getElementById('submitBtn');
        const saveIcon = document.getElementById('saveIcon');
        const loadingIcon = document.getElementById('loadingIcon');
        const buttonText = document.getElementById('buttonText');
        
        submitBtn.disabled = true;
        saveIcon.classList.add('hidden');
        loadingIcon.classList.remove('hidden');
        buttonText.textContent = 'Memproses...';
    });
</script>
@endpush
