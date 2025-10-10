<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kelola Bookings - Meeting Room Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/dropdown-fix.css') }}" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
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
        
        /* Mobile responsive table */
        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            .table-responsive table {
                min-width: 800px;
            }
            
            .table-responsive th,
            .table-responsive td {
                white-space: nowrap;
                min-width: 120px;
            }
            
            .table-responsive th:first-child,
            .table-responsive td:first-child {
                min-width: 60px;
            }
            
            .table-responsive th:last-child,
            .table-responsive td:last-child {
                min-width: 100px;
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
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar-alt text-2xl text-white"></i>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="hidden md:flex space-x-6">
                        <a href="{{ route('admin.dashboard') }}" class="text-white/80 hover:text-white transition-colors">
                            <i class="fas fa-tachometer-alt mr-1"></i>Beranda
                        </a>
                        <a href="{{ route('admin.users') }}" class="text-white/80 hover:text-white transition-colors">
                            <i class="fas fa-users mr-1"></i>Pengguna
                        </a>
                        <a href="{{ route('admin.rooms') }}" class="text-white/80 hover:text-white transition-colors">
                            <i class="fas fa-door-open mr-1"></i>Ruang
                        </a>
                        <a href="{{ route('admin.bookings') }}" class="text-white hover:text-white/80 transition-colors">
                            <i class="fas fa-calendar-check mr-1"></i>Pemesanan
                        </a>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-white/80 text-sm">
                            <i class="fas fa-user-shield mr-1"></i>
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
                    <h2 class="text-2xl font-bold text-white mb-2">Kelola Bookings</h2>
                    <p class="text-white/80">Pantau dan kelola semua pemesanan ruang meeting</p>
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

        <!-- Bookings Table -->
        <div class="glass-effect rounded-2xl p-6 shadow-2xl">
            @if($bookings->count() > 0)
                <div class="overflow-x-auto table-responsive">
                    <table class="w-full text-white">
                        <thead>
                            <tr class="border-b border-white/20">
                                <th class="text-left py-3 px-4 font-semibold">ID</th>
                                <th class="text-left py-3 px-4 font-semibold">Judul</th>
                                <th class="text-left py-3 px-4 font-semibold">User</th>
                                <th class="text-left py-3 px-4 font-semibold">Room</th>
                                <th class="text-left py-3 px-4 font-semibold">Tanggal & Waktu</th>
                                <th class="text-left py-3 px-4 font-semibold">Status</th>
                                <th class="text-left py-3 px-4 font-semibold">Dokumen</th>
                                <th class="text-left py-3 px-4 font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                            <tr class="border-b border-white/10 hover:bg-white/5 transition-colors booking-row" data-status="{{ $booking->status }}">
                                <td class="py-3 px-4">#{{ $booking->id }}</td>
                                <td class="py-3 px-4">
                                    <div class="min-w-0">
                                        <p class="text-white font-medium truncate" title="{{ $booking->title }}">{{ $booking->title }}</p>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="min-w-0">
                                        <p class="text-white font-medium truncate">{{ $booking->user->full_name }}</p>
                                        <p class="text-white/60 text-sm truncate" title="{{ $booking->user->email }}">{{ $booking->user->email }}</p>
                                        @if($booking->unit_kerja)
                                            <p class="text-white/60 text-xs truncate">Unit: {{ $booking->unit_kerja }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="min-w-0">
                                        <p class="text-white font-medium truncate">{{ $booking->meetingRoom->name }}</p>
                                        <p class="text-white/60 text-sm truncate">{{ $booking->meetingRoom->location }}</p>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div>
                                        <p class="text-white font-medium">{{ $booking->formatted_start_time }}</p>
                                        <p class="text-white/60 text-sm">{{ $booking->duration }} hours</p>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium
                                        @if($booking->status === 'pending') bg-yellow-500 text-white
                                        @elseif($booking->status === 'confirmed') bg-green-500 text-white
                                        @elseif($booking->status === 'cancelled') bg-red-500 text-white
                                        @elseif($booking->status === 'completed') bg-blue-500 text-white
                                        @else bg-gray-500 text-white @endif">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    @if($booking->dokumen_perizinan)
                                        <a href="{{ route('admin.bookings.download', $booking->id) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded-lg transition-colors duration-300"
                                           title="Download Dokumen Perizinan">
                                            <i class="fas fa-download mr-1"></i>
                                            Download PDF
                                        </a>
                                    @else
                                        <span class="text-white/60 text-sm">Tidak ada dokumen</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex space-x-2">
                                        <button onclick="viewBooking({{ $booking->id }})" class="text-blue-400 hover:text-blue-300 transition-colors" title="Lihat Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="updateBookingStatus({{ $booking->id }})" class="text-yellow-400 hover:text-yellow-300 transition-colors" title="Perbarui Status">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
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
                    <h3 class="text-xl font-bold text-white mb-2">No Bookings Found</h3>
                    <p class="text-white/60">There are no meeting room bookings yet.</p>
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

    <!-- Booking Status Modal -->
    <div id="bookingStatusModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Perbarui Booking Status</h3>
                    <button onclick="closeModal('bookingStatusModal')" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form id="statusPerbaruiForm">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Select status</option>
                            <option value="pending">Menunggu</option>
                            <option value="confirmed">Dikonfirmasi</option>
                            <option value="cancelled">Batalled</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reason (Optional)</label>
                        <textarea name="reason" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter reason for status change..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button" onclick="closeModal('bookingStatusModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                            Perbarui Status
                        </button>
                    </div>
                </form>
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
                                <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                                <p class="text-gray-900">${booking.user.full_name}</p>
                                <p class="text-gray-600 text-sm">${booking.user.email}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Room</label>
                                <p class="text-gray-900">${booking.meeting_room.name}</p>
                                <p class="text-gray-600 text-sm">${booking.meeting_room.location}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mulai Waktu</label>
                                <p class="text-gray-900">${new Tanggal(booking.start_time).toLocaleString()}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Selesai Waktu</label>
                                <p class="text-gray-900">${new Tanggal(booking.end_time).toLocaleString()}</p>
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

        function updateBookingStatus(bookingId) {
            currentBookingId = bookingId;
            const booking = @json($bookings->items()).find(b => b.id == bookingId);
            
            if (booking) {
                const statusSelect = document.querySelector('#statusPerbaruiForm select[name="status"]');
                statusSelect.value = booking.status;
                openModal('bookingStatusModal');
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

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Status filter
            const statusFilter = document.getElementById('status-filter');
            if (statusFilter) {
                statusFilter.addEventListener('change', function() {
                    const selectedStatus = this.value;
                    const bookingRows = document.querySelectorAll('.booking-row');
                    
                    bookingRows.forEach(row => {
                        if (selectedStatus === '' || row.dataset.status === selectedStatus) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }

            // Export functionality
            const exportBtn = document.getElementById('export-btn');
            if (exportBtn) {
                exportBtn.addEventListener('click', function() {
                    const bookings = @json($bookings->items());
                    let csv = 'ID,Judul,User,Email,Room,Mulai Waktu,Selesai Waktu,Status,Biaya\n';
                    
                    bookings.forEach(booking => {
                        csv += `"${booking.id}","${booking.title}","${booking.user.full_name}","${booking.user.email}","${booking.meeting_room.name}","${booking.start_time}","${booking.end_time}","${booking.status}","${booking.total_cost}"\n`;
                    });
                    
                    const blob = new Blob([csv], { type: 'text/csv' });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'bookings-export.csv';
                    a.click();
                    window.URL.revokeObjectURL(url);
                });
            }

            // Status update form
            const statusForm = document.getElementById('statusPerbaruiForm');
            if (statusForm) {
                statusForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (currentBookingId) {
                        const formData = new FormData(this);
                        formData.append('_method', 'POST');
                        
                        fetch(`/admin/bookings/${currentBookingId}/status`, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                                'Content-Type': 'application/x-www-form-urlencoded'
                            }
                        })
                        .then(response => {
                            console.log('Response status:', response.status);
                            if (response.ok) {
                                return response.json();
                            } else {
                                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                            }
                        })
                        .then(data => {
                            console.log('Response data:', data);
                            if (data.success) {
                                alert(data.message);
                                location.reload();
                            } else {
                                alert(data.message || 'Error updating status');
                            }
                        })
                        .catch(error => {
                            console.error('Error details:', error);
                            alert('Error updating status: ' + error.message);
                        });
                    }
                });
            }

            // Close modal on outside click
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('fixed')) {
                    const modals = ['bookingDetailModal', 'bookingStatusModal'];
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

    <!-- Mobile Sidebar -->
    @include('components.mobile-sidebar', [
        'userRole' => 'admin',
        'userName' => session('user_data.full_name'),
        'userEmail' => session('user_data.email'),
        'pageTitle' => 'Kelola Bookings'
    ])

    <!-- WhatsApp Floating Button -->
    @include('components.whatsapp-float')
</body>
</html>
