@extends('layouts.admin')

@section('title', 'Dashboard Admin - Sistem Pemesanan Ruang Meeting')

@php
    $pageTitle = 'Beranda Admin';
@endphp

@push('seo-meta')
    @include('components.seo-meta', [
        'page' => 'admin_dashboard',
        'title' => 'Dashboard Admin - Sistem Pemesanan Ruang Meeting',
        'description' => 'Dashboard admin untuk mengelola sistem pemesanan ruang meeting. Kelola pengguna, ruang meeting, dan pemesanan.',
        'keywords' => 'dashboard admin, manajemen sistem, pengguna, ruang meeting, pemesanan',
        'canonical' => '/admin/dashboard',
        'robots' => 'noindex, nofollow'
    ])
@endpush

@push('nav-actions')
    <!-- Admin Notification Bell -->
    <div class="relative">
        <button onclick="toggleAdminNotifikasis()" class="relative p-2 text-black hover:text-indigo-600 transition-colors duration-300">
            <i data-feather="bell" style="width: 20px; height: 20px;"></i>
            <span id="admin-notification-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
        </button>
    </div>
@endpush

@section('main-content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-4 md:gap-6 mb-8">
        <div class="border border-gray-200 rounded-2xl p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-50 rounded-lg">
                    <i data-feather="users" class="text-blue-500 w-[20px] h-[20px]"></i>
                </div>
                <div class="ml-4">
                    <p class="text-black text-sm">Total Pengguna</p>
                    <p class="text-2xl font-bold text-black">{{ $stats['total_users'] }}</p>
                </div>
            </div>
        </div>

        <div class="border border-gray-200 rounded-2xl p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-50 rounded-lg">
                    <i data-feather="box" class="text-green-500 w-[20px] h-[20px]"></i>
                </div>
                <div class="ml-4">
                    <p class="text-black text-sm">Ruang Meeting</p>
                    <p class="text-2xl font-bold text-black">{{ $stats['total_rooms'] }}</p>
                </div>
            </div>
        </div>

        <div class="border border-gray-200 rounded-2xl p-6 ">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-50 rounded-lg">
                    <i data-feather="calendar" class="text-yellow-500 w-[20px] h-[20px]"></i>
                </div>
                <div class="ml-4">
                    <p class="text-black text-sm">Total Pemesanan</p>
                    <p class="text-2xl font-bold text-black">{{ $stats['total_bookings'] }}</p>
                </div>
            </div>
        </div>

        <div class="border border-gray-200 rounded-2xl p-6 ">
            <div class="flex items-center">
                <div class="p-3 bg-purple-50 rounded-lg">
                    <i data-feather="check-circle" class="text-purple-500 w-[20px] h-[20px]"></i>
                </div>
                <div class="ml-4">
                    <p class="text-black text-sm">Pemesanan Dikonfirmasi</p>
                    <p class="text-2xl font-bold text-black">{{ $stats['confirmed_bookings'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Section -->
    <div class="border border-[#071e48] bg-[#071e48] rounded-2xl p-6  mb-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-xl font-bold text-white mb-2">Export Data</h3>
                <p class="text-white text-sm">Export data riwayat booking dan status ketersediaan ruangan (24 jam terakhir)</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <button onclick="exportBookingData()" 
                        class="px-6 py-3 border-2 border-white hover:bg-green-500 hover:border-green-500 text-white hover:text-white rounded-lg transition-colors duration-300 flex items-center">
                    <i data-feather="file-text" class="mr-2" style="width: 18px; height: 18px;"></i>
                    Export Excel
                </button>
                <button onclick="exportBookingPDF()" 
                        class="px-6 py-3 border-2 border-white hover:bg-red-500 hover:border-red-500 text-white hover:text-white rounded-lg transition-colors duration-300 flex items-center">
                    <i data-feather="file" class="mr-2" style="width: 18px; height: 18px;"></i>
                    Export PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Recent Bookings & Today's Bookings -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch">
        <!-- Recent Bookings -->
        <div class="border border-gray-200 rounded-2xl p-6 flex flex-col">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-black">Pemesanan Terbaru</h3>
                <a href="{{ route('admin.bookings') }}" class="text-black hover:text-black text-sm">
                    Lihat Semua <i data-feather="arrow-right" class="ml-1 inline" style="width: 16px; height: 16px;"></i>
                </a>
            </div>
            <div class="space-y-4 flex-1 relative min-h-[200px] max-h-[500px] pr-1 overflow-y-auto">
                @forelse($recentBookings as $booking)
                <div class="flex items-center justify-between p-4 rounded-lg border @if($booking->status === 'pending') bg-yellow-50 border-yellow-500 @elseif($booking->status === 'confirmed') bg-green-50 border-green-500 @elseif($booking->status === 'cancelled') bg-red-50 border-red-500 @else bg-white border-gray-200 @endif">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 @if($booking->status === 'pending') bg-yellow-500 @elseif($booking->status === 'confirmed') bg-green-500 @elseif($booking->status === 'cancelled') bg-red-500 @else bg-gray-500 @endif rounded-full flex items-center justify-center">
                            <i data-feather="calendar" class="text-white w-[20px] h-[20px]"></i>
                        </div>
                        <div>
                            <p class="text-black font-medium">{{ $booking->title }}</p>
                            <p class="text-black text-sm">{{ $booking->user->full_name }} • {{ $booking->meetingRoom->name }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            @if($booking->status === 'pending') bg-yellow-50 text-yellow-500
                            @elseif($booking->status === 'confirmed') bg-green-50 text-green-500
                            @elseif($booking->status === 'cancelled') bg-red-50 text-red-500
                            @else bg-gray-50 text-gray-500 @endif">
                            {{ ucfirst($booking->status) }}
                        </span>
                        <p class="text-black text-sm mt-1">{{ $booking->created_at ? $booking->created_at->format('M d, H:i') : 'Tidak tersedia' }}</p>
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center absolute inset-0">
                    <i data-feather="calendar" class="text-gray-300 mb-4 w-[48px] h-[48px]"></i>
                    <p class="text-black text-center">Tidak ada pemesanan terbaru</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Today's Bookings -->
        <div class="border border-gray-200 rounded-2xl p-6 flex flex-col">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-black">Pemesanan Hari Ini</h3>
                <span class="text-black text-sm">{{ now()->format('M d, Y') }}</span>
            </div>
            <div class="space-y-4 relative min-h-[200px] flex-1 max-h-[500px] pr-1 overflow-y-auto">
                @forelse($todayBookings as $booking)
                <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border border-blue-500">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                            <i data-feather="clock" class="text-white w-[20px] h-[20px]"></i>
                        </div>
                        <div>
                            <p class="text-black font-medium">{{ $booking->title }}</p>
                            <p class="text-black text-sm">{{ $booking->meetingRoom->name }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-black font-medium">{{ $booking->start_time ? $booking->start_time->format('H:i') : 'Tidak tersedia' }} - {{ $booking->end_time ? $booking->end_time->format('H:i') : 'Tidak tersedia' }}</p>
                        <p class="text-black text-sm">{{ $booking->user->full_name }}</p>
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center absolute inset-0">
                    <i data-feather="calendar" class="text-gray-300 mb-4 w-[48px] h-[48px]"></i>
                    <p class="text-black text-center">Tidak ada pemesanan hari ini</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('modals')
    <!-- Admin Notifikasi Dropdown -->
    <div id="adminNotifikasiDropdown" class="fixed top-16 right-4 bg-white rounded-lg shadow-lg border hidden z-50 w-80 max-h-96 overflow-y-auto">
        <div class="p-4 border-b">
            <h3 class="font-semibold text-black">Admin Notifikasi</h3>
        </div>
        <div id="adminNotifikasiList" class="p-2">
            <!-- Admin notifications will be loaded here -->
        </div>
        <div class="p-2 border-t flex space-x-2">
            <button onclick="markAllAdminNotifikasisAsRead()" class="flex-1 text-center text-black hover:text-gray-800 text-sm py-2">
                Tandai Semua Sebagai Terbaca
            </button>
            <button onclick="clearAllAdminNotifikasis()" class="flex-1 text-center text-red-500 hover:text-red-700 text-sm py-2">
                Hapus Semua
            </button>
        </div>
    </div>
@endpush

@push('scripts')
<script>
    // Admin notification functions
    function toggleAdminNotifikasis() {
        const dropdown = document.getElementById('adminNotifikasiDropdown');
        
        if (dropdown.classList.contains('hidden')) {
            dropdown.classList.remove('hidden');
            loadAdminNotifikasis();
        } else {
            dropdown.classList.add('hidden');
        }
    }

    function loadAdminNotifikasis() {
        const notificationList = document.getElementById('adminNotifikasiList');
        
        // Fetch notifications from backend
        fetch('/admin/notifications')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(notifications => {
                console.log('Loaded admin notifications:', notifications);
                
                // Calculate unread count - ensure read is boolean
                const unreadCount = notifications.filter(n => !n.read).length;
                const badge = document.getElementById('admin-notification-badge');
                
                console.log('Unread count:', unreadCount);
                
                if (unreadCount > 0) {
                    badge.textContent = unreadCount;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }

                if (notifications.length === 0) {
                    notificationList.innerHTML = '<div class="p-3 text-center text-gray-500">Tidak ada notifikasi</div>';
                    return;
                }

                notificationList.innerHTML = notifications.map(notification => `
                    <div class="p-3 border-b hover:bg-gray-50 cursor-pointer ${!notification.read ? 'bg-blue-50' : ''}" onclick="markAdminNotifikasiAsRead(${notification.id})">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i data-feather="${getAdminNotifikasiIcon(notification.type)}" class="text-${getAdminNotifikasiWarna(notification.type)}" style="width: 18px; height: 18px;"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-gray-900">${notification.title}</p>
                                <p class="text-sm text-black">${notification.message}</p>
                                <p class="text-xs text-gray-400 mt-1">${notification.time}</p>
                            </div>
                            ${!notification.read ? '<div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>' : ''}
                        </div>
                    </div>
                `).join('');
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                notificationList.innerHTML = '<div class="p-3 text-center text-gray-500">Error loading notifications</div>';
            });
    }

    function getAdminNotifikasiIcon(type) {
        const icons = {
            'success': 'check-circle',
            'info': 'info',
            'warning': 'alert-triangle',
            'error': 'x-circle'
        };
        return icons[type] || 'bell';
    }

    function getAdminNotifikasiWarna(type) {
        const colors = {
            'success': 'green-500',
            'info': 'blue-500',
            'warning': 'yellow-500',
            'error': 'red-500'
        };
        return colors[type] || 'gray-500';
    }

    function markAdminNotifikasiAsRead(notificationId) {
        console.log('Marking admin notification as read:', notificationId);
        
        // Get CSRF token safely
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            console.error('CSRF token not found');
            alert('Error: CSRF token tidak ditemukan. Silakan refresh halaman.');
            return;
        }
        
        // Mark notification as read in database
        fetch(`/admin/notifications/${notificationId}/mark-read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Mark as read response:', data);
            if (data.success) {
                // Reload notifications to update UI and badge count
                loadAdminNotifikasis();
            } else {
                console.error('Failed to mark notification as read:', data.message);
                alert('Gagal menandai notifikasi sebagai terbaca: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
            alert('Error: ' + error.message);
        });
    }

    function markAllAdminNotifikasisAsRead() {
        console.log('Marking all admin notifications as read');
        
        // Get CSRF token safely
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            console.error('CSRF token not found');
            alert('Error: CSRF token tidak ditemukan. Silakan refresh halaman.');
            return;
        }
        
        // Mark all notifications as read in database
        fetch('/admin/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Mark all as read response:', data);
            if (data.success) {
                // Reload notifications to update UI and badge count
                loadAdminNotifikasis();
                if (data.updated_count !== undefined) {
                    console.log('Updated ' + data.updated_count + ' notifications');
                }
            } else {
                console.error('Failed to mark all notifications as read:', data.message);
                alert('Gagal menandai semua notifikasi sebagai terbaca: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error marking all notifications as read:', error);
            alert('Error: ' + error.message);
        });
    }

    function clearAllAdminNotifikasis() {
        if (confirm('Apakah Anda yakin ingin menghapus semua notifikasi? Tindakan ini tidak dapat dibatalkan.')) {
            console.log('Clearing all admin notifications');
            
            fetch('/admin/notifications/clear', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear session storage
                    sessionStorage.removeItem('adminReadNotifikasis');
                    
                    // Reload notifications to update UI
                    loadAdminNotifikasis();
                    
                    // Show success message
                    alert(data.message);
                } else {
                    alert(data.message || 'Failed to clear notifications');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error clearing notifications');
            });
        }
    }

    // Close admin notification dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('adminNotifikasiDropdown');
        const bell = document.querySelector('[onclick="toggleAdminNotifikasis()"]');
        
        if (!dropdown.contains(e.target) && !bell.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // Load admin notifications on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadAdminNotifikasis();
    });

    // Export functions
    function exportBookingData() {
        const startDate = new Date();
        startDate.setHours(startDate.getHours() - 24);
        const endDate = new Date();
        
        const startDateStr = startDate.toISOString().split('T')[0];
        const endDateStr = endDate.toISOString().split('T')[0];
        
        window.open(`/admin/export/bookings/excel?start_date=${startDateStr}&end_date=${endDateStr}`, '_blank');
    }

    function exportBookingPDF() {
        const startDate = new Date();
        startDate.setHours(startDate.getHours() - 24);
        const endDate = new Date();
        
        const startDateStr = startDate.toISOString().split('T')[0];
        const endDateStr = endDate.toISOString().split('T')[0];
        
        window.open(`/admin/export/bookings/pdf?start_date=${startDateStr}&end_date=${endDateStr}`, '_blank');
    }
</script>
@endpush
