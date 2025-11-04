<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pemesanan Saya - Sistem Pemesanan Ruang Meeting</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <link href="{{ asset('css/dropdown-fix.css') }}" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Ensure buttons are clickable */
        button, .btn, [role="button"], a {
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
        
        /* Fix button hover states */
        button:hover, .btn:hover, a:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        /* Ensure proper z-index for overlays */
        .glass-effect {
            position: relative;
            z-index: 1;
        }
        
        /* Fix booking card interactions */
        .booking-item {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .booking-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        /* Mobile status badge fix */
        @media (max-width: 768px) {
            .booking-item {
                margin-bottom: 1rem;
            }
            
            .booking-item .flex.items-start.justify-between {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .booking-item .flex-shrink-0 {
                margin-left: 0;
                margin-top: 0.5rem;
                align-self: flex-start;
            }
            
            .booking-item .flex.items-center.space-x-4 {
                flex-wrap: wrap;
                gap: 0.5rem;
            }
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
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="glass-effect rounded-2xl p-6 mb-8 shadow-2xl">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white mb-2">Pemesanan Saya</h2>
                    <p class="text-white/80">Kelola dan pantau pemesanan ruang meeting Anda</p>
                </div>
                <div class="flex space-x-4">
                    <select id="status-filter" class="px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-white/50">
                        <option value="">Semua Status</option>
                        <option value="pending">Menunggu</option>
                        <option value="confirmed">Dikonfirmasi</option>
                        <option value="cancelled">Dibatalkan</option>
                        <option value="completed">Selesai</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Bookings List -->
        <div class="glass-effect rounded-2xl p-6 shadow-2xl">
            @if($bookings->count() > 0)
                <div class="space-y-4">
                    @foreach($bookings as $booking)
                    <div class="booking-item bg-white/10 rounded-lg p-6 hover:bg-white/20 transition-colors" 
                         data-status="{{ $booking->status }}">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                            <div class="flex-1">
                                <div class="mb-4">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-bold text-white mb-1">{{ $booking->title }}</h3>
                                            <p class="text-white/80 text-sm mb-2">{{ $booking->meetingRoom->name }} • {{ $booking->meetingRoom->location }}</p>
                                        </div>
                                        <div class="ml-4 flex-shrink-0">
                                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                                @if($booking->status === 'pending') bg-yellow-500 text-white
                                                @elseif($booking->status === 'confirmed') bg-green-500 text-white
                                                @elseif($booking->status === 'cancelled') bg-red-500 text-white
                                                @elseif($booking->status === 'completed') bg-blue-500 text-white
                                                @else bg-gray-500 text-white @endif">
                                                {{ $booking->status_text }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4 text-sm text-white/60">
                                        <span><i class="fas fa-calendar mr-1"></i>{{ $booking->start_time->format('d M Y') }}</span>
                                        <span><i class="fas fa-clock mr-1"></i>{{ $booking->start_time->format('H:i') }} - {{ $booking->end_time->format('H:i') }}</span>
                                        <span><i class="fas fa-users mr-1"></i>{{ $booking->attendees_count }} peserta</span>
                                    </div>
                                </div>
                                
                                @if($booking->description)
                                <p class="text-white/80 text-sm mb-3">{{ $booking->description }}</p>
                                @endif
                                
                                @if($booking->special_requirements)
                                <div class="bg-yellow-500/20 border border-yellow-500/30 rounded-lg p-3 mb-3">
                                    <p class="text-yellow-300 text-sm">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        <strong>Kebutuhan Khusus:</strong> {{ $booking->special_requirements }}
                                    </p>
                                </div>
                                @endif

                                @if(isset($booking->preempt_status) && $booking->preempt_status === 'pending')
                                <div class="bg-red-500/20 border border-red-500/30 rounded-lg p-3 mb-3">
                                    <p class="text-red-200 text-sm mb-3">
                                        <i class="fas fa-handshake-angle mr-2"></i>
                                        <strong>Permintaan Didahulukan:</strong> Booking ini sedang menunggu tanggapan Anda.
                                    </p>
                                    <div class="flex flex-wrap gap-2">
                                        <button onclick="respondPreempt({{ $booking->id }}, 'accept_cancel')" class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded">
                                            Terima & Batalkan
                                        </button>
                                    </div>
                                </div>
                                @endif
                                
                                @if($booking->attendees && count($booking->attendees) > 0)
                                <div class="mb-3">
                                    <p class="text-white/60 text-sm mb-1">Peserta:</p>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($booking->attendees as $attendee)
                                        <span class="px-2 py-1 bg-blue-500/20 text-blue-300 text-xs rounded">
                                            {{ $attendee }}
                                        </span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                                
                                @if($booking->status === 'cancelled' && $booking->cancellation_reason)
                                <div class="bg-red-500/20 border border-red-500/30 rounded-lg p-3">
                                    <p class="text-red-300 text-sm">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        <strong>Alasan Pembatalan:</strong> {{ $booking->cancellation_reason }}
                                    </p>
                                </div>
                                @endif
                            </div>
                            
                            <div class="flex items-center space-x-2 mt-4 lg:mt-0 lg:ml-6">
                                @if($booking->canBeCancelled())
                                <button onclick="cancelBooking({{ $booking->id }})" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors duration-300 flex items-center">
                                    <i class="fas fa-times mr-1"></i>Batal
                                </button>
                                @endif
                                
                                <button onclick="viewBooking({{ $booking->id }})" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors duration-300 flex items-center">
                                    <i class="fas fa-eye mr-1"></i>Lihat
                                </button>
                                
                                @if($booking->status !== 'confirmed')
                                <button onclick="editBooking({{ $booking->id }})" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-300 flex items-center">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </button>
                                @else
                                <button disabled class="px-4 py-2 bg-gray-400 text-gray-200 rounded-lg cursor-not-allowed flex items-center" title="Tidak dapat diedit karena sudah dikonfirmasi admin">
                                    <i class="fas fa-lock mr-1"></i>Edit
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="flex justify-between items-center mt-8">
                    <div class="text-white/80 text-sm">
                        Menampilkan {{ $bookings->firstItem() }} sampai {{ $bookings->lastItem() }} dari {{ $bookings->total() }} pemesanan
                    </div>
                    <div class="flex items-center space-x-4">
                        <button id="export-btn" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors duration-300 flex items-center">
                            <i class="fas fa-download mr-2"></i>Ekspor
                        </button>
                        <div class="flex space-x-2">
                        @if($bookings->previousPageUrl())
                        <a href="{{ $bookings->previousPageUrl() }}" class="px-3 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-colors">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                        @endif
                        
                        @for($i = 1; $i <= $bookings->lastPage(); $i++)
                        <a href="{{ $bookings->url($i) }}" 
                           class="px-3 py-2 rounded-lg transition-colors {{ $bookings->currentPage() == $i ? 'bg-white text-indigo-600 font-semibold' : 'bg-white/20 text-white hover:bg-white/30' }}">
                            {{ $i }}
                        </a>
                        @endfor
                        
                        @if($bookings->nextPageUrl())
                        <a href="{{ $bookings->nextPageUrl() }}" class="px-3 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-colors">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-calendar-times text-white/40 text-6xl mb-4"></i>
                    <h3 class="text-xl font-bold text-white mb-2">Tidak Ada Pemesanan</h3>
                    <p class="text-white/60 mb-6">Anda belum membuat pemesanan ruang meeting.</p>
                    <a href="{{ route('user.bookings.create') }}" 
                       class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors duration-300 inline-flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        Buat Pemesanan Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Booking Detail Modal -->
    <div id="bookingDetailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-800">Detail Pemesanan</h3>
                    <button onclick="closeModal('bookingDetailModal')" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div id="bookingDetailContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Edit Modal -->
    <div id="bookingEditModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-800">Edit Pemesanan</h3>
                    <button onclick="closeModal('bookingEditModal')" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form id="bookingEditForm">
                    @csrf
                    <div id="bookingEditContent">
                        <!-- Content will be loaded here -->
                    </div>
                    <div class="flex justify-end space-x-4 mt-6">
                        <button type="button" onclick="closeModal('bookingEditModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                            Perbarui Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Booking Batal Modal -->
    <div id="bookingBatalModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Batal Booking</h3>
                        <p class="text-gray-600">Tindakan ini tidak dapat dibatalkan</p>
                    </div>
                </div>
                <p class="text-gray-700 mb-6">Apakah Anda yakin ingin membatalkan pemesanan ini?</p>
                <div class="flex justify-end space-x-4">
                    <button onclick="closeModal('bookingBatalModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Batal
                    </button>
                    <button onclick="confirmBatalBooking()" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                        Batal Booking
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Berhasil Message -->
    @if (session('success'))
        <div id="success-message" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        </div>
    @endif

    <script>
        // Serialize bookings for client-side use (relations eager loaded in controller)
        const BOOKINGS = @json($bookings->items());
        let currentBookingId = null;

        // Modal functions
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Booking actions
        function viewBooking(bookingId) {
            currentBookingId = bookingId;
            const booking = BOOKINGS.find(b => b.id == bookingId);
            
            if (booking) {
                document.getElementById('bookingDetailContent').innerHTML = `
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xl font-bold text-gray-800">${booking.title}</h4>
                            <span class="px-3 py-1 rounded-full text-sm font-medium ${getStatusColor(booking.status)}">
                                ${booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ruang</label>
                                <p class="text-gray-900">${booking.meeting_room.name}</p>
                                <p class="text-gray-600 text-sm">${booking.meeting_room.location}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal & Waktu</label>
                                <p class="text-gray-900">${new Date(booking.start_time).toLocaleDateString()}</p>
                                <p class="text-gray-600 text-sm">${new Date(booking.start_time).toLocaleTimeString()} - ${new Date(booking.end_time).toLocaleTimeString()}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Durasi</label>
                                <p class="text-gray-900">${calculateDuration(booking.start_time, booking.end_time)} jam</p>
                            </div>
                        </div>
                        
                        ${booking.description ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                            <p class="text-gray-900">${booking.description}</p>
                        </div>
                        ` : ''}
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Visibility Deskripsi</label>
                            <p class="text-gray-900">
                                ${booking.description_visibility === 'public' ? 
                                    '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-globe mr-1"></i>Publik - Semua PIC dapat melihat</span>' :
                                    '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"><i class="fas fa-lock mr-1"></i>Terbatas - Hanya PIC yang diundang</span>'
                                }
                            </p>
                        </div>
                        
                        ${booking.invitations && booking.invitations.length > 0 ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">PIC yang Diundang</label>
                            <div class="space-y-2">
                                ${booking.invitations.map(invitation => `
                                    <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-medium mr-3">
                                                ${invitation.pic.full_name.charAt(0).toUpperCase()}
                                            </div>
                                            <div>
                                                <p class="text-gray-900 font-medium">${invitation.pic.full_name}</p>
                                                <p class="text-gray-600 text-sm">${invitation.pic.unit_kerja || 'Tidak ada unit kerja'}</p>
                                            </div>
                                        </div>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium ${getInvitationStatusColor(invitation.status)}">
                                            ${getInvitationStatusText(invitation.status)}
                                        </span>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                        ` : `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">PIC yang Diundang</label>
                            <p class="text-gray-500 italic">Tidak ada PIC yang diundang</p>
                        </div>
                        `}
                        
                        ${booking.special_requirements ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kebutuhan Khusus</label>
                            <p class="text-gray-900">${booking.special_requirements}</p>
                        </div>
                        ` : ''}
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Peserta</label>
                            <p class="text-gray-900">${booking.attendees_count} orang</p>
                            ${booking.attendees && booking.attendees.length > 0 ? `
                                <div class="mt-2">
                                    ${booking.attendees.map(email => `<span class="inline-block bg-gray-100 text-gray-800 px-2 py-1 rounded text-sm mr-2 mb-1">${email}</span>`).join('')}
                                </div>
                            ` : ''}
                        </div>
                    </div>
                `;
                openModal('bookingDetailModal');
            }
        }

        function editBooking(bookingId) {
            currentBookingId = bookingId;
            const booking = BOOKINGS.find(b => b.id == bookingId);
            const ALL_PICS = @json($allPics ?? []);
            
            if (booking) {
                // Build invited pics checklist
                const invitedIds = (booking.invitations || []).map(inv => inv.pic_id);
                const picsHtml = ALL_PICS
                    .filter(p => p.id !== booking.user_id)
                    .map(p => `
                        <label class="flex items-center text-gray-700 mb-2 cursor-pointer">
                            <input type="checkbox" name="invited_pics[]" value="${p.id}" class="mr-3" ${invitedIds.includes(p.id) ? 'checked' : ''}>
                            <div>
                                <div class="font-medium">${p.full_name}</div>
                                <div class="text-sm text-gray-500">${p.unit_kerja || ''}</div>
                            </div>
                        </label>
                    `).join('');

                document.getElementById('bookingEditContent').innerHTML = `
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Judul</label>
                            <input type="text" name="title" value="${booking.title}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Visibility Deskripsi</label>
                            <div class="space-y-2">
                                <label class="flex items-center"><input type="radio" name="description_visibility" value="invited_pics_only" class="mr-2" ${booking.description_visibility === 'invited_pics_only' ? 'checked' : ''}>Hanya PIC yang diundang dapat melihat deskripsi</label>
                                <label class="flex items-center"><input type="radio" name="description_visibility" value="public" class="mr-2" ${booking.description_visibility === 'public' ? 'checked' : ''}>Semua PIC dapat melihat deskripsi</label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih PIC yang akan diundang</label>
                            <div class="max-h-48 overflow-y-auto border rounded-lg p-3">
                                ${picsHtml || '<div class="text-sm text-gray-500">Tidak ada PIC tersedia</div>'}
                            </div>
                        </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                                <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">${booking.description || ''}</textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Mulai Waktu</label>
                                <input type="datetime-local" name="start_time" value="${formatTanggalWaktuLocal(booking.start_time)}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                       required onchange="checkTimeConflict()">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Selesai Waktu</label>
                                <input type="datetime-local" name="end_time" value="${formatTanggalWaktuLocal(booking.end_time)}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                       required onchange="checkTimeConflict()">
                            </div>
                        </div>
                        
                        <!-- Conflict Warning -->
                        <div id="time-conflict-warning" class="hidden mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-triangle text-red-500 mr-3 mt-1"></i>
                                <div>
                                    <h4 class="text-red-800 font-medium">Konflik Jadwal Terdeteksi!</h4>
                                    <p id="conflict-message" class="text-red-700 text-sm mt-1"></p>
                                </div>
                            </div>
                        </div>
                        <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kebutuhan Khusus</label>
                            <textarea name="special_requirements" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">${booking.special_requirements || ''}</textarea>
                        </div>
                    </div>
                `;
                openModal('bookingEditModal');
            }
        }

        function cancelBooking(bookingId) {
            currentBookingId = bookingId;
            openModal('bookingBatalModal');
        }

        function confirmBatalBooking() {
            if (currentBookingId) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/user/bookings/${currentBookingId}/cancel`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                form.appendChild(csrfToken);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function respondPreempt(bookingId, action) {
            fetch(`/user/bookings/${bookingId}/preempt-respond`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ action })
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message || 'Berhasil memproses tanggapan.');
                if (data.success) location.reload();
            })
            .catch(err => {
                alert('Gagal memproses tanggapan.');
            });
        }

        // removed proposeTimesPreempt: only 'Terima & Batalkan' is supported

        function getStatusColor(status) {
            const colors = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'confirmed': 'bg-green-100 text-green-800',
                'cancelled': 'bg-red-100 text-red-800'
            };
            return colors[status] || 'bg-gray-100 text-gray-800';
        }

        function getInvitationStatusColor(status) {
            const colors = {
                'invited': 'bg-yellow-100 text-yellow-800',
                'accepted': 'bg-green-100 text-green-800',
                'declined': 'bg-red-100 text-red-800'
            };
            return colors[status] || 'bg-gray-100 text-gray-800';
        }

        function getInvitationStatusText(status) {
            const texts = {
                'invited': 'Diundang',
                'accepted': 'Diterima',
                'declined': 'Ditolak'
            };
            return texts[status] || 'Tidak diketahui';
        }

        function calculateDuration(startWaktu, endWaktu) {
            const start = new Date(startWaktu);
            const end = new Date(endWaktu);
            const diffMs = end - start;
            const diffHours = diffMs / (1000 * 60 * 60);
            return diffHours.toFixed(1);
        }

        function formatTanggalWaktuLocal(dateWaktu) {
            const date = new Date(dateWaktu);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        }
        
        // Time conflict checking
        window.checkTimeConflict = function() {
            const startTime = document.querySelector('input[name="start_time"]').value;
            const endTime = document.querySelector('input[name="end_time"]').value;
            const warningDiv = document.getElementById('time-conflict-warning');
            const conflictMessage = document.getElementById('conflict-message');
            
            if (!startTime || !endTime) {
                warningDiv.classList.add('hidden');
                return;
            }
            
            // Check if end time is after start time
            if (new Date(endTime) <= new Date(startTime)) {
                warningDiv.classList.remove('hidden');
                conflictMessage.textContent = 'Waktu selesai harus setelah waktu mulai.';
                return;
            }
            
            // Check for conflicts with other bookings
            fetch('{{ route("user.check-availability") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    room_id: getCurrentBookingRoomId(),
                    start_time: startTime,
                    end_time: endTime,
                    exclude_booking_id: currentBookingId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.available) {
                    warningDiv.classList.add('hidden');
                } else {
                    warningDiv.classList.remove('hidden');
                    conflictMessage.innerHTML = data.message.replace(/\n/g, '<br>');
                }
            })
            .catch(error => {
                console.error('Error checking time conflict:', error);
            });
        };
        
        function getCurrentBookingRoomId() {
            // Get room ID from the current booking data
            const booking = BOOKINGS.find(b => b.id == currentBookingId);
            return booking ? booking.meeting_room.id : null;
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Status filter
            const statusFilter = document.getElementById('status-filter');
            if (statusFilter) {
                statusFilter.addEventListener('change', function() {
                    const selectedStatus = this.value;
                    const bookingItems = document.querySelectorAll('.booking-item');
                    
                    bookingItems.forEach(item => {
                        if (selectedStatus === '' || item.dataset.status === selectedStatus) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            }

            // Export functionality
            const exportBtn = document.getElementById('export-btn');
            if (exportBtn) {
                exportBtn.addEventListener('click', function() {
                    const bookings = BOOKINGS;
                    
                    // Prepare data for Excel
                    const data = bookings.map(booking => ({
                        'Judul': booking.title,
                        'Ruang': booking.meeting_room?.name || '',
                        'Mulai Waktu': booking.start_time || '',
                        'Selesai Waktu': booking.end_time || '',
                        'Status': booking.status || '',
                        'Biaya': booking.total_cost || 0
                    }));

                    // Create workbook
                    const wb = XLSX.utils.book_new();
                    const ws = XLSX.utils.json_to_sheet(data);

                    // Set column widths
                    ws['!cols'] = [
                        { wch: 30 }, // Judul
                        { wch: 20 }, // Ruang
                        { wch: 20 }, // Mulai Waktu
                        { wch: 20 }, // Selesai Waktu
                        { wch: 15 }, // Status
                        { wch: 15 }  // Biaya
                    ];

                    // Add worksheet to workbook
                    XLSX.utils.book_append_sheet(wb, ws, 'Pemesanan Saya');

                    // Generate Excel file
                    const filename = `my-bookings-${new Date().toISOString().split('T')[0]}.xlsx`;
                    XLSX.writeFile(wb, filename);
                });
            }

            // Edit form submission
            const editForm = document.getElementById('bookingEditForm');
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Collect form data manually
                    const formData = {
                        title: document.querySelector('input[name="title"]').value,
                        description: document.querySelector('textarea[name="description"]').value,
                        description_visibility: (document.querySelector('input[name="description_visibility"]:checked') || {}).value,
                        start_time: document.querySelector('input[name="start_time"]').value,
                        end_time: document.querySelector('input[name="end_time"]').value,
                        special_requirements: document.querySelector('textarea[name="special_requirements"]').value
                    };
                    const invited = Array.from(document.querySelectorAll('input[name="invited_pics[]"]:checked')).map(i => parseInt(i.value));
                    formData.invited_pics = invited;
                    
                    const bookingId = currentBookingId;
                    
                    console.log('Sending form data:', formData);
                    
                    fetch(`/user/bookings/${bookingId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(formData)
                    })
                    .then(response => {
                        if (response.status === 403) {
                            return response.json().then(data => {
                                throw new Error(data.message || 'Booking tidak dapat diedit karena sudah dikonfirmasi admin.');
                            });
                        }
                        if (response.status === 422) {
                            return response.json().then(data => {
                                throw new Error(data.message || 'Validation error occurred.');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            alert('Booking berhasil diperbarui!');
                            closeModal('bookingEditModal');
                            location.reload();
                        } else {
                            alert(data.message || 'Gagal memperbarui pemesanan');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert(error.message || 'Gagal memperbarui pemesanan');
                    });
                });
            }

            // Close modal on outside click
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('fixed')) {
                    const modals = ['bookingDetailModal', 'bookingEditModal', 'bookingBatalModal'];
                    modals.forEach(modalId => {
                        if (!document.getElementById(modalId).classList.contains('hidden')) {
                            closeModal(modalId);
                        }
                    });
                }
            });
        });

        // Auto-hide success message
        setTimeout(() => {
            const successMessage = document.getElementById('success-message');
            if (successMessage) {
                successMessage.style.transition = 'opacity 0.5s';
                successMessage.style.opacity = '0';
                setTimeout(() => successMessage.remove(), 500);
            }
        }, 1000);
    </script>

    <!-- Mobile Sidebar -->
    @include('components.mobile-sidebar', [
        'userRole' => 'user',
        'userName' => session('user_data.full_name'),
        'userEmail' => session('user_data.email'),
        'pageTitle' => 'Pemesanan Saya'
    ])

    <!-- WhatsApp Floating Button -->
    @include('components.whatsapp-float')
</body>
</html>
