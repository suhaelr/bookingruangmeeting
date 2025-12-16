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
                        Ruang Meeting 
                        <span class="text-red-500">*</span>
                    </label>
                    <select id="meeting_room_id" name="meeting_room_id" required class="w-full">
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
                </div>

                <!-- Room Details Display -->
                <div id="room-details" class="hidden bg-[#071e48] text-white rounded-lg p-4 border border-[#071e48]">
                    <h4 class="font-medium mb-2">Detail Ruang</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div id="room-capacity-container" class="hidden">
                            <span>Kapasitas:</span>
                            <span id="room-capacity" class="ml-2"></span>
                        </div>
                        <div>
                            <span>Fasilitas:</span>
                            <span id="room-amenities" class="ml-2"></span>
                        </div>
                    </div>
                </div>

                <!-- Meeting Judul -->
                <div>
                    <label for="title" class="block text-sm font-medium text-black mb-2">
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
                        Deskripsi
                    </label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-4 py-3 form-control"
                              placeholder="Masukkan deskripsi meeting, link Zoom/Google Meet, atau informasi penting lainnya">{{ old('description') }}</textarea>
                    <p class="text-gray-600 text-xs mt-1">Anda dapat memasukkan link Zoom, Google Meet, atau informasi penting lainnya</p>
                </div>

                <!-- PIC Invitations Section -->
                <div class="pic-invitations-section">
                    <label class="block text-sm font-medium text-black mb-2">
                        Undang PIC Lain
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
                            Waktu Mulai
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" id="start_time" name="start_time" value="{{ old('start_time') }}" required
                               class="w-full px-4 py-3 form-control">
                    </div>
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-black mb-2">
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
                        Unit Kerja
                        <span class="text-red-500">*</span>
                    </label>
                    <select id="unit_kerja" name="unit_kerja" required class="w-full">
                        <option value="">Pilih Unit Kerja</option>
                        @foreach(($unitKerjaOptions ?? []) as $unitKerja)
                            <option value="{{ $unitKerja }}" {{ old('unit_kerja', isset($userUnitKerja) && $userUnitKerja ? $userUnitKerja : '') == $unitKerja ? 'selected' : '' }}>{{ $unitKerja }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Dokumen Tambahan (Opsional) -->
                <div>
                    <label for="dokumen_perizinan" class="block text-sm font-medium text-black mb-2">
                        Dokumen Tambahan (Opsional. Dokumen berformat PDF, maksimal 2MB)
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
            // Initialize Select2 for meeting room
            initSelect2();
            
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

            // Initialize Select2
            function initSelect2() {
                if (typeof $ === 'undefined') {
                    console.error('jQuery tidak dimuat.');
                    return;
                }

                $('#meeting_room_id').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Pilih ruang meeting',
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
                @if(old('meeting_room_id'))
                    $('#meeting_room_id').val('{{ old('meeting_room_id') }}').trigger('change');
                @endif

                // Initialize Select2 for unit_kerja
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
                @php
                    $selectedUnitKerja = old('unit_kerja', isset($userUnitKerja) && $userUnitKerja ? $userUnitKerja : '');
                @endphp
                @if($selectedUnitKerja)
                    $('#unit_kerja').val('{{ $selectedUnitKerja }}').trigger('change');
                @endif

                // Room selection handler for Select2
                $('#meeting_room_id').on('change', function() {
                    const selectedValue = $(this).val();
                    const selectedOption = $(this).find('option:selected');
                    const roomDetails = document.getElementById('room-details');
                    const capacityContainer = document.getElementById('room-capacity-container');
                    
                    if (selectedValue) {
                        // Get capacity - use attr() to get raw value, then parse
                        const capacityAttr = selectedOption.attr('data-capacity');
                        const capacity = capacityAttr ? parseInt(capacityAttr) : 0;
                        
                        // Get amenities - use attr() to get raw JSON string, then parse
                        let amenities = [];
                        try {
                            const amenitiesAttr = selectedOption.attr('data-amenities');
                            if (amenitiesAttr) {
                                // Parse the JSON string from the attribute
                                amenities = JSON.parse(amenitiesAttr);
                            }
                        } catch (e) {
                            console.error('Error parsing amenities:', e);
                            amenities = [];
                        }
                        
                        // Show/hide capacity based on value
                        if (capacity && capacity > 0) {
                            document.getElementById('room-capacity').textContent = capacity + ' kursi';
                            capacityContainer.classList.remove('hidden');
                        } else {
                            capacityContainer.classList.add('hidden');
                        }
                        
                        document.getElementById('room-amenities').textContent = Array.isArray(amenities) ? amenities.join(', ') : '';
                        
                        roomDetails.classList.remove('hidden');
                    } else {
                        roomDetails.classList.add('hidden');
                    }
                    
                    // Trigger availability check
                    if (typeof debouncedCheckAvailability === 'function') {
                        debouncedCheckAvailability();
                    }
                });
            }

            // Room selection handler (keeping for compatibility)
            const meetingRoomSelect = document.getElementById('meeting_room_id');

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
            
            // Availability check is now handled by Select2 change event above
            // The debouncedCheckAvailability will be triggered via the Select2 change handler
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

