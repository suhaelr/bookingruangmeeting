<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengguna - Sistem Pemesanan Ruang Meeting</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Mobile navbar text size adjustments */
        @media (max-width: 768px) {
            .navbar-title {
                font-size: 1rem !important; /* text-lg -> text-base */
            }
            .navbar-subtitle {
                font-size: 0.75rem !important; /* text-sm -> text-xs */
            }
            .navbar-menu {
                font-size: 0.875rem !important; /* text-base -> text-sm */
            }
        }
    </style>
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
                        <h1 class="text-xl font-bold text-white navbar-title">{{ session('user_data.full_name') ?? 'Pengguna' }}</h1>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="hidden md:flex space-x-6">
                        <a href="{{ route('user.dashboard') }}" class="text-white hover:text-white/80 transition-colors navbar-menu">
                            <i class="fas fa-tachometer-alt mr-1"></i>Beranda
                        </a>
                        <a href="{{ route('user.bookings') }}" class="text-white/80 hover:text-white transition-colors navbar-menu">
                            <i class="fas fa-calendar-check mr-1"></i>Pemesanan Saya
                        </a>
                        <a href="{{ route('user.bookings.create') }}" class="text-white/80 hover:text-white transition-colors navbar-menu">
                            <i class="fas fa-plus mr-1"></i>Pemesanan Baru
                        </a>
                        <a href="{{ route('user.profile') }}" class="text-white/80 hover:text-white transition-colors navbar-menu">
                            <i class="fas fa-user mr-1"></i>Profil
                        </a>
                    </div>
                    <div class="flex items-center space-x-2">
                        <!-- Notifikasi Bell -->
                        <div class="relative">
                            <button onclick="toggleNotifikasis()" class="text-white/80 hover:text-white transition-colors p-2">
                                <i class="fas fa-bell text-lg"></i>
                                <span id="notification-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">3</span>
                            </button>
                        </div>
                        
                        <span class="text-white/80 text-sm">
                            <i class="fas fa-user mr-1"></i>
                            {{ session('user_data.full_name') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Sidebar -->
    @include('components.mobile-sidebar', [
        'userRole' => 'user',
        'userName' => session('user_data.full_name'),
        'userEmail' => session('user_data.email'),
        'pageTitle' => 'Dashboard Pengguna'
    ])

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Selamat Datang Section -->
        <div class="glass-effect rounded-2xl p-6 mb-8 shadow-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-white mb-2">Selamat Datang, {{ session('user_data.full_name') }}!</h2>
                    <p class="text-white/80">Kelola pemesanan ruang meeting Anda dan tetap terorganisir</p>
                </div>
                <div class="hidden md:block">
                    <a href="{{ route('user.bookings.create') }}" 
                       class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors duration-300 flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        Pesan Ruang Meeting
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="glass-effect rounded-2xl p-6 shadow-2xl">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-500 rounded-lg">
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
                    <div class="p-3 bg-yellow-500 rounded-lg">
                        <i class="fas fa-clock text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-white/80 text-sm">Menunggu</p>
                        <p class="text-2xl font-bold text-white">{{ $stats['pending_bookings'] }}</p>
                    </div>
                </div>
            </div>

            <div class="glass-effect rounded-2xl p-6 shadow-2xl">
                <div class="flex items-center">
                    <div class="p-3 bg-green-500 rounded-lg">
                        <i class="fas fa-check-circle text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-white/80 text-sm">Dikonfirmasi</p>
                        <p class="text-2xl font-bold text-white">{{ $stats['confirmed_bookings'] }}</p>
                    </div>
                </div>
            </div>

            <div class="glass-effect rounded-2xl p-6 shadow-2xl">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-500 rounded-lg">
                        <i class="fas fa-calendar text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-white/80 text-sm">Bulan Ini</p>
                        <p class="text-2xl font-bold text-white">{{ $stats['this_month'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Active Bookings -->
            <div class="glass-effect rounded-2xl p-6 shadow-2xl">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-white">Pemesanan Aktif</h3>
                    <a href="{{ route('user.bookings') }}" class="text-blue-400 hover:text-blue-300 text-sm">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="space-y-4">
                    @forelse($activeBookings as $booking)
                    <div class="flex items-center justify-between p-4 bg-white/10 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-calendar text-white"></i>
                            </div>
                            <div>
                                <p class="text-white font-medium">{{ $booking->title }}</p>
                                <p class="text-white/60 text-sm">{{ $booking->meetingRoom->name }}</p>
                                <p class="text-white/60 text-xs">{{ $booking->formatted_start_time }}</p>
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
                            @if($booking->canBeCancelled())
                            <form method="POST" action="{{ route('user.bookings.cancel', $booking->id) }}" class="mt-2">
                                @csrf
                                <button type="submit" class="text-red-400 hover:text-red-300 text-xs">
                                    <i class="fas fa-times mr-1"></i>Batal
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="fas fa-calendar-times text-white/40 text-4xl mb-4"></i>
                        <p class="text-white/60">Tidak ada pemesanan aktif</p>
                        <a href="{{ route('user.bookings.create') }}" 
                           class="text-blue-400 hover:text-blue-300 text-sm mt-2 inline-block">
                            Pesan ruang meeting
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Today's Bookings -->
            <div class="glass-effect rounded-2xl p-6 shadow-2xl">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-white">Jadwal Hari Ini</h3>
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
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                @if($booking->status === 'pending') bg-yellow-500 text-white
                                @elseif($booking->status === 'confirmed') bg-green-500 text-white
                                @elseif($booking->status === 'cancelled') bg-red-500 text-white
                                @else bg-gray-500 text-white @endif">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="fas fa-calendar-day text-white/40 text-4xl mb-4"></i>
                        <p class="text-white/60">Tidak ada meeting hari ini</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Available Rooms -->
        <div class="mt-8">
            <div class="glass-effect rounded-2xl p-6 shadow-2xl">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-white">Ruang Meeting Tersedia</h3>
                    <a href="{{ route('user.bookings.create') }}" class="text-blue-400 hover:text-blue-300 text-sm">
                        Pesan Sekarang <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($availableRooms as $room)
                    <div class="bg-white/10 rounded-lg p-4 hover:bg-white/20 transition-colors">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-white font-medium">{{ $room->name }}</h4>
                            <span class="text-white/60 text-sm">{{ $room->capacity }} kursi</span>
                        </div>
                        <p class="text-white/60 text-sm mb-2">{{ $room->location }}</p>
                        <div class="flex flex-wrap gap-1 mb-3">
                            @foreach($room->getAmenitiesList() as $amenity)
                            <span class="px-2 py-1 bg-blue-500/20 text-blue-300 text-xs rounded">
                                {{ ucfirst($amenity) }}
                            </span>
                            @endforeach
                        </div>
                        <div class="flex items-center justify-between">
                            <a href="{{ route('user.bookings.create', ['room' => $room->id]) }}" 
                               class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition-colors">
                                Pesan
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full text-center py-8">
                        <i class="fas fa-door-open text-white/40 text-4xl mb-4"></i>
                        <p class="text-white/60">Tidak ada ruang meeting tersedia</p>
                    </div>
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

    <!-- Notifikasi Dropdown -->
    <div id="notificationDropdown" class="fixed top-16 right-4 bg-white rounded-lg shadow-lg border hidden z-50 w-80 max-h-96 overflow-y-auto">
        <div class="p-4 border-b">
            <h3 class="font-semibold text-gray-800">Notifikasis</h3>
        </div>
        <div id="notificationList" class="p-2">
            <!-- Notifikasis will be loaded here -->
        </div>
        <div class="p-2 border-t">
            <button onclick="markAllAsRead()" class="w-full text-center text-blue-500 hover:text-blue-700 text-sm py-2">
                Mark all as read
            </button>
        </div>
    </div>

    <!-- WhatsApp Floating Button -->
    @include('components.whatsapp-float')

    <script>
        // Notifikasi functions
        function toggleNotifikasis() {
            const dropdown = document.getElementById('notificationDropdown');
            const badge = document.getElementById('notification-badge');
            
            if (dropdown.classList.contains('hidden')) {
                dropdown.classList.remove('hidden');
                loadNotifikasis();
            } else {
                dropdown.classList.add('hidden');
            }
        }

        function loadNotifikasis() {
            const notificationList = document.getElementById('notificationList');
            
            // Sample notifications - in real app, this would come from backend
            let notifications = [
                {
                    id: 1,
                    title: 'Booking Confirmed',
                    message: 'Your meeting "Team Standup" has been confirmed for tomorrow at 10:00 AM',
                    time: '2 hours ago',
                    read: false,
                    type: 'success'
                },
                {
                    id: 2,
                    title: 'Room Available',
                    message: 'Conference Room A is now available for booking',
                    time: '4 hours ago',
                    read: false,
                    type: 'info'
                },
                {
                    id: 3,
                    title: 'Reminder',
                    message: 'You have a meeting in 30 minutes: "Project Review"',
                    time: '6 hours ago',
                    read: true,
                    type: 'warning'
                }
            ];

            // Check session storage for read status
            const readNotifikasis = JSON.parse(sessionStorage.getItem('readNotifikasis') || '[]');
            notifications = notifications.map(notification => ({
                ...notification,
                read: readNotifikasis.includes(notification.id) || notification.read
            }));

            const unreadCount = notifications.filter(n => !n.read).length;
            const badge = document.getElementById('notification-badge');
            
            if (unreadCount > 0) {
                badge.textContent = unreadCount;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }

            notificationList.innerHTML = notifications.map(notification => `
                <div class="p-3 border-b hover:bg-gray-50 cursor-pointer ${!notification.read ? 'bg-blue-50' : ''}" onclick="markAsRead(${notification.id})">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-${getNotifikasiIcon(notification.type)} text-${getNotifikasiWarna(notification.type)}"></i>
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
        }

        function getNotifikasiIcon(type) {
            const icons = {
                'success': 'check-circle',
                'info': 'info-circle',
                'warning': 'exclamation-triangle',
                'error': 'times-circle'
            };
            return icons[type] || 'bell';
        }

        function getNotifikasiWarna(type) {
            const colors = {
                'success': 'green-500',
                'info': 'blue-500',
                'warning': 'yellow-500',
                'error': 'red-500'
            };
            return colors[type] || 'gray-500';
        }

        function markAsRead(notificationId) {
            // In real app, this would make an API call
            console.log('Marking notification as read:', notificationId);
            
            // Add to read notifications in session storage
            const readNotifikasis = JSON.parse(sessionStorage.getItem('readNotifikasis') || '[]');
            if (!readNotifikasis.includes(notificationId)) {
                readNotifikasis.push(notificationId);
                sessionStorage.setItem('readNotifikasis', JSON.stringify(readNotifikasis));
            }
            
            // Reload notifications to update UI
            loadNotifikasis();
        }

        function markAllAsRead() {
            // In real app, this would make an API call
            console.log('Marking all notifications as read');
            
            // Mark all notifications as read in session storage
            const allNotifikasiIds = [1, 2, 3]; // All notification IDs
            sessionStorage.setItem('readNotifikasis', JSON.stringify(allNotifikasiIds));
            
            // Reload notifications to update UI
            loadNotifikasis();
        }

        // Close notification dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('notificationDropdown');
            const bell = document.querySelector('[onclick="toggleNotifikasis()"]');
            
            if (!dropdown.contains(e.target) && !bell.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
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

        // Load notifications on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadNotifikasis();
        });
    </script>
</body>
</html>
