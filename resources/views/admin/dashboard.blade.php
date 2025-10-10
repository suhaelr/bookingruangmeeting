<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Admin - Sistem Pemesanan Ruang Meeting</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="gradient-bg min-h-screen">
    <!-- Desktop Navigation -->
    <nav class="glass-effect shadow-lg desktop-nav">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar-alt text-2xl text-white"></i>
                    </div>
                    <div class="ml-4">
                        <h1 class="text-xl font-bold text-white">Admin Panel</h1>
                        <p class="text-white/80 text-sm">{{ session('user_data.full_name') ?? 'Administrator' }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="hidden md:flex space-x-6">
                        <a href="{{ route('admin.dashboard') }}" class="text-white hover:text-white/80 transition-colors">
                            <i class="fas fa-tachometer-alt mr-1"></i>Beranda
                        </a>
                        <a href="{{ route('admin.users') }}" class="text-white/80 hover:text-white transition-colors">
                            <i class="fas fa-users mr-1"></i>Pengguna
                        </a>
                        <a href="{{ route('admin.rooms') }}" class="text-white/80 hover:text-white transition-colors">
                            <i class="fas fa-door-open mr-1"></i>Ruang
                        </a>
                        <a href="{{ route('admin.bookings') }}" class="text-white/80 hover:text-white transition-colors">
                            <i class="fas fa-calendar-check mr-1"></i>Pemesanan
                        </a>
                    </div>
                    <div class="flex items-center space-x-2">
                        <!-- Admin Notifikasi Bell -->
                        <div class="relative">
                            <button onclick="toggleAdminNotifikasis()" class="text-white/80 hover:text-white transition-colors p-2">
                                <i class="fas fa-bell text-lg"></i>
                                <span id="admin-notification-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                            </button>
                        </div>
                        
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

    <!-- Mobile Sidebar -->
    @include('components.mobile-sidebar', [
        'userRole' => 'admin',
        'userName' => session('user_data.full_name'),
        'userEmail' => session('user_data.email'),
        'pageTitle' => 'Panel Admin'
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
                        <p class="text-white/80 text-sm">Total Users</p>
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
                        <p class="text-white/80 text-sm">Meeting Rooms</p>
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
                        <p class="text-white/80 text-sm">Total Bookings</p>
                        <p class="text-2xl font-bold text-white">{{ $stats['total_bookings'] }}</p>
                    </div>
                </div>
            </div>

            <div class="glass-effect rounded-2xl p-6 shadow-2xl">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-500 rounded-lg">
                        <i class="fas fa-dollar-sign text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-white/80 text-sm">Revenue This Month</p>
                        <p class="text-2xl font-bold text-white">Rp {{ number_format($stats['revenue_this_month'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Monthly Stats Chart -->
            <div class="glass-effect rounded-2xl p-6 shadow-2xl">
                <h3 class="text-xl font-bold text-white mb-4">Booking Trends (6 Months)</h3>
                <canvas id="monthlyChart" width="400" height="200"></canvas>
            </div>

            <!-- Booking Status Chart -->
            <div class="glass-effect rounded-2xl p-6 shadow-2xl">
                <h3 class="text-xl font-bold text-white mb-4">Booking Status</h3>
                <canvas id="statusChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Recent Bookings & Today's Bookings -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Bookings -->
            <div class="glass-effect rounded-2xl p-6 shadow-2xl">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-white">Recent Bookings</h3>
                    <a href="{{ route('admin.bookings') }}" class="text-blue-400 hover:text-blue-300 text-sm">
                        View All <i class="fas fa-arrow-right ml-1"></i>
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
                            <p class="text-white/60 text-sm mt-1">{{ $booking->created_at->format('M d, H:i') }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-white/60 text-center py-4">No recent bookings</p>
                    @endforelse
                </div>
            </div>

            <!-- Today's Bookings -->
            <div class="glass-effect rounded-2xl p-6 shadow-2xl">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-white">Today's Bookings</h3>
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
                            <p class="text-white font-medium">{{ $booking->start_time->format('H:i') }} - {{ $booking->end_time->format('H:i') }}</p>
                            <p class="text-white/60 text-sm">{{ $booking->user->full_name }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-white/60 text-center py-4">No bookings today</p>
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
        // Monthly Chart
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_column($monthlyStats, 'month')) !!},
                datasets: [{
                    label: 'Bookings',
                    data: {!! json_encode(array_column($monthlyStats, 'bookings')) !!},
                    borderWarna: 'rgb(59, 130, 246)',
                    backgroundWarna: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: {
                            color: 'white'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: 'white'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: 'white'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }
                }
            }
        });

        // Status Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Confirmed', 'Cancelled'],
                datasets: [{
                    data: [
                        {{ $stats['pending_bookings'] }},
                        {{ $stats['confirmed_bookings'] }},
                        {{ $stats['cancelled_bookings'] }}
                    ],
                    backgroundWarna: [
                        'rgb(245, 158, 11)',
                        'rgb(34, 197, 94)',
                        'rgb(239, 68, 68)'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: {
                            color: 'white'
                        }
                    }
                }
            }
        });

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
                    // Check session storage for read status
                    const readNotifikasis = JSON.parse(sessionStorage.getItem('adminReadNotifikasis') || '[]');
                    notifications = notifications.map(notification => ({
                        ...notification,
                        read: readNotifikasis.includes(notification.id) || notification.read
                    }));

                    const unreadCount = notifications.filter(n => !n.read).length;
                    const badge = document.getElementById('admin-notification-badge');
                    
                    if (unreadCount > 0) {
                        badge.textContent = unreadCount;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
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
            
            // Add to read notifications in session storage
            const readNotifikasis = JSON.parse(sessionStorage.getItem('adminReadNotifikasis') || '[]');
            if (!readNotifikasis.includes(notificationId)) {
                readNotifikasis.push(notificationId);
                sessionStorage.setItem('adminReadNotifikasis', JSON.stringify(readNotifikasis));
            }
            
            // Reload notifications to update UI
            loadAdminNotifikasis();
        }

        function markAllAdminNotifikasisAsRead() {
            console.log('Marking all admin notifications as read');
            
            // Mark all notifications as read in session storage
            const allNotifikasiIds = [1, 2, 3, 4]; // All admin notification IDs
            sessionStorage.setItem('adminReadNotifikasis', JSON.stringify(allNotifikasiIds));
            
            // Reload notifications to update UI
            loadAdminNotifikasis();
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
                successMessage.style.transition = 'opacity 0.5s';
                successMessage.style.opacity = '0';
                setTimeout(() => successMessage.remove(), 500);
            }
        }, 3000);
    </script>
</body>
</html>
