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
                <h2 class="text-2xl font-bold text-white mb-2">Book a Meeting Room</h2>
                <p class="text-white/80">Fill in the details below to book your meeting room</p>
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
                        <i class="fas fa-door-open mr-2"></i>Meeting Room *
                    </label>
                    <div class="relative">
                        <select id="meeting_room_id" name="meeting_room_id" required
                                class="w-full px-4 py-3 pr-10 bg-white/20 border border-white/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all duration-300 appearance-none cursor-pointer">
                            <option value="" class="bg-gray-800 text-white">Select a meeting room</option>
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
                    <h4 class="text-white font-medium mb-2">Room Details</h4>
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
                        <i class="fas fa-heading mr-2"></i>Meeting Judul *
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required
                           class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all duration-300"
                           placeholder="Enter meeting title">
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="description" class="block text-sm font-medium text-white mb-2">
                        <i class="fas fa-align-left mr-2"></i>Deskripsi
                    </label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all duration-300"
                              placeholder="Enter meeting description">{{ old('description') }}</textarea>
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
                            <i class="fas fa-users mr-2"></i>Number of Peserta *
                        </label>
                        <input type="number" id="attendees_count" name="attendees_count" value="{{ old('attendees_count', 1) }}" min="1" required
                               class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all duration-300">
                    </div>
                    <div>
                        <label for="attendees" class="block text-sm font-medium text-white mb-2">
                            <i class="fas fa-envelope mr-2"></i>Attendee Emails (Optional)
                        </label>
                        <input type="text" id="attendees" name="attendees" value="{{ old('attendees') }}"
                               class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all duration-300"
                               placeholder="email1@example.com, email2@example.com">
                        <p class="text-white/60 text-xs mt-1">Separate multiple emails with commas</p>
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
                    <input type="text" id="unit_kerja" name="unit_kerja" value="{{ old('unit_kerja') }}" required
                           class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all duration-300"
                           placeholder="Masukkan unit kerja Anda">
                </div>

                <!-- Dokumen Perizinan -->
                <div>
                    <label for="dokumen_perizinan" class="block text-sm font-medium text-white mb-2">
                        <i class="fas fa-file-pdf mr-2"></i>Dokumen Perizinan (PDF, Max 2MB) *
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
                    
                    <p class="text-white/60 text-xs mt-1">Upload dokumen perizinan dalam format PDF (maksimal 2MB)</p>
                </div>

                <!-- Captcha Section -->
                <div class="bg-white/10 rounded-lg p-6 border border-white/20">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <i class="fas fa-shield-alt mr-2 text-blue-400"></i>
                        Verifikasi Keamanan
                    </h3>
                    
                    <div class="flex items-center space-x-4">
                        <div class="flex-1">
                            <label for="captcha_answer" class="block text-sm font-medium text-white mb-2">
                                <i class="fas fa-key mr-2"></i>Masukkan angka berikut:
                            </label>
                            <div class="flex items-center space-x-3">
                                <div id="captcha-question" class="text-2xl font-bold text-white bg-white/20 px-6 py-4 rounded-lg border border-white/30 min-w-[200px] text-center tracking-widest">
                                    Loading...
                                </div>
                                <button type="button" id="refresh-captcha" class="px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-300">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                        <div class="flex-1">
                            <label for="captcha_answer" class="block text-sm font-medium text-white mb-2">
                                Jawaban:
                            </label>
                            <input type="text" id="captcha_answer" name="captcha_answer" required
                                   class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all duration-300 text-center text-xl font-mono tracking-widest"
                                   placeholder="Masukkan 4 digit angka" maxlength="4">
                        </div>
                    </div>
                    <p class="text-white/60 text-xs mt-2">
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
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-300 flex items-center">
                        <i class="fas fa-calendar-plus mr-2"></i>
                        Book Meeting Room
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

            // Set minimum date to 15 minutes from now (allows urgent bookings)
            // Use server timezone to match PHP
            const now = new Date();
            const minTime = new Date(now.getTime() + 15 * 60 * 1000); // 15 minutes from now
            const minTimeString = minTime.toISOString().slice(0, 16);
            
            console.log('Current time:', now.toLocaleString('id-ID', {timeZone: 'Asia/Jakarta'}));
            console.log('Min time:', minTime.toLocaleString('id-ID', {timeZone: 'Asia/Jakarta'}));
            
            if (startWaktuInput) {
                startWaktuInput.min = minTimeString;
                startWaktuInput.setCustomValidity('Waktu mulai harus minimal 15 menit dari sekarang');
                
                // Add real-time validation
                startWaktuInput.addEventListener('change', function() {
                    const startTime = new Date(this.value);
                    const endTime = new Date(document.getElementById('end_time').value);
                    const minTime = new Date(minTimeString);
                    
                    if (startTime < minTime) {
                        this.setCustomValidity('Waktu mulai harus minimal 15 menit dari sekarang');
                    } else if (endTime && startTime >= endTime) {
                        this.setCustomValidity('Waktu mulai harus sebelum waktu selesai');
                    } else {
                        this.setCustomValidity('');
                    }
                });
            }
            
            if (endWaktuInput) {
                endWaktuInput.min = minTimeString;
                endWaktuInput.setCustomValidity('Waktu selesai harus minimal 15 menit dari sekarang');
                
                // Add real-time validation
                endWaktuInput.addEventListener('change', function() {
                    const startTime = new Date(document.getElementById('start_time').value);
                    const endTime = new Date(this.value);
                    const minTime = new Date(minTimeString);
                    
                    if (endTime < minTime) {
                        this.setCustomValidity('Waktu selesai harus minimal 15 menit dari sekarang');
                    } else if (startTime && endTime <= startTime) {
                        this.setCustomValidity('Waktu selesai harus setelah waktu mulai');
                    } else {
                        this.setCustomValidity('');
                    }
                });
            }

            // Real-time availability check
            function checkAvailability() {
                const roomId = document.getElementById('meeting_room_id').value;
                const startWaktu = document.getElementById('start_time').value;
                const endWaktu = document.getElementById('end_time').value;
                
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
                        } else {
                            statusDiv.innerHTML = `
                                <div class="bg-red-500/20 border border-red-500/50 text-red-300 px-4 py-3 rounded-lg">
                                    <div class="flex items-start">
                                        <i class="fas fa-exclamation-triangle mr-2 mt-1"></i>
                                        <div class="flex-1">
                                            <div class="text-sm whitespace-pre-line leading-relaxed">${data.message}</div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }
                    })
                    .catch(error => {
                        console.error('Error checking availability:', error);
                    });
                }
            }

            // Add event listeners for real-time checking
            if (meetingRoomSelect) {
                meetingRoomSelect.addEventListener('change', checkAvailability);
            }
            if (startWaktuInput) {
                startWaktuInput.addEventListener('change', checkAvailability);
            }
            if (endWaktuInput) {
                endWaktuInput.addEventListener('change', checkAvailability);
            }

            // Ensure form submission works
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Check captcha verification
                    if (!captchaVerified) {
                        e.preventDefault();
                        alert('Silakan jawab pertanyaan captcha dengan benar sebelum melanjutkan.');
                        document.getElementById('captcha_answer').focus();
                        return false;
                    }
                    
                    // Add loading state to submit button
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
                    }
                });
            }
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
