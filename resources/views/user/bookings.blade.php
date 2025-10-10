<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Bookings - Meeting Room Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
        }
        
        .booking-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen">
    <!-- Navigation -->
    <nav class="glass-effect shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar-alt text-2xl text-white"></i>
                    </div>
                    <div class="ml-4">
                        <h1 class="text-xl font-bold text-white">{{ session('user_data.full_name') ?? 'Pengguna' }}</h1>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('user.dashboard') }}" class="text-white/80 hover:text-white transition-colors">
                        <i class="fas fa-arrow-left mr-1"></i>Kembali ke Beranda
                    </a>
                    <a href="{{ route('user.bookings.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors duration-300 flex items-center">
                        <i class="fas fa-plus mr-2"></i>Pemesanan Baru
                    </a>
                    <div class="flex items-center space-x-2">
                        <span class="text-white/80 text-sm">
                            <i class="fas fa-user mr-1"></i>
                            {{ session('user_data.full_name') }}
                        </span>
                        <a href="{{ route('logout') }}" 
                           class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors duration-300 flex items-center">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Keluar
                        </a>
                    </div>
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
                    <h2 class="text-2xl font-bold text-white mb-2">My Meeting Bookings</h2>
                    <p class="text-white/80">Manage and track your meeting room reservations</p>
                </div>
                <div class="flex space-x-4">
                    <select id="status-filter" class="px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-white/50">
                        <option value="">All Status</option>
                        <option value="pending">Menunggu</option>
                        <option value="confirmed">Dikonfirmasi</option>
                        <option value="cancelled">Batalled</option>
                        <option value="completed">Selesai</option>
                    </select>
                    <button id="export-btn" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors duration-300 flex items-center">
                        <i class="fas fa-download mr-2"></i>Export
                    </button>
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
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h3 class="text-lg font-bold text-white mb-1">{{ $booking->title }}</h3>
                                        <p class="text-white/80 text-sm mb-2">{{ $booking->meetingRoom->name }} • {{ $booking->meetingRoom->location }}</p>
                                        <div class="flex items-center space-x-4 text-sm text-white/60">
                                            <span><i class="fas fa-calendar mr-1"></i>{{ $booking->formatted_start_time }}</span>
                                            <span><i class="fas fa-clock mr-1"></i>{{ $booking->duration }} hours</span>
                                            <span><i class="fas fa-users mr-1"></i>{{ $booking->attendees_count }} attendees</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="px-3 py-1 rounded-full text-sm font-medium
                                            @if($booking->status === 'pending') bg-yellow-500 text-white
                                            @elseif($booking->status === 'confirmed') bg-green-500 text-white
                                            @elseif($booking->status === 'cancelled') bg-red-500 text-white
                                            @elseif($booking->status === 'completed') bg-blue-500 text-white
                                            @else bg-gray-500 text-white @endif">
                                            {{ $booking->status_text }}
                                        </span>
                                        <p class="text-white/60 text-sm mt-1">Rp {{ number_format($booking->total_cost, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                
                                @if($booking->description)
                                <p class="text-white/80 text-sm mb-3">{{ $booking->description }}</p>
                                @endif
                                
                                @if($booking->special_requirements)
                                <div class="bg-yellow-500/20 border border-yellow-500/30 rounded-lg p-3 mb-3">
                                    <p class="text-yellow-300 text-sm">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        <strong>Special Requirements:</strong> {{ $booking->special_requirements }}
                                    </p>
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
                                        <strong>Batallation Reason:</strong> {{ $booking->cancellation_reason }}
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
                                
                                <button onclick="editBooking({{ $booking->id }})" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-300 flex items-center">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="flex justify-between items-center mt-8">
                    <div class="text-white/80 text-sm">
                        Showing {{ $bookings->firstItem() }} to {{ $bookings->lastItem() }} of {{ $bookings->total() }} bookings
                    </div>
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
            @else
                <div class="text-center py-12">
                    <i class="fas fa-calendar-times text-white/40 text-6xl mb-4"></i>
                    <h3 class="text-xl font-bold text-white mb-2">No Bookings Found</h3>
                    <p class="text-white/60 mb-6">You haven't made any meeting room bookings yet.</p>
                    <a href="{{ route('user.bookings.create') }}" 
                       class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors duration-300 inline-flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        Book Your First Meeting Room
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
                    <h3 class="text-2xl font-bold text-gray-800">Booking Details</h3>
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
                    <h3 class="text-2xl font-bold text-gray-800">Edit Booking</h3>
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
                        <p class="text-gray-600">This action cannot be undone</p>
                    </div>
                </div>
                <p class="text-gray-700 mb-6">Are you sure you want to cancel this booking?</p>
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
            const booking = @json($bookings->items()).find(b => b.id == bookingId);
            
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
                                <label class="block text-sm font-medium text-gray-700 mb-1">Room</label>
                                <p class="text-gray-900">${booking.meeting_room.name}</p>
                                <p class="text-gray-600 text-sm">${booking.meeting_room.location}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal & Waktu</label>
                                <p class="text-gray-900">${new Tanggal(booking.start_time).toLocaleTanggalString()}</p>
                                <p class="text-gray-600 text-sm">${new Tanggal(booking.start_time).toLocaleWaktuString()} - ${new Tanggal(booking.end_time).toLocaleWaktuString()}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Duration</label>
                                <p class="text-gray-900">${calculateDuration(booking.start_time, booking.end_time)} hours</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Total Biaya</label>
                                <p class="text-gray-900 font-semibold">Rp ${new Intl.NumberFormat('id-ID').format(booking.total_cost)}</p>
                            </div>
                        </div>
                        
                        ${booking.description ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <p class="text-gray-900">${booking.description}</p>
                        </div>
                        ` : ''}
                        
                        ${booking.special_requirements ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Special Requirements</label>
                            <p class="text-gray-900">${booking.special_requirements}</p>
                        </div>
                        ` : ''}
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Peserta</label>
                            <p class="text-gray-900">${booking.attendees_count} people</p>
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
            const booking = @json($bookings->items()).find(b => b.id == bookingId);
            
            if (booking) {
                document.getElementById('bookingEditContent').innerHTML = `
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Judul</label>
                            <input type="text" name="title" value="${booking.title}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">${booking.description || ''}</textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Mulai Waktu</label>
                                <input type="datetime-local" name="start_time" value="${formatTanggalWaktuLocal(booking.start_time)}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Selesai Waktu</label>
                                <input type="datetime-local" name="end_time" value="${formatTanggalWaktuLocal(booking.end_time)}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Special Requirements</label>
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

        function getStatusColor(status) {
            const colors = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'confirmed': 'bg-green-100 text-green-800',
                'cancelled': 'bg-red-100 text-red-800'
            };
            return colors[status] || 'bg-gray-100 text-gray-800';
        }

        function calculateDuration(startWaktu, endWaktu) {
            const start = new Tanggal(startWaktu);
            const end = new Tanggal(endWaktu);
            const diffMs = end - start;
            const diffHours = diffMs / (1000 * 60 * 60);
            return diffHours.toFixed(1);
        }

        function formatTanggalWaktuLocal(dateWaktu) {
            const date = new Tanggal(dateWaktu);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padMulai(2, '0');
            const day = String(date.getTanggal()).padMulai(2, '0');
            const hours = String(date.getHours()).padMulai(2, '0');
            const minutes = String(date.getMinutes()).padMulai(2, '0');
            return `${year}-${month}-${day}T${hours}:${minutes}`;
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
                    const bookings = @json($bookings->items());
                    let csv = 'Judul,Room,Mulai Waktu,Selesai Waktu,Status,Biaya\n';
                    
                    bookings.forEach(booking => {
                        csv += `"${booking.title}","${booking.meeting_room.name}","${booking.start_time}","${booking.end_time}","${booking.status}","${booking.total_cost}"\n`;
                    });
                    
                    const blob = new Blob([csv], { type: 'text/csv' });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'my-bookings.csv';
                    a.click();
                    window.URL.revokeObjectURL(url);
                });
            }

            // Edit form submission
            const editForm = document.getElementById('bookingEditForm');
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const bookingId = currentBookingId;
                    
                    fetch(`/user/bookings/${bookingId}`, {
                        method: 'PUT',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Booking updated successfully!');
                            closeModal('bookingEditModal');
                            location.reload();
                        } else {
                            alert(data.message || 'Error updating booking');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error updating booking');
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
        setWaktuout(() => {
            const successMessage = document.getElementById('success-message');
            if (successMessage) {
                successMessage.style.transition = 'opacity 0.5s';
                successMessage.style.opacity = '0';
                setWaktuout(() => successMessage.remove(), 500);
            }
        }, 3000);
    </script>

    <!-- WhatsApp Floating Button -->
    @include('components.whatsapp-float')
</body>
</html>
