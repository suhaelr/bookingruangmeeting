@extends('layouts.admin')

@section('title', 'Kelola Pemesanan - Sistem Pemesanan Ruang Meeting')

@php
    $pageTitle = 'Kelola Pemesanan';
@endphp

@push('styles')
<style>
    body,
    body.gradient-bg {
        background: #ffffff !important;
        background-image: none !important;
        color: #000000 !important;
    }

    /* Filter select */
    .status-filter {
        background-color: #ffffff !important;
        color: #000000 !important;
        border: 1px solid #d1d5db !important;
    }
    .status-filter option {
        background-color: #ffffff !important;
        color: #000000 !important;
    }

    /* Loading spinner for submit button */
    .loading-spinner {
        display: inline-block;
    }
    .loading-spinner.hidden {
        display: none;
    }
    .submit-text.hidden {
        display: none;
    }
    button:disabled {
        pointer-events: none;
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
@endpush

@section('main-content')
    <!-- Header -->
    <div class="border border-gray-200 rounded-2xl p-6 mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-black mb-2">Kelola Pemesanan</h2>
                <p class="text-black">Pantau dan kelola semua pemesanan ruang meeting</p>
            </div>
            <div class="flex space-x-4">
                <select id="status-filter" class="status-filter px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
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
    <div class="rounded-2xl p-6 border border-gray-200">
        @if($bookings->count() > 0)
            <div class="overflow-x-auto table-responsive bg-white rounded-xl border border-gray-200">
                <table class="w-full text-black">
                    <thead class="bg-gray-100">
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 font-semibold text-black">ID</th>
                            <th class="text-left py-3 px-4 font-semibold text-black">Judul</th>
                            <th class="text-left py-3 px-4 font-semibold text-black">Pengguna</th>
                            <th class="text-left py-3 px-4 font-semibold text-black">Ruang</th>
                            <th class="text-left py-3 px-4 font-semibold text-black">Tanggal & Waktu</th>
                            <th class="text-left py-3 px-4 font-semibold text-black">Status</th>
                            <th class="text-left py-3 px-4 font-semibold text-black">Dokumen</th>
                            <th class="text-left py-3 px-4 font-semibold text-black">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @foreach($bookings as $booking)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors booking-row" data-status="{{ $booking->status }}" data-booking-id="{{ $booking->id }}">
                            <td class="py-3 px-4">#{{ $booking->id }}</td>
                            <td class="py-3 px-4">
                                <div class="min-w-0">
                                    <p class="text-black font-medium truncate" title="{{ $booking->title }}">{{ $booking->title }}</p>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="min-w-0">
                                    <p class="text-black font-medium truncate">{{ $booking->user->full_name }}</p>
                                    <p class="text-black text-sm truncate" title="{{ $booking->user->email }}">{{ $booking->user->email }}</p>
                                    @if($booking->unit_kerja)
                                        <p class="text-black text-xs truncate">Unit: {{ $booking->unit_kerja }}</p>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="min-w-0">
                                    <p class="text-black font-medium truncate">{{ $booking->meetingRoom->name }}</p>
                                    <p class="text-black text-sm truncate">{{ $booking->meetingRoom->location }}</p>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <div>
                                    <p class="text-black font-medium">{{ $booking->start_time->format('d M Y') }}</p>
                                    <p class="text-black text-sm">{{ $booking->start_time->format('H:i') }} - {{ $booking->end_time->format('H:i') }}</p>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <span id="status-badge-{{ $booking->id }}" class="px-2 py-1 rounded-full text-xs font-medium
                                    @if($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($booking->status === 'confirmed') bg-green-100 text-green-800
                                    @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                                    @elseif($booking->status === 'completed') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-black @endif">
                                    @if($booking->status === 'pending') Menunggu
                                    @elseif($booking->status === 'confirmed') Dikonfirmasi
                                    @elseif($booking->status === 'cancelled') Dibatalkan
                                    @elseif($booking->status === 'completed') Selesai
                                    @else {{ ucfirst($booking->status) }}
                                    @endif
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                @if($booking->dokumen_perizinan)
                                    <a href="{{ route('admin.bookings.download', $booking->id) }}" 
                                       class="inline-flex items-center px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded-lg transition-colors duration-300"
                                       title="Download Dokumen Perizinan">
                                        <i data-feather="download" class="mr-1 inline" style="width: 16px; height: 16px;"></i>
                                        Download PDF
                                    </a>
                                @else
                                    <span class="text-black text-sm">Tidak ada dokumen</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex space-x-2">
                                    <button onclick="viewBooking({{ $booking->id }})" class="text-indigo-600 hover:text-indigo-800 transition-colors" title="Lihat Detail">
                                        <i data-feather="eye" style="width: 18px; height: 18px;"></i>
                                    </button>
                                    <button onclick="updateBookingStatus({{ $booking->id }})" class="text-yellow-600 hover:text-yellow-700 transition-colors" title="Perbarui Status">
                                        <i data-feather="edit" style="width: 18px; height: 18px;"></i>
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
                <div class="text-black text-sm">
                    Menampilkan {{ $bookings->firstItem() }} sampai {{ $bookings->lastItem() }} dari {{ $bookings->total() }} pemesanan
                </div>
                <div class="flex items-center space-x-4">
                    <button id="export-btn" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors duration-300 flex items-center">
                        <i data-feather="download" class="mr-2" style="width: 18px; height: 18px;"></i>Ekspor
                    </button>
                    <div class="flex space-x-2">
                    @if($bookings->previousPageUrl())
                    <a href="{{ $bookings->previousPageUrl() }}" class="px-3 py-2 bg-gray-100 text-black rounded-lg hover:bg-gray-200 transition-colors">
                        <i data-feather="chevron-left" style="width: 18px; height: 18px;"></i>
                    </a>
                    @endif
                    
                    @for($i = 1; $i <= $bookings->lastPage(); $i++)
                    <a href="{{ $bookings->url($i) }}" 
                       class="px-3 py-2 rounded-lg transition-colors {{ $bookings->currentPage() == $i ? 'bg-white text-indigo-600 font-semibold border border-indigo-100' : 'bg-gray-100 text-black hover:bg-gray-200' }}">
                        {{ $i }}
                    </a>
                    @endfor
                    
                    @if($bookings->nextPageUrl())
                    <a href="{{ $bookings->nextPageUrl() }}" class="px-3 py-2 bg-gray-100 text-black rounded-lg hover:bg-gray-200 transition-colors">
                        <i data-feather="chevron-right" style="width: 18px; height: 18px;"></i>
                    </a>
                    @endif
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <i data-feather="calendar" class="text-gray-300 mb-4" style="width: 64px; height: 64px;"></i>
                <h3 class="text-xl font-bold text-black mb-2">Tidak Ada Booking</h3>
                <p class="text-black">Belum ada pemesanan ruang meeting.</p>
            </div>
        @endif
    </div>
@endsection

@push('modals')
    <!-- Booking Detail Modal -->
    <div id="bookingDetailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-4xl max-w-full w-[700px] max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-black">Detail Booking</h3>
                    <button onclick="closeModal('bookingDetailModal')" class="text-gray-500 hover:text-gray-700">
                        <i data-feather="x" style="width: 20px; height: 20px;"></i>
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
        <div class="bg-white rounded-2xl max-w-full w-[700px]">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-black">Perbarui Booking Status</h3>
                    <button onclick="closeModal('bookingStatusModal')" class="text-gray-500 hover:text-gray-700">
                        <i data-feather="x" style="width: 20px; height: 20px;"></i>
                    </button>
                </div>
                <form id="statusPerbaruiForm">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <div class="relative">
                            <select name="status" class="w-full appearance-none px-3 py-2 pr-10 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Pilih status</option>
                                <option value="pending">Menunggu</option>
                                <option value="confirmed">Dikonfirmasi</option>
                                <option value="cancelled">Dibatalkan</option>
                                <option value="completed">Selesai</option>
                            </select>
                            <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-500">
                                <i data-feather="chevron-down" style="width: 18px; height: 18px;"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alasan (Opsional)</label>
                        <textarea name="reason" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Masukkan alasan perubahan status..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button" onclick="closeModal('bookingStatusModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Batal
                        </button>
                        <button type="submit" id="submitStatusBtn" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center">
                            <span class="submit-text">Perbarui Status</span>
                            <i data-feather="loader" class="loading-spinner hidden ml-2 animate-spin inline" style="width: 18px; height: 18px;"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
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
        
        // Reset form and button state when closing status modal
        if (modalId === 'bookingStatusModal') {
            const statusForm = document.getElementById('statusPerbaruiForm');
            if (statusForm) {
                statusForm.reset();
            }
            
            const submitBtn = document.getElementById('submitStatusBtn');
            const submitText = submitBtn?.querySelector('.submit-text');
            const loadingSpinner = submitBtn?.querySelector('.loading-spinner');
            
            if (submitBtn) {
                submitBtn.disabled = false;
                if (submitText) submitText.classList.remove('hidden');
                if (loadingSpinner) loadingSpinner.classList.add('hidden');
            }
        }
    }
    
    function resetSubmitButton() {
        const submitBtn = document.getElementById('submitStatusBtn');
        const submitText = submitBtn?.querySelector('.submit-text');
        const loadingSpinner = submitBtn?.querySelector('.loading-spinner');
        
        if (submitBtn) {
            submitBtn.disabled = false;
            if (submitText) submitText.classList.remove('hidden');
            if (loadingSpinner) loadingSpinner.classList.add('hidden');
        }
    }

    // Booking actions
    function viewBooking(bookingId) {
        currentBookingId = bookingId;
        const booking = @json($bookings->items()).find(b => b.id == bookingId);
        
        if (booking) {
            document.getElementById('bookingDetailContent').innerHTML = `
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xl font-bold text-black">${booking.title}</h4>
                        <span class="px-3 py-1 rounded-full text-sm font-medium ${getStatusColor(booking.status)}">
                            ${getStatusText(booking.status)}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pengguna</label>
                            <p class="text-gray-900">${booking.user.full_name}</p>
                            <p class="text-black text-sm">${booking.user.email}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ruang</label>
                            <p class="text-gray-900">${booking.meeting_room.name}</p>
                            <p class="text-black text-sm">${booking.meeting_room.location}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mulai Waktu</label>
                            <p class="text-gray-900">${new Date(booking.start_time).toLocaleString('id-ID')}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Selesai Waktu</label>
                            <p class="text-gray-900">${new Date(booking.end_time).toLocaleString('id-ID')}</p>
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
                                ${booking.attendees.map(email => `<span class="inline-block bg-gray-100 text-black px-2 py-1 rounded text-sm mr-2 mb-1">${email}</span>`).join('')}
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
            'cancelled': 'bg-red-100 text-red-800',
            'completed': 'bg-blue-100 text-blue-800'
        };
        return colors[status] || 'bg-gray-100 text-black';
    }
    
    function getStatusText(status) {
        const statusTexts = {
            'pending': 'Menunggu',
            'confirmed': 'Dikonfirmasi',
            'cancelled': 'Dibatalkan',
            'completed': 'Selesai'
        };
        return statusTexts[status] || status;
    }

    function calculateDuration(startWaktu, endWaktu) {
        const start = new Date(startWaktu);
        const end = new Date(endWaktu);
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
                window.location.href = '{{ route("admin.export.bookings.excel") }}';
            });
        }

        // Status update form
        const statusForm = document.getElementById('statusPerbaruiForm');
        if (statusForm) {
            statusForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Prevent double submission
                const submitBtn = document.getElementById('submitStatusBtn');
                if (submitBtn && submitBtn.disabled) {
                    return;
                }
                
                if (currentBookingId) {
                    // Get submit button and loading elements
                    const submitText = submitBtn?.querySelector('.submit-text');
                    const loadingSpinner = submitBtn?.querySelector('.loading-spinner');
                    
                    // Show loading state
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        if (submitText) submitText.classList.add('hidden');
                        if (loadingSpinner) loadingSpinner.classList.remove('hidden');
                    }
                    
                    const formData = new FormData(this);
                    formData.append('_method', 'POST');
                    
                    fetch(`/admin/bookings/${currentBookingId}/status`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(async response => {
                        let data = null;
                        try {
                            data = await response.json();
                        } catch (jsonError) {
                            console.error('JSON parsing error:', jsonError);
                        }

                        if (response.ok && data) {
                            return data;
                        }

                        const errorMessage = data?.message
                            ?? (data?.errors ? Object.values(data.errors).flat().join(', ') : null)
                            ?? `HTTP ${response.status}: ${response.statusText}`;

                        throw new Error(errorMessage);
                    })
                    .then(data => {
                        if (data.success) {
                            // Get the new status from the form select
                            const statusSelect = document.querySelector('#statusPerbaruiForm select[name="status"]');
                            const newStatus = statusSelect ? statusSelect.value : null;
                            
                            if (newStatus) {
                                // Update the table row status immediately
                                const bookingRow = document.querySelector(`tr[data-booking-id="${currentBookingId}"]`);
                                const statusBadge = document.getElementById(`status-badge-${currentBookingId}`);
                                
                                if (bookingRow && statusBadge) {
                                    // Update data-status attribute
                                    bookingRow.setAttribute('data-status', newStatus);
                                    
                                    // Update status badge
                                    statusBadge.className = `px-2 py-1 rounded-full text-xs font-medium ${getStatusColor(newStatus)}`;
                                    statusBadge.textContent = getStatusText(newStatus);
                                }
                            }
                            
                            // Close modal immediately
                            closeModal('bookingStatusModal');
                            
                            // Show success notification (optional - you can replace with a toast library)
                            // For now, we'll just close the modal silently for better UX
                            // You can uncomment the alert if you want to show a message
                            // alert(data.message || 'Status berhasil diperbarui!');
                        } else {
                            alert(data.message || 'Gagal memperbarui status');
                            resetSubmitButton();
                        }
                    })
                    .catch(error => {
                        console.error('Error details:', error);
                        alert('Gagal memperbarui status: ' + error.message);
                        resetSubmitButton();
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
</script>
@endpush
