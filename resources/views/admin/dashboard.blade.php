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
            <i class="fas fa-bell text-xl"></i>
            <span id="admin-notification-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
        </button>
    </div>
@endpush

@section('main-content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="glass-effect rounded-2xl p-6 shadow-2xl">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="fas fa-users text-black text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-black text-sm">Total Pengguna</p>
                    <p class="text-2xl font-bold text-black">{{ $stats['total_users'] }}</p>
                </div>
            </div>
        </div>

        <div class="glass-effect rounded-2xl p-6 shadow-2xl">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <i class="fas fa-door-open text-black text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-black text-sm">Ruang Meeting</p>
                    <p class="text-2xl font-bold text-black">{{ $stats['total_rooms'] }}</p>
                </div>
            </div>
        </div>

        <div class="glass-effect rounded-2xl p-6 shadow-2xl">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <i class="fas fa-calendar-check text-black text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-black text-sm">Total Pemesanan</p>
                    <p class="text-2xl font-bold text-black">{{ $stats['total_bookings'] }}</p>
                </div>
            </div>
        </div>

        <div class="glass-effect rounded-2xl p-6 shadow-2xl">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <i class="fas fa-check-circle text-black text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-black text-sm">Pemesanan Dikonfirmasi</p>
                    <p class="text-2xl font-bold text-black">{{ $stats['confirmed_bookings'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Section -->
    <div class="glass-effect rounded-2xl p-6 shadow-2xl mb-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-xl font-bold text-black mb-2">Export Data</h3>
                <p class="text-black text-sm">Export data riwayat booking dan status ketersediaan ruangan (24 jam terakhir)</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <button onclick="exportBookingData()" 
                        class="px-6 py-3 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors duration-300 flex items-center">
                    <i class="fas fa-file-excel mr-2"></i>
                    Export Excel
                </button>
                <button onclick="exportBookingPDF()" 
                        class="px-6 py-3 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors duration-300 flex items-center">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Export PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Recent Bookings & Today's Bookings -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Bookings -->
        <div class="glass-effect rounded-2xl p-6 shadow-2xl">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-black">Pemesanan Terbaru</h3>
                <a href="{{ route('admin.bookings') }}" class="text-black hover:text-black text-sm">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="space-y-4">
                @forelse($recentBookings as $booking)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-calendar text-black"></i>
                        </div>
                        <div>
                            <p class="text-black font-medium">{{ $booking->title }}</p>
                            <p class="text-black text-sm">{{ $booking->user->full_name }} • {{ $booking->meetingRoom->name }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            @if($booking->status === 'pending') bg-yellow-100 text-black
                            @elseif($booking->status === 'confirmed') bg-green-100 text-black
                            @elseif($booking->status === 'cancelled') bg-red-100 text-black
                            @else bg-gray-200 text-black @endif">
                            {{ ucfirst($booking->status) }}
                        </span>
                        <p class="text-black text-sm mt-1">{{ $booking->created_at ? $booking->created_at->format('M d, H:i') : 'Tidak tersedia' }}</p>
                    </div>
                </div>
                @empty
                <p class="text-black text-center py-4">Tidak ada pemesanan terbaru</p>
                @endforelse
            </div>
        </div>

        <!-- Today's Bookings -->
        <div class="glass-effect rounded-2xl p-6 shadow-2xl">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-black">Pemesanan Hari Ini</h3>
                <span class="text-black text-sm">{{ now()->format('M d, Y') }}</span>
            </div>
            <div class="space-y-4">
                @forelse($todayBookings as $booking)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-black"></i>
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
                <p class="text-black text-center py-4">Tidak ada pemesanan hari ini</p>
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
                                <i class="fas fa-${getAdminNotifikasiIcon(notification.type)} text-${getAdminNotifikasiWarna(notification.type)}"></i>
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
            'info': 'info-circle',
            'warning': 'exclamation-triangle',
            'error': 'times-circle'
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
