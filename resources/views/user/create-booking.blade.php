<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pesan Ruang Meeting - Sistem Pemesanan Ruang Meeting</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/dropdown-fix.css') }}" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
        
        /* Fix dropdown styling */
        select {
            background-color: rgba(255, 255, 255, 0.2) !important;
            color: white !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            background-image: none !important;
        }
        
        select option {
            background-color: #1a202c !important;
            color: white !important;
            padding: 8px 12px !important;
        }
        
        select option:hover {
            background-color: #2d3748 !important;
            color: white !important;
        }
        
        select option:checked {
            background-color: #3182ce !important;
            color: white !important;
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
</head>
<body class="gradient-bg min-h-screen">
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
        <div class="glass-effect rounded-2xl p-8 shadow-2xl">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-white mb-2">Pesan Ruang Meeting</h2>
                <p class="text-white/80">Isi detail di bawah untuk memesan ruang meeting Anda</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-500/20 border border-red-500/50 text-red-300 px-6 py-4 rounded-lg mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle mr-3 mt-1 text-red-400"></i>
                        <div class="flex-1">
                            <h4 class="font-semibold text-red-200 mb-2">❌ Gagal Membuat Booking</h4>
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
                    <label for="meeting_room_id" class="block text-sm font-medium text-white mb-2">
                        <i class="fas fa-door-open mr-2"></i>Ruang Meeting *
                    </label>
                    <div class="relative">
                        <select id="meeting_room_id" name="meeting_room_id" required
                                class="w-full px-4 py-3 pr-10 bg-white/20 border border-white/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all duration-300 appearance-none cursor-pointer">
                            <option value="" class="bg-gray-800 text-white">Pilih ruang meeting</option>
                            @foreach($rooms as $room)
                            <option value="{{ $room->id }}" 
                                    data-capacity="{{ $room->capacity }}"
                                    data-amenities="{{ json_encode($room->getAmenitiesList()) }}"
                                    class="bg-gray-800 text-white"
                                    {{ old('meeting_room_id') == $room->id ? 'selected' : '' }}>
                                {{ $room->name }} - {{ $room->location }} ({{ $room->capacity }} kursi)
                            </option>
                            @endforeach
                        </select>
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                            <i class="fas fa-chevron-down text-white/60"></i>
                        </div>
                    </div>
                </div>

                <!-- Room Details Display -->
                <div id="room-details" class="hidden bg-white/10 rounded-lg p-4">
                    <h4 class="text-white font-medium mb-2">Detail Ruang</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-white/60">Kapasitas:</span>
                            <span id="room-capacity" class="text-white ml-2"></span>
                        </div>
                        <div>
                            <span class="text-white/60">Fasilitas:</span>
                            <span id="room-amenities" class="text-white ml-2"></span>
                        </div>
                    </div>
                </div>

                <!-- Meeting Judul -->
                <div>
                    <label for="title" class="block text-sm font-medium text-white mb-2">
                        <i class="fas fa-heading mr-2"></i>Judul Meeting *
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required
                           class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all duration-300"
                           placeholder="Masukkan judul pertemuan">
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="description" class="block text-sm font-medium text-white mb-2">
                        <i class="fas fa-align-left mr-2"></i>Deskripsi
                    </label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all duration-300"
                              placeholder="Masukkan deskripsi meeting, link Zoom/Google Meet, atau informasi penting lainnya">{{ old('description') }}</textarea>
                    <p class="text-white/60 text-xs mt-1">Anda dapat memasukkan link Zoom, Google Meet, atau informasi penting lainnya</p>
                </div>

                <!-- PIC Invitations Section -->
                <div class="pic-invitations-section">
                    <label class="block text-sm font-medium text-white mb-2">
                        <i class="fas fa-user-tie mr-2"></i>Undang PIC Lain
                    </label>
                    
                    <!-- Visibility Setting -->
                    <div class="mb-4">
                        <label class="flex items-center text-white mb-2">
                            <input type="radio" name="description_visibility" value="invited_pics_only" checked class="mr-2">
                            Hanya PIC yang diundang dapat melihat deskripsi
                        </label>
                        <label class="flex items-center text-white">
                            <input type="radio" name="description_visibility" value="public" class="mr-2">
                            Semua PIC dapat melihat deskripsi
                        </label>
                    </div>
                    
                    <!-- PIC Selection -->
                    <div class="bg-white/10 rounded-lg p-4 max-h-60 overflow-y-auto">
                        <h4 class="text-white font-medium mb-3">Pilih PIC yang akan diundang:</h4>
                        @foreach($allPics as $pic)
                            @if($pic->id !== auth()->id())
                            <label class="flex items-center text-white mb-2 cursor-pointer hover:bg-white/5 p-2 rounded">
                                <input type="checkbox" name="invited_pics[]" value="{{ $pic->id }}" class="mr-3">
                                <div>
                                    <div class="font-medium">{{ $pic->full_name }}</div>
                                    <div class="text-sm text-white/60">{{ $pic->unit_kerja }}</div>
                                </div>
                            </label>
                            @endif
                        @endforeach
                    </div>
                    
                    <p class="text-white/60 text-xs mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        PIC yang diundang akan menerima notifikasi dan dapat melihat deskripsi meeting (termasuk link Zoom/Meet) di kalender mereka.
                    </p>
                </div>

                <!-- Tanggal and Waktu -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-white mb-2">
                            <i class="fas fa-clock mr-2"></i>Mulai Waktu *
                        </label>
                        <input type="datetime-local" id="start_time" name="start_time" value="{{ old('start_time') }}" required
                               class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all duration-300">
                    </div>
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-white mb-2">
                            <i class="fas fa-clock mr-2"></i>Selesai Waktu *
                        </label>
                        <input type="datetime-local" id="end_time" name="end_time" value="{{ old('end_time') }}" required
                               class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all duration-300">
                    </div>
                </div>

                <!-- Peserta -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="attendees_count" class="block text-sm font-medium text-white mb-2">
                            <i class="fas fa-users mr-2"></i>Jumlah Peserta *
                        </label>
                        <input type="number" id="attendees_count" name="attendees_count" value="{{ old('attendees_count', 1) }}" min="1" required
                               class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all duration-300">
                    </div>
                    <div>
                        <label for="attendees" class="block text-sm font-medium text-white mb-2">
                            <i class="fas fa-envelope mr-2"></i>Email Peserta (Opsional)
                        </label>
                        <input type="text" id="attendees" name="attendees" value="{{ old('attendees') }}"
                               class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all duration-300"
                               placeholder="email1@example.com, email2@example.com">
                        <p class="text-white/60 text-xs mt-1">Pisahkan beberapa email dengan koma</p>
                    </div>
                </div>

                <!-- Special Requirements -->
                <div>
                    <label for="special_requirements" class="block text-sm font-medium text-white mb-2">
                        <i class="fas fa-clipboard-list mr-2"></i>Kebutuhan Khusus
                    </label>
                    <textarea id="special_requirements" name="special_requirements" rows="3"
                              class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all duration-300"
                              placeholder="Kebutuhan khusus untuk meeting">{{ old('special_requirements') }}</textarea>
                </div>

                <!-- Unit Kerja -->
                <div>
                    <label for="unit_kerja" class="block text-sm font-medium text-white mb-2">
                        <i class="fas fa-building mr-2"></i>Unit Kerja *
                    </label>
                    <input type="text" id="unit_kerja" name="unit_kerja" 
                           value="{{ old('unit_kerja', isset($userUnitKerja) && $userUnitKerja ? $userUnitKerja : '') }}" 
                           required
                           class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all duration-300"
                           placeholder="Masukkan unit kerja Anda">
                </div>

                <!-- Dokumen Tambahan (Opsional) -->
                <div>
                    <label for="dokumen_perizinan" class="block text-sm font-medium text-white mb-2">
                        <i class="fas fa-file-pdf mr-2"></i>Dokumen Tambahan (Opsional) (PDF, Max 2MB)
                    </label>
                    
                    <!-- File Input -->
                    <div class="relative">
                        <input type="file" id="dokumen_perizinan" name="dokumen_perizinan" accept=".pdf" 
                               class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all duration-300"
                               onchange="handleFileSelect(this)">
                        <input type="hidden" id="dokumen_perizinan_data" name="dokumen_perizinan_data" value="{{ old('dokumen_perizinan_data') }}">
                    </div>
                    
                    <!-- File Preview -->
                    <div id="file-preview" class="hidden mt-3 p-3 bg-white/10 rounded-lg border border-white/20">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-file-pdf text-red-400 mr-2"></i>
                                <span id="file-name" class="text-white text-sm"></span>
                                <span id="file-size" class="text-white/60 text-xs ml-2"></span>
                            </div>
                            <button type="button" onclick="removeFile()" class="text-red-400 hover:text-red-300 transition-colors">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    
                    <p class="text-white/60 text-xs mt-1">Opsional: unggah dokumen pendukung dalam format PDF (maksimal 2MB)</p>
                </div>

                <!-- Captcha Section -->
                <div class="bg-white/10 rounded-lg p-6 border border-white/20">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <i class="fas fa-shield-alt mr-2 text-blue-400"></i>
                        Verifikasi Keamanan
                    </h3>
                    
                    <!-- Mobile-friendly vertical layout -->
                    <div class="space-y-4">
                        <!-- Captcha Question -->
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">
                                <i class="fas fa-key mr-2"></i>Masukkan angka berikut:
                            </label>
                            <div class="flex items-center justify-center space-x-3">
                                <div id="captcha-question" class="text-xl font-bold text-white bg-white/20 px-4 py-4 rounded-lg border border-white/30 text-center tracking-widest min-w-[100px] max-w-[150px]">
                                    Loading...
                                </div>
                                <button type="button" id="refresh-captcha" class="px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-300">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Captcha Answer -->
                        <div>
                            <label for="captcha_answer" class="block text-sm font-medium text-white mb-2">
                                Jawaban:
                            </label>
                            <input type="text" id="captcha_answer" name="captcha_answer" required
                                   class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all duration-300 text-center text-lg font-mono tracking-widest"
                                   placeholder="4 digit" maxlength="4">
                        </div>
                    </div>
                    
                    <p class="text-white/60 text-xs mt-3 text-center">
                        <i class="fas fa-info-circle mr-1"></i>
                        Silakan masukkan 4 digit angka yang ditampilkan di atas untuk melanjutkan pemesanan.
                    </p>
                </div>

                <!-- Kirim Button -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('user.dashboard') }}" 
                       class="px-6 py-3 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-colors duration-300">
                        Batal
                    </a>
                    <button type="submit" id="submit-booking-btn"
                            class="px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-300 flex items-center">
                        <i class="fas fa-calendar-plus mr-2"></i>
                        Pesan Ruang Meeting
                    </button>
                </div>
            </form>
        </div>
    </div>

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
                    
                    if (this.value) {
                        const capacity = selectedOption.dataset.capacity;
                        const amenities = JSON.parse(selectedOption.dataset.amenities);
                        
                        document.getElementById('room-capacity').textContent = capacity + ' kursi';
                        document.getElementById('room-amenities').textContent = amenities.join(', ');
                        
                        roomDetails.classList.remove('hidden');
                    } else {
                        roomDetails.classList.add('hidden');
                    }
                });
            }

            // Set up event listeners
            const startWaktuInput = document.getElementById('start_time');
            const endWaktuInput = document.getElementById('end_time');
            const attendeesInput = document.getElementById('attendees');

            if (startWaktuInput) {
                startWaktuInput.addEventListener('change', function() {
                    const startWaktu = new Date(this.value);
                    const endWaktu = new Date(startWaktu.getTime() + 60 * 60 * 1000); // Add 1 hour
                    document.getElementById('end_time').value = endWaktu.toISOString().slice(0, 16);
                });
            }

            if (attendeesInput) {
                attendeesInput.addEventListener('blur', function() {
                    const emails = this.value.split(',').map(email => email.trim()).filter(email => email);
                    this.value = emails.join(', ');
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
                        
                        const availabilityDiv = document.getElementById('availability-status');
                        if (!availabilityDiv) {
                            const newDiv = document.createElement('div');
                            newDiv.id = 'availability-status';
                            newDiv.className = 'mt-4';
                            document.querySelector('form').insertBefore(newDiv, document.querySelector('.flex.justify-end'));
                        }
                        
                        const statusDiv = document.getElementById('availability-status');
                        if (data.available) {
                            statusDiv.innerHTML = `
                                <div class="bg-green-500/20 border border-green-500/50 text-green-300 px-4 py-3 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        <span class="font-medium">${data.message}</span>
                                    </div>
                                </div>
                            `;
                            // Enable submit button
                            submitBtn.disabled = false;
                            submitBtn.className = 'px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-300 flex items-center';
                        } else {
                            let conflictHtml = `
                                <div class="bg-red-500/20 border border-red-500/50 text-red-300 px-4 py-3 rounded-lg">
                                    <div class="flex items-start">
                                        <i class="fas fa-exclamation-triangle mr-2 mt-1"></i>
                                        <div class="flex-1">
                                            <div class="text-sm whitespace-pre-line leading-relaxed">${data.message}</div>
                                        </div>
                                    </div>
                                </div>
                            `;
                            
                            // Add preempt buttons if there are conflicts with OTHER users
                            if (data.conflicts && data.conflicts.length > 0) {
                                conflictHtml += `
                                    <div class="mt-3 bg-yellow-500/20 border border-yellow-500/50 text-yellow-300 px-4 py-3 rounded-lg">
                                        <div class="flex items-start">
                                            <i class="fas fa-handshake-angle mr-2 mt-1"></i>
                                            <div class="flex-1">
                                                <div class="text-sm font-medium mb-2">Ingin meminta didahulukan?</div>
                                                <div class="text-xs mb-3">Anda dapat meminta pemilik booking untuk mengalah jika ada kepentingan yang lebih mendesak.</div>
                                                <div class="space-y-2">
                                                    ${data.conflicts.map(conflict => `
                                                        <div class="flex items-center justify-between bg-white/10 rounded p-2">
                                                            <div class="text-xs">
                                                                <div class="font-medium">${conflict.title}</div>
                                                                <div class="text-yellow-200/80">oleh ${conflict.user} • ${conflict.start_time} - ${conflict.end_time}</div>
                                                            </div>
                                                            <button type="button" onclick="requestPreempt(${conflict.id})" 
                                                                    class="px-3 py-1 bg-yellow-600 hover:bg-yellow-700 text-white text-xs rounded transition-colors">
                                                                <i class="fas fa-handshake mr-1"></i>Minta Didahulukan
                                                            </button>
                                                        </div>
                                                    `).join('')}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            }
                            
                            statusDiv.innerHTML = conflictHtml;
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
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
                    }
                });
            }

            // Preempt request function
            window.requestPreempt = function(bookingId) {
                const reason = prompt('Masukkan alasan mengapa Anda perlu didahulukan (opsional):');
                if (reason === null) return; // User cancelled
                
                fetch(`/user/bookings/${bookingId}/preempt-request`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        reason: reason || null
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message || 'Permintaan didahulukan berhasil dikirim!');
                        // Don't refresh availability check after preempt request
                        // as it might show conflicts with the user's own booking
                    } else {
                        alert(data.message || 'Gagal mengirim permintaan didahulukan.');
                    }
                })
                .catch(error => {
                    console.error('Error requesting preempt:', error);
                    alert('Terjadi kesalahan saat mengirim permintaan.');
                });
            };
        });
    </script>

    <!-- Mobile Sidebar -->
    @include('components.mobile-sidebar', [
        'userRole' => 'user',
        'userName' => session('user_data.full_name'),
        'userEmail' => session('user_data.email'),
        'pageTitle' => 'Pemesanan Baru'
    ])

    <!-- WhatsApp Floating Button -->
    @include('components.whatsapp-float')
</body>
</html>
