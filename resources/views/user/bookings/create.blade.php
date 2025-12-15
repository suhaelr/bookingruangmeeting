@extends('layouts.user')

@section('title', 'Pesan Ruang Meeting - Sistem Pemesanan Ruang Meeting')

@php
    $pageTitle = 'Pemesanan Baru';
@endphp

@push('styles')
<style>
        /* Ensure buttons are clickable */
        button, .btn, [role="button"] {
            cursor: pointer !important;
            pointer-events: auto !important;
            user-select: none;
        }
        
        /* Ensure form elements are interactive */
        select, input, textarea {
            pointer-events: auto !important;
        }
        
        /* Uniform field styling */
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
        .form-control::placeholder { color: #000000 !important; opacity: 1; }
        select.form-control option { background: #ffffff; color: #000000; }
        
        /* Ensure proper z-index for overlays */
        .glass-effect {
            position: relative;
            z-index: 1;
        }
        
        /* Fix button hover states */
        button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
    </style>
@endpush

@section('main-content')
    <div class="max-w-4xl mx-auto">
        <div class="border border-gray-200 rounded-2xl p-8">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-black mb-2">Pesan Ruang Meeting</h2>
                <p class="text-black">Isi detail di bawah untuk memesan ruang meeting Anda</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg mb-6">
                    <div class="flex items-start">
                        <i data-feather="alert-circle" class="mr-3 mt-1" style="width: 20px; height: 20px;"></i>
                        <div class="flex-1">
                            <h4 class="font-semibold mb-2">Gagal Membuat Booking</h4>
                            <div class="space-y-2">
                                @foreach ($errors->all() as $error)
                                    <div class="text-sm whitespace-pre-line leading-relaxed">{{ $error }}</div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('user.bookings.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <!-- Meeting Room Selection -->
                <div>
                    <label for="meeting_room_id" class="block text-sm font-medium text-black mb-2">
                        <i data-feather="box" class="mr-2 inline" style="width: 18px; height: 18px;"></i>
                        Ruang Meeting 
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select id="meeting_room_id" name="meeting_room_id" required
                                class="w-full px-4 py-3 pr-10 form-control appearance-none cursor-pointer">
                            <option value="">Pilih ruang meeting</option>
                            @foreach($rooms as $room)
                            <option value="{{ $room->id }}" 
                                    data-capacity="{{ $room->capacity ?? 0 }}"
                                    data-amenities="{{ json_encode($room->getAmenitiesList()) }}"
                                    {{ old('meeting_room_id') == $room->id ? 'selected' : '' }}>
                                {{ $room->name }} - {{ $room->location }}@if($room->capacity && $room->capacity > 0) ({{ $room->capacity }} kursi)@endif
                            </option>
                            @endforeach
                        </select>
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                            <i data-feather="chevron-down" class="text-gray-500" style="width: 18px; height: 18px;"></i>
                        </div>
                    </div>
                </div>

                <!-- Room Details Display -->
                <div id="room-details" class="hidden bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <h4 class="text-black font-medium mb-2">Detail Ruang</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div id="room-capacity-container" class="hidden">
                            <span class="text-gray-600">Kapasitas:</span>
                            <span id="room-capacity" class="text-black ml-2"></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Fasilitas:</span>
                            <span id="room-amenities" class="text-black ml-2"></span>
                        </div>
                    </div>
                </div>

                <!-- Meeting Judul -->
                <div>
                    <label for="title" class="block text-sm font-medium text-black mb-2">
                        <i data-feather="type" class="mr-2 inline" style="width: 18px; height: 18px;"></i>
                        Judul Meeting
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required
                           class="w-full px-4 py-3 form-control"
                           placeholder="Masukkan judul pertemuan">
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="description" class="block text-sm font-medium text-black mb-2">
                        <i data-feather="file-text" class="mr-2 inline" style="width: 18px; height: 18px;"></i>Deskripsi
                    </label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-4 py-3 form-control"
                              placeholder="Masukkan deskripsi meeting, link Zoom/Google Meet, atau informasi penting lainnya">{{ old('description') }}</textarea>
                    <p class="text-gray-600 text-xs mt-1">Anda dapat memasukkan link Zoom, Google Meet, atau informasi penting lainnya</p>
                </div>

                <!-- PIC Invitations Section -->
                <div class="pic-invitations-section">
                    <label class="block text-sm font-medium text-black mb-2">
                        <i data-feather="users" class="mr-2 inline" style="width: 18px; height: 18px;"></i>Undang PIC Lain
                    </label>
                    
                    <!-- Visibility Setting -->
                    <div class="mb-4">
                        <label class="flex items-center text-black mb-2 cursor-pointer">
                            <input type="radio" name="description_visibility" value="invited_pics_only" checked class="mr-2" id="visibility_invited_only">
                            Hanya PIC yang diundang dapat melihat deskripsi
                        </label>
                        <label class="flex items-center text-black cursor-pointer">
                            <input type="radio" name="description_visibility" value="public" class="mr-2" id="visibility_public">
                            Semua PIC dapat melihat deskripsi
                        </label>
                    </div>
                    
                    <!-- PIC Selection -->
                    <div class="bg-gray-50 rounded-lg p-4 max-h-60 overflow-y-auto border border-gray-200">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-black font-medium">Pilih PIC yang akan diundang:</h4>
                            <button type="button" id="clear-all-pics" class="text-sm text-red-600 hover:text-red-700 font-medium px-3 py-1 rounded hover:bg-red-50 transition-colors duration-200">
                                <i data-feather="x-circle" class="mr-1 inline" style="width: 16px; height: 16px;"></i>Hapus Semua Pilihan
                            </button>
                        </div>
                        @foreach($allPics as $pic)
                            @if($pic->id !== auth()->id())
                            <label class="flex items-center text-black mb-2 cursor-pointer hover:bg-gray-100 p-2 rounded">
                                <input type="checkbox" name="invited_pics[]" value="{{ $pic->id }}" class="mr-3 pic-checkbox">
                                <div>
                                    <div class="font-medium text-black">{{ $pic->full_name }}</div>
                                    <div class="text-sm text-gray-600">{{ $pic->unit_kerja }}</div>
                                </div>
                            </label>
                            @endif
                        @endforeach
                    </div>
                    
                    <p class="text-gray-600 text-xs mt-2">
                        <i data-feather="info" class="mr-1 inline" style="width: 16px; height: 16px;"></i>
                        PIC yang diundang akan menerima notifikasi dan dapat melihat deskripsi meeting (termasuk link Zoom/Meet) di kalender mereka.
                    </p>
                </div>

                <!-- Tanggal and Waktu -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-black mb-2">
                            <i data-feather="clock" class="mr-2 inline" style="width: 18px; height: 18px;"></i>
                            Waktu Mulai
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" id="start_time" name="start_time" value="{{ old('start_time') }}" required
                               class="w-full px-4 py-3 form-control">
                    </div>
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-black mb-2">
                            <i data-feather="clock" class="mr-2 inline" style="width: 18px; height: 18px;"></i>
                            Waktu Selesai
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" id="end_time" name="end_time" value="{{ old('end_time') }}" required
                               class="w-full px-4 py-3 form-control">
                    </div>
                </div>

                <!-- Unit Kerja -->
                <div>
                    <label for="unit_kerja" class="block text-sm font-medium text-black mb-2">
                        <i data-feather="building" class="mr-2 inline" style="width: 18px; height: 18px;"></i>
                        Unit Kerja
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select id="unit_kerja" name="unit_kerja" required
                                class="w-full px-4 py-3 pr-10 form-control appearance-none cursor-pointer">
                            <option value="">Pilih Unit Kerja</option>
                            <option value="SEKRETARIAT UTAMA" {{ old('unit_kerja', isset($userUnitKerja) && $userUnitKerja ? $userUnitKerja : '') == 'SEKRETARIAT UTAMA' ? 'selected' : '' }}>SEKRETARIAT UTAMA</option>
                            <option value="DEPUTI BIDANG PENYEDIAAN DAN PENYALURAN" {{ old('unit_kerja', isset($userUnitKerja) && $userUnitKerja ? $userUnitKerja : '') == 'DEPUTI BIDANG PENYEDIAAN DAN PENYALURAN' ? 'selected' : '' }}>DEPUTI BIDANG PENYEDIAAN DAN PENYALURAN</option>
                            <option value="DEPUTI BIDANG PROMOSI DAN KERJA SAMA" {{ old('unit_kerja', isset($userUnitKerja) && $userUnitKerja ? $userUnitKerja : '') == 'DEPUTI BIDANG PROMOSI DAN KERJA SAMA' ? 'selected' : '' }}>DEPUTI BIDANG PROMOSI DAN KERJA SAMA</option>
                            <option value="DEPUTI BIDANG SISTEM DAN TATA KELOLA" {{ old('unit_kerja', isset($userUnitKerja) && $userUnitKerja ? $userUnitKerja : '') == 'DEPUTI BIDANG SISTEM DAN TATA KELOLA' ? 'selected' : '' }}>DEPUTI BIDANG SISTEM DAN TATA KELOLA</option>
                            <option value="DEPUTI BIDANG PEMANTAUAN DAN PENGAWASAN" {{ old('unit_kerja', isset($userUnitKerja) && $userUnitKerja ? $userUnitKerja : '') == 'DEPUTI BIDANG PEMANTAUAN DAN PENGAWASAN' ? 'selected' : '' }}>DEPUTI BIDANG PEMANTAUAN DAN PENGAWASAN</option>
                            <option value="INSPEKTORAT UTAMA" {{ old('unit_kerja', isset($userUnitKerja) && $userUnitKerja ? $userUnitKerja : '') == 'INSPEKTORAT UTAMA' ? 'selected' : '' }}>INSPEKTORAT UTAMA</option>
                            <option value="PUSAT DATA DAN SISTEM INFORMASI" {{ old('unit_kerja', isset($userUnitKerja) && $userUnitKerja ? $userUnitKerja : '') == 'PUSAT DATA DAN SISTEM INFORMASI' ? 'selected' : '' }}>PUSAT DATA DAN SISTEM INFORMASI</option>
                        </select>
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                            <i data-feather="chevron-down" class="text-gray-500" style="width: 18px; height: 18px;"></i>
                        </div>
                    </div>
                </div>

                <!-- Dokumen Tambahan (Opsional) -->
                <div>
                    <label for="dokumen_perizinan" class="block text-sm font-medium text-black mb-2">
                        <i data-feather="file" class="mr-2 inline" style="width: 18px; height: 18px;"></i>Dokumen Tambahan (Opsional) (PDF, Max 2MB)
                    </label>
                    
                    <!-- File Input -->
                    <div class="relative">
                        <input type="file" id="dokumen_perizinan" name="dokumen_perizinan" accept=".pdf" 
                               class="w-full px-4 py-3 form-control"
                               onchange="handleFileSelect(this)">
                        <input type="hidden" id="dokumen_perizinan_data" name="dokumen_perizinan_data" value="{{ old('dokumen_perizinan_data') }}">
                    </div>
                    
                    <!-- File Preview -->
                    <div id="file-preview" class="hidden mt-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i data-feather="file" class="text-red-500 mr-2" style="width: 18px; height: 18px;"></i>
                                <span id="file-name" class="text-black text-sm"></span>
                                <span id="file-size" class="text-gray-600 text-xs ml-2"></span>
                            </div>
                            <button type="button" onclick="removeFile()" class="text-red-500 hover:text-red-600 transition-colors">
                                <i data-feather="x" style="width: 18px; height: 18px;"></i>
                            </button>
                        </div>
                    </div>
                    
                    <p class="text-gray-600 text-xs mt-1">Opsional: unggah dokumen pendukung dalam format PDF (maksimal 2MB)</p>
                </div>

                <!-- Captcha Section -->
                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-black mb-4 flex items-center">
                        <i data-feather="shield" class="mr-2" style="width: 20px; height: 20px;"></i>
                        Verifikasi Keamanan
                    </h3>
                    
                    <!-- Mobile-friendly vertical layout -->
                    <div class="space-y-4">
                        <!-- Captcha Question -->
                        <div>
                            <label class="block text-sm font-medium text-black mb-2">
                                <i data-feather="key" class="mr-2 inline" style="width: 18px; height: 18px;"></i>Masukkan angka berikut:
                            </label>
                            <div class="flex items-center justify-center space-x-3">
                                <div id="captcha-question" class="text-xl font-bold text-black bg-gray-50 px-4 py-4 rounded-lg border border-gray-200 text-center tracking-widest min-w-[100px] max-w-[150px]">
                                    Loading...
                                </div>
                                <button type="button" id="refresh-captcha" class="px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-300">
                                    <i data-feather="refresh-cw" style="width: 18px; height: 18px;"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Captcha Answer -->
                        <div>
                            <label for="captcha_answer" class="block text-sm font-medium text-black mb-2">
                                Jawaban:
                            </label>
                            <input type="text" id="captcha_answer" name="captcha_answer" required
                                   class="w-full px-4 py-3 form-control text-center text-lg font-mono tracking-widest"
                                   placeholder="4 digit" maxlength="4">
                        </div>
                    </div>
                    
                    <p class="text-gray-600 text-xs mt-3 text-center">
                        <i data-feather="info" class="mr-1 inline" style="width: 16px; height: 16px;"></i>
                        Silakan masukkan 4 digit angka yang ditampilkan di atas untuk melanjutkan pemesanan.
                    </p>
                </div>

                <!-- Kirim Button -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('user.dashboard') }}" 
                       class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-300">
                        Batal
                    </a>
                    <button type="submit" id="submit-booking-btn"
                            class="px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-300 flex items-center">
                        <i data-feather="calendar" class="mr-2" style="width: 18px; height: 18px;"></i>
                        Pesan Ruang Meeting
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // File handling
            let selectedFile = null;
            
            // Check if there's a previously uploaded file
            const existingFileData = document.getElementById('dokumen_perizinan_data').value;
            if (existingFileData) {
                try {
                    const fileData = JSON.parse(existingFileData);
                    showFilePreview(fileData.name, fileData.size);
                } catch (e) {
                    console.log('No existing file data');
                }
            }
            
            // Captcha functionality
            let captchaVerified = false;
            
            // Load captcha on page load
            loadCaptcha();
            
            // Refresh captcha button
            document.getElementById('refresh-captcha').addEventListener('click', function() {
                loadCaptcha();
            });
            
            // Captcha verification on input change
            document.getElementById('captcha_answer').addEventListener('input', function() {
                verifyCaptcha();
            });
            
            function loadCaptcha() {
                fetch('{{ route("captcha.generate") }}')
                    .then(response => response.json())
                    .then(data => {
                        // Display only the number
                        document.getElementById('captcha-question').textContent = data.question;
                        captchaVerified = false;
                        document.getElementById('captcha_answer').value = '';
                    })
                    .catch(error => {
                        console.error('Error loading captcha:', error);
                        document.getElementById('captcha-question').textContent = 'Error loading captcha';
                    });
            }
            
            function verifyCaptcha() {
                const answer = document.getElementById('captcha_answer').value;
                if (answer) {
                    fetch('{{ route("captcha.verify") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            captcha_answer: answer
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            captchaVerified = true;
                            document.getElementById('captcha_answer').classList.remove('border-red-500');
                            document.getElementById('captcha_answer').classList.add('border-green-500');
                        } else {
                            captchaVerified = false;
                            document.getElementById('captcha_answer').classList.remove('border-green-500');
                            document.getElementById('captcha_answer').classList.add('border-red-500');
                        }
                    })
                    .catch(error => {
                        console.error('Error verifying captcha:', error);
                        captchaVerified = false;
                    });
                }
            }
            
            // File handling functions
            window.handleFileSelect = function(input) {
                const file = input.files[0];
                if (file) {
                    // Validate file type
                    if (file.type !== 'application/pdf') {
                        alert('Hanya file PDF yang diizinkan!');
                        input.value = '';
                        return;
                    }
                    
                    // Validate file size (2MB = 2 * 1024 * 1024 bytes)
                    if (file.size > 2 * 1024 * 1024) {
                        alert('Ukuran file terlalu besar! Maksimal 2MB.');
                        input.value = '';
                        return;
                    }
                    
                    selectedFile = file;
                    showFilePreview(file.name, file.size);
                    
                    // Store file data for form persistence
                    const fileData = {
                        name: file.name,
                        size: file.size,
                        type: file.type
                    };
                    document.getElementById('dokumen_perizinan_data').value = JSON.stringify(fileData);
                }
            };
            
            window.removeFile = function() {
                selectedFile = null;
                document.getElementById('dokumen_perizinan').value = '';
                document.getElementById('dokumen_perizinan_data').value = '';
                document.getElementById('file-preview').classList.add('hidden');
            };
            
            function showFilePreview(fileName, fileSize) {
                document.getElementById('file-name').textContent = fileName;
                document.getElementById('file-size').textContent = formatFileSize(fileSize);
                document.getElementById('file-preview').classList.remove('hidden');
            }
            
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // Room selection handler
            const meetingRoomSelect = document.getElementById('meeting_room_id');
            if (meetingRoomSelect) {
                meetingRoomSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const roomDetails = document.getElementById('room-details');
                    const capacityContainer = document.getElementById('room-capacity-container');
                    
                    if (this.value) {
                        const capacity = selectedOption.dataset.capacity || 0;
                        const amenities = JSON.parse(selectedOption.dataset.amenities);
                        
                        // Show/hide capacity based on value
                        if (capacity && parseInt(capacity) > 0) {
                            document.getElementById('room-capacity').textContent = capacity + ' kursi';
                            capacityContainer.classList.remove('hidden');
                        } else {
                            capacityContainer.classList.add('hidden');
                        }
                        
                        document.getElementById('room-amenities').textContent = amenities.join(', ');
                        
                        roomDetails.classList.remove('hidden');
                    } else {
                        roomDetails.classList.add('hidden');
                    }
                });
            }

            // Description visibility handler - auto-check all PICs when "public" is selected
            const visibilityInvitedOnly = document.getElementById('visibility_invited_only');
            const visibilityPublic = document.getElementById('visibility_public');
            const picCheckboxes = document.querySelectorAll('input[name="invited_pics[]"]');
            
            if (visibilityPublic && visibilityInvitedOnly) {
                visibilityPublic.addEventListener('change', function() {
                    if (this.checked) {
                        // Auto-check all PIC checkboxes
                        picCheckboxes.forEach(checkbox => {
                            checkbox.checked = true;
                        });
                    }
                });
                
                visibilityInvitedOnly.addEventListener('change', function() {
                    if (this.checked) {
                        // Don't auto-uncheck, let user manage manually
                    }
                });
            }

            // Clear all PIC selections button
            const clearAllPicsBtn = document.getElementById('clear-all-pics');
            if (clearAllPicsBtn) {
                clearAllPicsBtn.addEventListener('click', function() {
                    // Uncheck all PIC checkboxes
                    picCheckboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                });
            }

            // Set up event listeners
            const startWaktuInput = document.getElementById('start_time');
            const endWaktuInput = document.getElementById('end_time');

            if (startWaktuInput) {
                startWaktuInput.addEventListener('change', function() {
                    const startWaktu = new Date(this.value);
                    const endWaktu = new Date(startWaktu.getTime() + 60 * 60 * 1000); // Add 1 hour
                    document.getElementById('end_time').value = endWaktu.toISOString().slice(0, 16);
                });
            }

        // No time restrictions - user can book anytime
        const now = new Date();
        const minTimeString = now.toISOString().slice(0, 16);
        
        console.log('Current time:', now.toLocaleString('id-ID', {timeZone: 'Asia/Jakarta'}));
            
            if (startWaktuInput) {
                // No minimum time restriction
                startWaktuInput.addEventListener('change', function() {
                    // Use the centralized validation function
                    validateEndTime();
                });
            }
            
            if (endWaktuInput) {
                // No minimum time restriction
                endWaktuInput.addEventListener('change', validateEndTime);
            }
            
            // Separate function for time validation (only logical constraints)
            function validateEndTime() {
                const endWaktuInput = document.getElementById('end_time');
                const startWaktuInput = document.getElementById('start_time');
                if (!endWaktuInput || !startWaktuInput) return;
                
                const startTime = new Date(startWaktuInput.value);
                const endTime = new Date(endWaktuInput.value);
                
                // Clear previous errors
                endWaktuInput.setCustomValidity('');
                startWaktuInput.setCustomValidity('');
                
                // Only validate logical time constraints
                if (startTime && endTime && !isNaN(startTime.getTime()) && !isNaN(endTime.getTime())) {
                    if (endTime <= startTime) {
                        endWaktuInput.setCustomValidity('Waktu selesai harus setelah waktu mulai');
                    } else if (startTime >= endTime) {
                        startWaktuInput.setCustomValidity('Waktu mulai harus sebelum waktu selesai');
                    }
                }
            }
            
            // Also validate when form loads
            if (startWaktuInput && endWaktuInput) {
                // Initial validation
                setTimeout(() => {
                    validateEndTime();
                }, 100);
            }

            // Add event listeners for real-time availability checking with debouncing
            let availabilityCheckTimeout;
            
            function debouncedCheckAvailability() {
                // Clear previous timeout
                if (availabilityCheckTimeout) {
                    clearTimeout(availabilityCheckTimeout);
                }
                
                // Clear current availability status
                const availabilityDiv = document.getElementById('availability-status');
                if (availabilityDiv) {
                    availabilityDiv.innerHTML = '';
                }
                
                // Set new timeout for availability check
                availabilityCheckTimeout = setTimeout(() => {
                    checkAvailability();
                }, 500); // Wait 500ms after user stops typing/selecting
            }
            
            if (meetingRoomSelect) {
                meetingRoomSelect.addEventListener('change', debouncedCheckAvailability);
            }
            if (startWaktuInput) {
                startWaktuInput.addEventListener('change', debouncedCheckAvailability);
                startWaktuInput.addEventListener('input', debouncedCheckAvailability);
            }
            if (endWaktuInput) {
                endWaktuInput.addEventListener('change', debouncedCheckAvailability);
                endWaktuInput.addEventListener('input', debouncedCheckAvailability);
            }

            // Initial availability check
            let initialCheckTimeout = setTimeout(() => {
                if (!isSubmitting) {
                    checkAvailability();
                }
            }, 500);

            // Real-time availability check and submit button control
            function checkAvailability() {
                // Don't check availability if form is being submitted
                if (isSubmitting) {
                    return;
                }
                
                const roomId = document.getElementById('meeting_room_id').value;
                const startWaktu = document.getElementById('start_time').value;
                const endWaktu = document.getElementById('end_time').value;
                const submitBtn = document.getElementById('submit-booking-btn');
                
                // Debug logging
                console.log('Checking availability with:', {
                    roomId: roomId,
                    startWaktu: startWaktu,
                    endWaktu: endWaktu,
                    timestamp: new Date().toISOString()
                });
                
                if (roomId && startWaktu && endWaktu) {
                    fetch('{{ route("user.check-availability") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            room_id: roomId,
                            start_time: startWaktu,
                            end_time: endWaktu
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Debug logging for response
                        console.log('Availability check response:', data);
                        
                        // Clear any existing availability status
                        const availabilityDiv = document.getElementById('availability-status');
                        if (availabilityDiv) {
                            availabilityDiv.innerHTML = '';
                        }
                        
                        if (data.available) {
                            // Hide conflict modal if exists
                            window.closeConflictModal();
                            
                            // Show success message
                            if (availabilityDiv) {
                                availabilityDiv.innerHTML = `
                                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                                        <div class="flex items-center">
                                            <i data-feather="check-circle" class="mr-2" style="width: 18px; height: 18px;"></i>
                                            <span class="font-medium">${data.message}</span>
                                        </div>
                                    </div>
                                `;
                            }
                            // Enable submit button
                            submitBtn.disabled = false;
                            submitBtn.className = 'px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-300 flex items-center';
                        } else {
                            // Show conflict modal/popup
                            showConflictModal(data.message, data.conflicts || []);
                            // Disable submit button
                            submitBtn.disabled = true;
                            submitBtn.className = 'px-6 py-3 bg-gray-400 text-gray-200 rounded-lg cursor-not-allowed flex items-center';
                        }
                    })
                    .catch(error => {
                        console.error('Error checking availability:', error);
                    });
                }
            }
            
            // Conflict Modal Functions
            function showConflictModal(message, conflicts) {
                // Remove existing modal if any
                const existingModal = document.getElementById('conflictModal');
                if (existingModal) {
                    existingModal.remove();
                }
                
                let conflictContent = '';
                if (conflicts && conflicts.length > 0) {
                    conflictContent = `
                        <div class="space-y-3 mt-4">
                            <p class="text-sm text-gray-700 mb-3">Booking yang bentrok:</p>
                            ${conflicts.map(conflict => `
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-black">${conflict.title}</h4>
                                            <p class="text-sm text-black mt-1">oleh ${conflict.user}</p>
                                            <p class="text-xs text-gray-500 mt-1">${conflict.start_time} - ${conflict.end_time}</p>
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    `;
                }
                
                const modalHtml = `
                    <div id="conflictModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-2 sm:p-4" onclick="window.closeConflictModal()">
                        <div class="bg-white rounded-2xl max-w-md w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
                            <div class="sticky top-0 bg-white border-b border-gray-200 z-10 p-4 sm:p-6 pb-4 flex justify-between items-center">
                                <h3 class="text-lg sm:text-xl font-bold text-black">Jadwal Bentrok</h3>
                                <button type="button" onclick="window.closeConflictModal()" class="text-gray-500 hover:text-gray-700 p-2 -mr-2">
                                    <i data-feather="x" style="width: 24px; height: 24px;"></i>
                                </button>
                            </div>
                            
                            <div class="p-4 sm:p-6">
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                                    <div class="flex items-start">
                                        <i data-feather="alert-triangle" class="text-red-500 mr-2 mt-1" style="width: 20px; height: 20px;"></i>
                                        <div class="flex-1">
                                            <p class="text-sm text-red-800 whitespace-pre-line leading-relaxed">${message}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                ${conflictContent}
                                
                                <div class="mt-6 flex justify-end">
                                    <button type="button" onclick="window.closeConflictModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 w-full sm:w-auto">
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
                        const modal = document.getElementById('conflictModal');
                        if (modal && !modal.classList.contains('hidden')) {
                            window.closeConflictModal();
                            document.removeEventListener('keydown', escHandler);
                        }
                    }
                });
            }
            
            // Make functions globally accessible
            window.closeConflictModal = function() {
                const modal = document.getElementById('conflictModal');
                if (modal) {
                    modal.remove();
                }
            };
            
            // Ensure form submission works
            const form = document.querySelector('form');
            let isSubmitting = false; // Flag to prevent availability check after submit
            
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Check captcha verification
                    if (!captchaVerified) {
                        e.preventDefault();
                        alert('Silakan jawab pertanyaan captcha dengan benar sebelum melanjutkan.');
                        document.getElementById('captcha_answer').focus();
                        return false;
                    }
                    
                    // Set submitting flag to prevent availability checks
                    isSubmitting = true;
                    
                    // Clear any pending timeouts
                    if (initialCheckTimeout) {
                        clearTimeout(initialCheckTimeout);
                    }
                    
                    // Remove event listeners to prevent further availability checks
                    if (meetingRoomSelect) {
                        meetingRoomSelect.removeEventListener('change', checkAvailability);
                    }
                    if (startWaktuInput) {
                        startWaktuInput.removeEventListener('change', checkAvailability);
                    }
                    if (endWaktuInput) {
                        endWaktuInput.removeEventListener('change', checkAvailability);
                    }
                    
                    // Clear availability status div to prevent showing conflicts
                    const availabilityDiv = document.getElementById('availability-status');
                    if (availabilityDiv) {
                        availabilityDiv.innerHTML = '';
                    }
                    
                    // Add loading state to submit button
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i data-feather="loader" class="mr-2 animate-spin" style="width: 18px; height: 18px;"></i>Memproses...';
                        if (typeof feather !== 'undefined') {
                            feather.replace();
                        }
                    }
                });
            }

        });
</script>
@endpush

