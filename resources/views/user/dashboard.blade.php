<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Meta Tags -->
    @include('components.seo-meta', [
        'page' => 'user_dashboard',
        'title' => 'Dashboard Pengguna - Sistem Pemesanan Ruang Meeting',
        'description' => 'Dashboard pengguna untuk mengelola pemesanan ruang meeting. Lihat jadwal, statistik, dan kelola booking Anda.',
        'keywords' => 'dashboard pengguna, pemesanan ruang meeting, jadwal meeting, statistik booking',
        'canonical' => '/user/dashboard',
        'robots' => 'noindex, nofollow'
    ])
    
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

        <!-- Room Availability Grid -->
        <div class="mt-8">
            <div class="glass-effect rounded-2xl p-6 shadow-2xl">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-white">Ketersediaan Ruang Meeting</h3>
                        <p class="text-white/60 text-sm mt-1">{{ now()->format('l, d F Y') }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-green-500 rounded"></div>
                            <span class="text-white/80 text-sm">Tersedia</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-red-500 rounded"></div>
                            <span class="text-white/80 text-sm">Dibooking</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-orange-500 rounded"></div>
                            <span class="text-white/80 text-sm">Pernah Digunakan</span>
                        </div>
                    </div>
                </div>
                
                <!-- Grid Container -->
                <div class="overflow-x-auto">
                    <div class="min-w-full">
                        <!-- Time Header -->
                        <div class="flex mb-2">
                            <div class="w-48 flex-shrink-0"></div>
                            <div class="flex space-x-1">
                                @for($hour = 8; $hour <= 18; $hour++)
                                    @for($minute = 0; $minute < 60; $minute += 30)
                                        <div class="w-12 text-center text-white/60 text-xs py-2">
                                            {{ sprintf('%02d:%02d', $hour, $minute) }}
                                        </div>
                                    @endfor
                                @endfor
                            </div>
                        </div>
                        
                        <!-- Room Rows -->
                        @foreach($roomAvailabilityGrid as $room)
                        <div class="flex mb-1">
                            <!-- Room Info -->
                            <div class="w-48 flex-shrink-0 bg-white/5 rounded-l-lg p-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-white font-medium text-sm">{{ $room['name'] }}</h4>
                                        <p class="text-white/60 text-xs">{{ $room['location'] }}</p>
                                    </div>
                                    <span class="text-white/60 text-xs">{{ $room['capacity'] }} kursi</span>
                                </div>
                            </div>
                            
                            <!-- Time Slots -->
                            <div class="flex space-x-1 flex-1">
                                @foreach($room['timeSlots'] as $slot)
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center text-xs font-medium transition-all duration-200 hover:scale-105 cursor-pointer
                                    @if($slot['isAvailable'] && !$slot['wasUsed'])
                                        bg-green-500/20 border-2 border-green-500/30 text-green-300 hover:bg-green-500/30
                                    @elseif($slot['isAvailable'] && $slot['wasUsed'])
                                        bg-orange-500/20 border-2 border-orange-500/30 text-orange-300 hover:bg-orange-500/30
                                    @else
                                        bg-red-500/20 border-2 border-red-500/30 text-red-300 hover:bg-red-500/30
                                    @endif"
                                    data-slot-info='@json(
                                        "room_id" => $room["id"],
                                        "room_name" => $room["name"],
                                        "room_location" => $room["location"],
                                        "room_capacity" => $room["capacity"],
                                        "time" => $slot["time"],
                                        "datetime" => $slot["datetime"],
                                        "isAvailable" => $slot["isAvailable"],
                                        "wasUsed" => $slot["wasUsed"],
                                        "booking" => $slot["booking"],
                                        "previousBooking" => $slot["previousBooking"]
                                    )'
                                    onmousedown="startHoldTimer(this)"
                                    onmouseup="clearHoldTimer()"
                                    onmouseleave="clearHoldTimer()"
                                    onclick="handleSlotClick(this)">
                                    @if($slot['isAvailable'] && !$slot['wasUsed'])
                                        <i class="fas fa-check text-green-300"></i>
                                    @elseif($slot['isAvailable'] && $slot['wasUsed'])
                                        <i class="fas fa-history text-orange-300"></i>
                                    @else
                                        <i class="fas fa-times text-red-300"></i>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Legend -->
                <div class="mt-6 p-4 bg-white/5 rounded-lg">
                    <h4 class="text-white font-medium mb-3">Keterangan:</h4>
                    <div class="space-y-3">
                        <!-- First row: Green and Red -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div class="flex items-center space-x-2">
                                <div class="w-4 h-4 bg-green-500/20 border border-green-500/30 rounded"></div>
                                <span class="text-white/80">Ruang tersedia untuk dipesan</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-4 h-4 bg-red-500/20 border border-red-500/30 rounded"></div>
                                <span class="text-white/80">Ruang sudah dibooking</span>
                            </div>
                        </div>
                        <!-- Second row: Orange (centered) -->
                        <div class="flex justify-center">
                            <div class="flex items-center space-x-2 text-sm">
                                <div class="w-4 h-4 bg-orange-500/20 border border-orange-500/30 rounded"></div>
                                <span class="text-white/80">Ruang pernah digunakan (tersedia)</span>
                            </div>
                        </div>
                    </div>
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

        // Grid functionality
        function openBookingModal(roomId, startTime, roomName) {
            // Redirect to booking creation with pre-filled data
            const url = new URL('{{ route("user.bookings.create") }}', window.location.origin);
            url.searchParams.set('room_id', roomId);
            url.searchParams.set('start_time', startTime);
            window.location.href = url.toString();
        }

        function showBookingDetails(bookingData) {
            const booking = JSON.parse(bookingData);
            
            // Create modal content
            const modalContent = `
                <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" onclick="closeBookingModal()">
                    <div class="bg-white rounded-2xl max-w-md w-full p-6" onclick="event.stopPropagation()">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold text-gray-800">Detail Booking</h3>
                            <button onclick="closeBookingModal()" class="text-gray-500 hover:text-gray-700">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Judul Meeting</label>
                                <p class="text-gray-800 font-medium">${booking.title}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-600">Dibooking Oleh</label>
                                <p class="text-gray-800">${booking.user_name}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-600">Unit Kerja</label>
                                <p class="text-gray-800">${booking.unit_kerja}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-600">Waktu</label>
                                <p class="text-gray-800">${booking.start_time} - ${booking.end_time}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-600">Status</label>
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    ${booking.status === 'pending' ? 'bg-yellow-500 text-white' : 
                                      booking.status === 'confirmed' ? 'bg-green-500 text-white' : 
                                      'bg-gray-500 text-white'}">
                                    ${booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}
                                </span>
                            </div>
                        </div>
                        
                        <div class="flex justify-end mt-6">
                            <button onclick="closeBookingModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            // Add modal to body
            document.body.insertAdjacentHTML('beforeend', modalContent);
        }

        function closeBookingModal() {
            const modal = document.querySelector('.fixed.inset-0.bg-black.bg-opacity-50');
            if (modal) {
                modal.remove();
            }
        }

        function showPreviousBookingDetails(bookingData) {
            const booking = JSON.parse(bookingData);
            
            // Create modal content
            const modalContent = `
                <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" onclick="closeBookingModal()">
                    <div class="bg-white rounded-2xl max-w-md w-full p-6" onclick="event.stopPropagation()">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold text-gray-800">Riwayat Penggunaan Ruang</h3>
                            <button onclick="closeBookingModal()" class="text-gray-500 hover:text-gray-700">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="bg-orange-50 border border-orange-200 rounded-lg p-3">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-history text-orange-500"></i>
                                    <span class="text-orange-800 font-medium">Ruang pernah digunakan</span>
                                </div>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-600">Judul Meeting</label>
                                <p class="text-gray-800 font-medium">${booking.title}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-600">Digunakan Oleh</label>
                                <p class="text-gray-800">${booking.user_name}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-600">Unit Kerja</label>
                                <p class="text-gray-800">${booking.unit_kerja}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-600">Waktu Penggunaan</label>
                                <p class="text-gray-800">${booking.start_time} - ${booking.end_time}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-600">Status</label>
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-500 text-white">
                                    Selesai
                                </span>
                            </div>
                            
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-info-circle text-blue-500"></i>
                                    <span class="text-blue-800 text-sm">Ruang ini sekarang tersedia untuk dipesan</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-end mt-6">
                            <button onclick="closeBookingModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            // Add modal to body
            document.body.insertAdjacentHTML('beforeend', modalContent);
        }

        // Auto-refresh grid every 5 minutes
        setInterval(function() {
            // In a real app, you might want to refresh the grid data
            console.log('Grid auto-refresh - checking for updates...');
        }, 300000); // 5 minutes

        // Click and hold functionality
        let holdTimer = null;
        let isHolding = false;

        function startHoldTimer(element) {
            isHolding = false;
            holdTimer = setTimeout(() => {
                isHolding = true;
                showSlotDetails(element);
            }, 500); // 500ms hold time
        }

        function clearHoldTimer() {
            if (holdTimer) {
                clearTimeout(holdTimer);
                holdTimer = null;
            }
        }

        function handleSlotClick(element) {
            if (!isHolding) {
                // Quick click - normal behavior
                const slotInfo = JSON.parse(element.getAttribute('data-slot-info'));
                
                if (slotInfo.isAvailable) {
                    openBookingModal(slotInfo.room_id, slotInfo.datetime, slotInfo.room_name);
                } else if (slotInfo.booking) {
                    showBookingDetails(JSON.stringify(slotInfo.booking));
                } else if (slotInfo.previousBooking) {
                    showPreviousBookingDetails(JSON.stringify(slotInfo.previousBooking));
                }
            }
            isHolding = false;
        }

        function showSlotDetails(element) {
            const slotInfo = JSON.parse(element.getAttribute('data-slot-info'));
            
            let modalContent = `
                <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" onclick="closeSlotDetails()">
                    <div class="bg-white rounded-2xl max-w-lg w-full p-6" onclick="event.stopPropagation()">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold text-gray-800">Detail Slot Waktu</h3>
                            <button onclick="closeSlotDetails()" class="text-gray-500 hover:text-gray-700">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <!-- Room Info -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h4 class="font-medium text-blue-800 mb-2">Informasi Ruang</h4>
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <div>
                                        <span class="text-blue-600 font-medium">Nama:</span>
                                        <span class="text-blue-800">${slotInfo.room_name}</span>
                                    </div>
                                    <div>
                                        <span class="text-blue-600 font-medium">Lokasi:</span>
                                        <span class="text-blue-800">${slotInfo.room_location}</span>
                                    </div>
                                    <div>
                                        <span class="text-blue-600 font-medium">Kapasitas:</span>
                                        <span class="text-blue-800">${slotInfo.room_capacity} kursi</span>
                                    </div>
                                    <div>
                                        <span class="text-blue-600 font-medium">Waktu:</span>
                                        <span class="text-blue-800">${slotInfo.time}</span>
                                    </div>
                                </div>
                            </div>
            `;

            // Status specific content
            if (slotInfo.isAvailable && !slotInfo.wasUsed) {
                modalContent += `
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span class="font-medium text-green-800">Status: Tersedia</span>
                        </div>
                        <p class="text-green-700 text-sm">Ruang ini tersedia untuk dipesan pada jam ${slotInfo.time}</p>
                    </div>
                `;
            } else if (slotInfo.isAvailable && slotInfo.wasUsed) {
                modalContent += `
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fas fa-history text-orange-500"></i>
                            <span class="font-medium text-orange-800">Status: Pernah Digunakan</span>
                        </div>
                        <p class="text-orange-700 text-sm">Ruang ini pernah digunakan dan sekarang tersedia untuk dipesan</p>
                    </div>
                `;
            } else if (slotInfo.booking) {
                modalContent += `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fas fa-times-circle text-red-500"></i>
                            <span class="font-medium text-red-800">Status: Sedang Dibooking</span>
                        </div>
                        <div class="mt-2 space-y-1 text-sm">
                            <div><span class="text-red-600 font-medium">Judul:</span> <span class="text-red-800">${slotInfo.booking.title}</span></div>
                            <div><span class="text-red-600 font-medium">Oleh:</span> <span class="text-red-800">${slotInfo.booking.user_name}</span></div>
                            <div><span class="text-red-600 font-medium">Unit:</span> <span class="text-red-800">${slotInfo.booking.unit_kerja}</span></div>
                            <div><span class="text-red-600 font-medium">Waktu:</span> <span class="text-red-800">${slotInfo.booking.start_time} - ${slotInfo.booking.end_time}</span></div>
                        </div>
                    </div>
                `;
            }

            // Previous booking info if available
            if (slotInfo.previousBooking) {
                modalContent += `
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <h4 class="font-medium text-gray-800 mb-2">Riwayat Penggunaan</h4>
                        <div class="space-y-1 text-sm">
                            <div><span class="text-gray-600 font-medium">Judul:</span> <span class="text-gray-800">${slotInfo.previousBooking.title}</span></div>
                            <div><span class="text-gray-600 font-medium">Oleh:</span> <span class="text-gray-800">${slotInfo.previousBooking.user_name}</span></div>
                            <div><span class="text-gray-600 font-medium">Unit:</span> <span class="text-gray-800">${slotInfo.previousBooking.unit_kerja}</span></div>
                            <div><span class="text-gray-600 font-medium">Waktu:</span> <span class="text-gray-800">${slotInfo.previousBooking.start_time} - ${slotInfo.previousBooking.end_time}</span></div>
                        </div>
                    </div>
                `;
            }

            modalContent += `
                        </div>
                        
                        <div class="flex justify-end mt-6">
                            <button onclick="closeSlotDetails()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            // Add modal to body
            document.body.insertAdjacentHTML('beforeend', modalContent);
        }

        function closeSlotDetails() {
            const modal = document.querySelector('.fixed.inset-0.bg-black.bg-opacity-50');
            if (modal) {
                modal.remove();
            }
        }
    </script>
</body>
</html>
