<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- SEO Meta Tags -->
    @include('components.seo-meta', [
        'page' => 'admin_dashboard',
        'title' => 'Dashboard Admin - Sistem Pemesanan Ruang Meeting',
        'description' => 'Dashboard admin untuk mengelola sistem pemesanan ruang meeting. Kelola pengguna, ruang meeting, dan pemesanan.',
        'keywords' => 'dashboard admin, manajemen sistem, pengguna, ruang meeting, pemesanan',
        'canonical' => '/admin/dashboard',
        'robots' => 'noindex, nofollow'
    ])
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                    <!-- Admin Notification Bell -->
                    <div class="relative">
                        <button onclick="toggleAdminNotifikasis()" class="relative p-2 text-white hover:text-blue-300 transition-colors duration-300">
                            <i class="fas fa-bell text-xl"></i>
                            <span id="admin-notification-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                        </button>
                    </div>
                    <a href="{{ route('logout') }}" 
                       class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors duration-300 flex items-center">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Keluar
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Sidebar -->
    @include('components.mobile-sidebar', [
        'userRole' => 'admin',
        'userName' => session('user_data.full_name'),
        'userEmail' => session('user_data.email'),
        'pageTitle' => 'Beranda Admin'
    ])

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="glass-effect rounded-2xl p-6 shadow-2xl">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-500 rounded-lg">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-white/80 text-sm">Total Pengguna</p>
                        <p class="text-2xl font-bold text-white">{{ $stats['total_users'] }}</p>
                    </div>
                </div>
            </div>

            <div class="glass-effect rounded-2xl p-6 shadow-2xl">
                <div class="flex items-center">
                    <div class="p-3 bg-green-500 rounded-lg">
                        <i class="fas fa-door-open text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-white/80 text-sm">Ruang Meeting</p>
                        <p class="text-2xl font-bold text-white">{{ $stats['total_rooms'] }}</p>
                    </div>
                </div>
            </div>

            <div class="glass-effect rounded-2xl p-6 shadow-2xl">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-500 rounded-lg">
                        <i class="fas fa-calendar-check text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-white/80 text-sm">Total Pemesanan</p>
                        <p class="text-2xl font-bold text-white">{{ $stats['total_bookings'] }}</p>
                    </div>
                </div>
            </div>

            <div class="glass-effect rounded-2xl p-6 shadow-2xl">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-500 rounded-lg">
                        <i class="fas fa-check-circle text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-white/80 text-sm">Pemesanan Dikonfirmasi</p>
                        <p class="text-2xl font-bold text-white">{{ $stats['confirmed_bookings'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export Section -->
        <div class="glass-effect rounded-2xl p-6 shadow-2xl mb-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h3 class="text-xl font-bold text-white mb-2">Export Data</h3>
                    <p class="text-white/70 text-sm">Export data riwayat booking dan status ketersediaan ruangan (24 jam terakhir)</p>
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
                    <h3 class="text-xl font-bold text-white">Pemesanan Terbaru</h3>
                    <a href="{{ route('admin.bookings') }}" class="text-blue-400 hover:text-blue-300 text-sm">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="space-y-4">
                    @forelse($recentBookings as $booking)
                    <div class="flex items-center justify-between p-4 bg-white/10 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-calendar text-white"></i>
                            </div>
                            <div>
                                <p class="text-white font-medium">{{ $booking->title }}</p>
                                <p class="text-white/60 text-sm">{{ $booking->user->full_name }} • {{ $booking->meetingRoom->name }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                @if($booking->status === 'pending') bg-yellow-500 text-white
                                @elseif($booking->status === 'confirmed') bg-green-500 text-white
                                @elseif($booking->status === 'cancelled') bg-red-500 text-white
                                @else bg-gray-500 text-white @endif">
                                {{ ucfirst($booking->status) }}
                            </span>
                            <p class="text-white/60 text-sm mt-1">{{ $booking->created_at ? $booking->created_at->format('M d, H:i') : 'Tidak tersedia' }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-white/60 text-center py-4">Tidak ada pemesanan terbaru</p>
                    @endforelse
                </div>
            </div>

            <!-- Today's Bookings -->
            <div class="glass-effect rounded-2xl p-6 shadow-2xl">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-white">Pemesanan Hari Ini</h3>
                    <span class="text-white/60 text-sm">{{ now()->format('M d, Y') }}</span>
                </div>
                <div class="space-y-4">
                    @forelse($todayBookings as $booking)
                    <div class="flex items-center justify-between p-4 bg-white/10 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-clock text-white"></i>
                            </div>
                            <div>
                                <p class="text-white font-medium">{{ $booking->title }}</p>
                                <p class="text-white/60 text-sm">{{ $booking->meetingRoom->name }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-white font-medium">{{ $booking->start_time ? $booking->start_time->format('H:i') : 'Tidak tersedia' }} - {{ $booking->end_time ? $booking->end_time->format('H:i') : 'Tidak tersedia' }}</p>
                            <p class="text-white/60 text-sm">{{ $booking->user->full_name }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-white/60 text-center py-4">Tidak ada pemesanan hari ini</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div id="success-message" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        </div>
    @endif

    <!-- Admin Notifikasi Dropdown -->
    <div id="adminNotifikasiDropdown" class="fixed top-16 right-4 bg-white rounded-lg shadow-lg border hidden z-50 w-80 max-h-96 overflow-y-auto">
        <div class="p-4 border-b">
            <h3 class="font-semibold text-gray-800">Admin Notifikasis</h3>
        </div>
        <div id="adminNotifikasiList" class="p-2">
            <!-- Admin notifications will be loaded here -->
        </div>
        <div class="p-2 border-t flex space-x-2">
            <button onclick="markAllAdminNotifikasisAsRead()" class="flex-1 text-center text-blue-500 hover:text-blue-700 text-sm py-2">
                Mark all as read
            </button>
            <button onclick="clearAllAdminNotifikasis()" class="flex-1 text-center text-red-500 hover:text-red-700 text-sm py-2">
                Clear all
            </button>
        </div>
    </div>

    <!-- WhatsApp Floating Button -->
    @include('components.whatsapp-float')

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
                .then(response => response.json())
                .then(notifications => {
                    const unreadCount = notifications.filter(n => !n.read).length;
                    const badge = document.getElementById('admin-notification-badge');
                    
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
                                    <p class="text-sm text-gray-600">${notification.message}</p>
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
            
            // Mark notification as read in database
            fetch(`/admin/notifications/${notificationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload notifications to update UI
                    loadAdminNotifikasis();
                } else {
                    console.error('Failed to mark notification as read:', data.message);
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
            });
        }

        function markAllAdminNotifikasisAsRead() {
            console.log('Marking all admin notifications as read');
            
            // Mark all notifications as read in database
            fetch('/admin/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload notifications to update UI
                    loadAdminNotifikasis();
                } else {
                    console.error('Failed to mark all notifications as read:', data.message);
                }
            })
            .catch(error => {
                console.error('Error marking all notifications as read:', error);
            });
        }

        function clearAllAdminNotifikasis() {
            if (confirm('Are you sure you want to clear all notifications? This action cannot be undone.')) {
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
            
            // Auto-refresh notifications every 30 seconds
            setInterval(loadAdminNotifikasis, 30000);
        });

        // Auto-hide success message
        setTimeout(() => {
            const successMessage = document.getElementById('success-message');
            if (successMessage) {
                successMessage.style.transition = 'opacity 0.3s';
                successMessage.style.opacity = '0';
                setTimeout(() => successMessage.remove(), 300);
            }
        }, 1000);

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
</body>
</html>
