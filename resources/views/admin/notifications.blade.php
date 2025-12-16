@extends('layouts.admin')

@section('title', 'Notifikasi - Sistem Pemesanan Ruang Meeting')

@php
    $pageTitle = 'Notifikasi';
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
    
    /* Select2 styling for status filter */
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
    <div class="border border-[#071e48] bg-[#071e48] rounded-2xl p-6 mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-white mb-2">Notifikasi</h2>
                <p class="text-white">Lihat dan kelola semua notifikasi admin</p>
            </div>
            <div class="flex space-x-2">
                <button id="mark-all-read-btn" class="px-4 py-2 border border-white hover:bg-green-600 hover:border-green-600 text-white rounded-lg transition-colors duration-300 flex items-center">
                    <i data-feather="check" class="mr-2" style="width: 18px; height: 18px;"></i>Tandai Semua Dibaca
                </button>
                <button id="clear-all-btn" class="px-4 py-2 border border-white hover:bg-red-600 hover:border-red-600 text-white rounded-lg transition-colors duration-300 flex items-center">
                    <i data-feather="trash-2" class="mr-2" style="width: 18px; height: 18px;"></i>Hapus Semua
                </button>
            </div>
        </div>
    </div>

    <!-- Notifications Table -->
    <div class="rounded-2xl p-6 border border-gray-200">
        <div class="flex justify-end mb-5">
            <div style="width: 200px;">
                <select id="read-filter" class="status-filter px-4 py-2 rounded-lg border border-gray-200 bg-white text-black">
                    <option value="">Semua Notifikasi</option>
                    <option value="unread">Belum Dibaca</option>
                    <option value="read">Sudah Dibaca</option>
                </select>
            </div>
        </div>
        @if($notifications->count() > 0)
            <div class="overflow-x-auto table-responsive bg-white rounded-xl border border-gray-200">
                <table class="w-full text-black">
                    <thead class="bg-gray-100">
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 font-semibold text-black">ID</th>
                            <th class="text-left py-3 px-4 font-semibold text-black">Judul</th>
                            <th class="text-left py-3 px-4 font-semibold text-black">Pesan</th>
                            <th class="text-left py-3 px-4 font-semibold text-black">Tipe</th>
                            <th class="text-left py-3 px-4 font-semibold text-black">Waktu</th>
                            <th class="text-left py-3 px-4 font-semibold text-black">Status</th>
                            <th class="text-left py-3 px-4 font-semibold text-black">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @foreach($notifications as $notification)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors notification-row" 
                            data-read="{{ $notification->is_read ? 'read' : 'unread' }}" 
                            data-notification-id="{{ $notification->id }}">
                            <td class="py-3 px-4">#{{ $notification->id }}</td>
                            <td class="py-3 px-4">
                                <div class="min-w-0">
                                    <p class="text-black font-medium truncate" title="{{ $notification->title }}">{{ $notification->title }}</p>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="min-w-0">
                                    <p class="text-black text-sm truncate" title="{{ $notification->message }}">{{ \Illuminate\Support\Str::limit($notification->message, 50) }}</p>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    @if($notification->type === 'success') bg-green-100 text-green-800
                                    @elseif($notification->type === 'info') bg-blue-100 text-blue-800
                                    @elseif($notification->type === 'warning') bg-yellow-100 text-yellow-800
                                    @elseif($notification->type === 'error') bg-red-100 text-red-800
                                    @else bg-gray-100 text-black @endif">
                                    {{ ucfirst($notification->type) }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div>
                                    <p class="text-black font-medium">{{ $notification->created_at->format('d M Y') }}</p>
                                    <p class="text-black text-sm">{{ $notification->created_at->format('H:i') }}</p>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <span id="read-badge-{{ $notification->id }}" class="px-2 py-1 rounded-full text-xs font-medium
                                    @if($notification->is_read) bg-gray-100 text-gray-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    @if($notification->is_read) Sudah Dibaca
                                    @else Belum Dibaca
                                    @endif
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex space-x-2">
                                    <button onclick="viewNotification({{ $notification->id }})" class="text-indigo-600 hover:text-indigo-800 transition-colors" title="Lihat Detail">
                                        <i data-feather="eye" style="width: 18px; height: 18px;"></i>
                                    </button>
                                    @if(!$notification->is_read)
                                    <button onclick="markAsRead({{ $notification->id }})" class="text-green-600 hover:text-green-700 transition-colors" title="Tandai Dibaca">
                                        <i data-feather="check" style="width: 18px; height: 18px;"></i>
                                    </button>
                                    @endif
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
                    Menampilkan {{ $notifications->firstItem() }} sampai {{ $notifications->lastItem() }} dari {{ $notifications->total() }} notifikasi
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex space-x-2">
                    @if($notifications->previousPageUrl())
                    <a href="{{ $notifications->previousPageUrl() }}" class="px-3 py-2 bg-gray-100 text-black rounded-lg hover:bg-gray-200 transition-colors">
                        <i data-feather="chevron-left" style="width: 18px; height: 18px;"></i>
                    </a>
                    @endif
                    
                    @for($i = 1; $i <= $notifications->lastPage(); $i++)
                    <a href="{{ $notifications->url($i) }}" 
                       class="px-3 py-2 rounded-lg transition-colors {{ $notifications->currentPage() == $i ? 'bg-white text-indigo-600 font-semibold border border-indigo-100' : 'bg-gray-100 text-black hover:bg-gray-200' }}">
                        {{ $i }}
                    </a>
                    @endfor
                    
                    @if($notifications->nextPageUrl())
                    <a href="{{ $notifications->nextPageUrl() }}" class="px-3 py-2 bg-gray-100 text-black rounded-lg hover:bg-gray-200 transition-colors">
                        <i data-feather="chevron-right" style="width: 18px; height: 18px;"></i>
                    </a>
                    @endif
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <i data-feather="bell-off" class="text-gray-300 mb-4" style="width: 64px; height: 64px;"></i>
                <h3 class="text-xl font-bold text-black mb-2">Tidak Ada Notifikasi</h3>
                <p class="text-black">Belum ada notifikasi untuk admin.</p>
            </div>
        @endif
    </div>
@endsection

@push('modals')
    <!-- Notification Detail Modal -->
    <div id="notificationDetailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-4xl max-w-full w-[700px] max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-black">Detail Notifikasi</h3>
                    <button onclick="closeModal('notificationDetailModal')" class="text-gray-500 hover:text-gray-700">
                        <i data-feather="x" style="width: 20px; height: 20px;"></i>
                    </button>
                </div>
                <div id="notificationDetailContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
<script>
    let currentNotificationId = null;

    // Modal functions
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Notification actions
    function viewNotification(notificationId) {
        currentNotificationId = notificationId;
        const notification = @json($notifications->items()).find(n => n.id == notificationId);
        
        if (notification) {
            document.getElementById('notificationDetailContent').innerHTML = `
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xl font-bold text-black">${notification.title}</h4>
                        <span class="px-3 py-1 rounded-full text-sm font-medium ${getTypeColor(notification.type)}">
                            ${notification.type.charAt(0).toUpperCase() + notification.type.slice(1)}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <p class="text-gray-900">${notification.is_read ? 'Sudah Dibaca' : 'Belum Dibaca'}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Waktu</label>
                            <p class="text-gray-900">${new Date(notification.created_at).toLocaleString('id-ID')}</p>
                        </div>
                        ${notification.booking_id ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Booking ID</label>
                            <p class="text-gray-900">#${notification.booking_id}</p>
                        </div>
                        ` : ''}
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pesan</label>
                        <p class="text-gray-900 whitespace-pre-wrap">${notification.message}</p>
                    </div>
                </div>
            `;
            openModal('notificationDetailModal');
        }
    }

    function markAsRead(notificationId) {
        fetch(`/admin/notifications/${notificationId}/mark-read`, {
            method: 'POST',
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

            if (response.ok && data && data.success) {
                // Update the table row status immediately
                const notificationRow = document.querySelector(`tr[data-notification-id="${notificationId}"]`);
                const readBadge = document.getElementById(`read-badge-${notificationId}`);
                
                if (notificationRow && readBadge) {
                    // Update data-read attribute
                    notificationRow.setAttribute('data-read', 'read');
                    
                    // Update read badge
                    readBadge.className = 'px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800';
                    readBadge.textContent = 'Sudah Dibaca';
                    
                    // Remove the mark as read button
                    const markReadBtn = notificationRow.querySelector('button[onclick*="markAsRead"]');
                    if (markReadBtn) {
                        markReadBtn.remove();
                    }
                }
            } else {
                const errorMessage = data?.message || 'Gagal menandai notifikasi sebagai dibaca';
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: errorMessage
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gagal menandai notifikasi sebagai dibaca: ' + error.message
            });
        });
    }

    function getTypeColor(type) {
        const colors = {
            'success': 'bg-green-100 text-green-800',
            'info': 'bg-blue-100 text-blue-800',
            'warning': 'bg-yellow-100 text-yellow-800',
            'error': 'bg-red-100 text-red-800'
        };
        return colors[type] || 'bg-gray-100 text-black';
    }

    // Initialize Select2 for read filter
    function initReadFilterSelect2() {
        if (typeof $ === 'undefined') {
            console.error('jQuery tidak dimuat.');
            return;
        }

        $('#read-filter').select2({
            theme: 'bootstrap-5',
            placeholder: 'Semua Notifikasi',
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
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2 for read filter
        initReadFilterSelect2();
        
        // Read filter change event
        $(document).on('change', '#read-filter', function() {
            const selectedFilter = $(this).val();
            const notificationRows = document.querySelectorAll('.notification-row');
            
            notificationRows.forEach(row => {
                if (selectedFilter === '' || selectedFilter === null || row.dataset.read === selectedFilter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Mark all as read button
        const markAllReadBtn = document.getElementById('mark-all-read-btn');
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', function() {
                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin menandai semua notifikasi sebagai dibaca?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, tandai semua',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('{{ route("admin.notifications.mark-all-read") }}', {
                            method: 'POST',
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

                            if (response.ok && data && data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Semua notifikasi telah ditandai sebagai dibaca',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    // Reload the page to show updated status
                                    window.location.reload();
                                });
                            } else {
                                const errorMessage = data?.message || 'Gagal menandai semua notifikasi sebagai dibaca';
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: errorMessage
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Gagal menandai semua notifikasi sebagai dibaca: ' + error.message
                            });
                        });
                    }
                });
            });
        }

        // Clear all button
        const clearAllBtn = document.getElementById('clear-all-btn');
        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', function() {
                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin menghapus semua notifikasi? Tindakan ini tidak dapat dibatalkan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus semua',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('{{ route("admin.notifications.clear") }}', {
                            method: 'DELETE',
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

                            if (response.ok && data && data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Semua notifikasi telah dihapus',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    // Reload the page to show updated status
                                    window.location.reload();
                                });
                            } else {
                                const errorMessage = data?.message || 'Gagal menghapus semua notifikasi';
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: errorMessage
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Gagal menghapus semua notifikasi: ' + error.message
                            });
                        });
                    }
                });
            });
        }

        // Close modal on outside click
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('fixed')) {
                const modals = ['notificationDetailModal'];
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

