<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
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
    <!-- PDF.js for cross-browser PDF rendering -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js"></script>
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

        /* Responsive calendar grid */
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 0.75rem; /* 3 */
        }
        @media (max-width: 1024px) {
            .calendar-grid { grid-template-columns: repeat(5, minmax(0, 1fr)); }
        }
        @media (max-width: 640px) {
            .calendar-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }

        .calendar-day {
            min-height: 120px;
            border-radius: 0.75rem; /* xl */
            padding: 0.75rem; /* 3 */
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.1);
        }
        @media (max-width: 640px) {
            .calendar-day { min-height: 96px; padding: 0.5rem; }
            .calendar-day .items { max-height: 3.5rem; }
            .mobile-calendar-day { cursor: pointer; position: relative; }
            .mobile-calendar-day:active { transform: scale(0.98); }
            .mobile-calendar-day .calendar-items-container { pointer-events: auto; }
            .mobile-calendar-day .calendar-item { pointer-events: auto; position: relative; z-index: 10; }
        }
    </style>
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
                    <!-- User Notification Bell -->
                    <div class="relative">
                        <button onclick="toggleNotifikasis()" class="relative p-2 text-white hover:text-blue-300 transition-colors duration-300">
                            <i class="fas fa-bell text-xl"></i>
                            <span id="notification-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
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

        <!-- Kalender Bulanan (Confirmed bookings per date) -->
        <div class="mt-8">
            <div class="glass-effect rounded-2xl p-6 shadow-2xl">
                <div class="mb-6 flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h3 class="text-xl font-bold text-white">Kalender Booking</h3>
                        <p class="text-white/60 text-sm mt-1">{{ $calendarAnchor->translatedFormat('F Y') }}</p>
                    </div>
                    <div class="w-full sm:w-auto">
                        <input type="month" id="calendar-date-picker" value="{{ $calendarAnchor->format('Y-m') }}" class="bg-white/20 text-white text-sm rounded-lg border border-white/30 focus:ring-blue-500 focus:border-blue-500 px-3 py-2 w-full" />
                    </div>
                </div>

                <div class="calendar-grid">
                    @php
                        $firstWeekday = (int) $calendarAnchor->copy()->startOfMonth()->dayOfWeekIso; // 1..7
                        $placeholders = $firstWeekday - 1;
                    @endphp
                    @for($i=0;$i<$placeholders;$i++)
                        <div class="calendar-day"></div>
                    @endfor

                    @foreach($calendarDays as $day)
                        <div class="calendar-day {{ $day['isToday'] ? 'ring-2 ring-blue-400' : '' }} mobile-calendar-day" 
                             data-day="{{ $day['day'] }}" 
                             data-date="{{ $day['date'] ?? '' }}"
                             data-day-data='@json($day)'
                             data-has-items="{{ count($day['items']) > 0 ? 'true' : 'false' }}">
                            <div class="flex items-center justify-between mb-2 calendar-day-header">
                                <span class="text-white font-semibold">{{ $day['day'] }}</span>
                                <span class="text-xs text-white/60">{{ count($day['items']) }} booking</span>
                            </div>
                            <div class="space-y-1 items overflow-y-auto pr-1 calendar-items-container">
                                @forelse($day['items'] as $item)
                                    <div class="text-xs bg-blue-500/20 text-blue-100 rounded px-2 py-1 cursor-pointer hover:bg-blue-500/30 calendar-item"
                                         data-item-data='@json($item)'
                                         onclick='event.stopPropagation(); event.preventDefault(); showCalendarItemDetails(@json($item))'>
                                        <div class="font-medium truncate">{{ $item['title'] }}</div>
                                        <div class="text-[10px] opacity-80 truncate">{{ $item['start_time'] }}-{{ $item['end_time'] }} • {{ $item['room'] }}</div>
                                        <div class="text-[10px] opacity-80 truncate">{{ $item['pic_name'] }} • {{ $item['unit_kerja'] }}</div>
                                    </div>
                                @empty
                                    <div class="text-[11px] text-white/40">Tidak ada</div>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
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
            <h3 class="font-semibold text-gray-800">Notifikasi</h3>
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
            
            // Fetch notifications from backend
            fetch('/user/notifications/api')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(notifications => {
                    console.log('Loaded notifications:', notifications);
                    
                    // Calculate unread count - ensure read is boolean
                    const unreadCount = notifications.filter(n => !n.read).length;
                    const badge = document.getElementById('notification-badge');
                    
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
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                    notificationList.innerHTML = '<div class="p-3 text-center text-gray-500">Error loading notifications: ' + error.message + '</div>';
                });
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
            console.log('Marking notification as read:', notificationId);
            
            // Get CSRF token safely
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('CSRF token not found');
                alert('Error: CSRF token tidak ditemukan. Silakan refresh halaman.');
                return;
            }
            
            // Mark notification as read in database
            fetch(`/user/notifications/${notificationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'Content-Type': 'application/json'
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
                    // Reload notifications to update UI
                    loadNotifikasis();
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

        function markAllAsRead() {
            console.log('Marking all notifications as read');
            
            // Get CSRF token safely
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('CSRF token not found');
                alert('Error: CSRF token tidak ditemukan. Silakan refresh halaman.');
                return;
            }
            
            // Mark all notifications as read in database
            fetch('/user/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'Content-Type': 'application/json'
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
                    // Reload notifications to update UI
                    loadNotifikasis();
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
                successMessage.style.transition = 'opacity 0.3s';
                successMessage.style.opacity = '0';
                setTimeout(() => successMessage.remove(), 300);
            }
        }, 1000);

        // Download PDF function
        function downloadPDF(url) {
            const link = document.createElement('a');
            link.href = url;
            link.download = 'dokumen_booking.pdf';
            link.target = '_blank';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Load PDF viewer with multiple fallback methods
        function loadPDFViewer(pdfUrl, viewerId, isMobile) {
            const iframeEl = document.getElementById(`${viewerId}-iframe`);
            const canvasEl = document.getElementById(`${viewerId}-canvas`);
            const loadingEl = document.getElementById(`${viewerId}-loading`);
            const errorEl = document.getElementById(`${viewerId}-error`);
            
            if (!iframeEl || !canvasEl || !loadingEl) {
                console.error('PDF viewer elements not found');
                return;
            }

            // Detect browser
            const isChrome = /Chrome/.test(navigator.userAgent) && !/Edge/.test(navigator.userAgent);
            const isFirefox = /Firefox/.test(navigator.userAgent);
            const isSafari = /Safari/.test(navigator.userAgent) && !/Chrome/.test(navigator.userAgent) && !/Edge/.test(navigator.userAgent);
            const isEdge = /Edge/.test(navigator.userAgent) || /Edg/.test(navigator.userAgent);

            // Try native iframe first (best for Chrome, Edge, Firefox)
            // Safari and mobile: use PDF.js directly (better compatibility)
            if (isSafari || isMobile) {
                tryPDFJS();
            } else if (isChrome || isFirefox || isEdge) {
                tryNativeIframe();
            } else {
                // Fallback: try native iframe first, then PDF.js
                tryNativeIframe();
            }

            function tryNativeIframe() {
                if (!iframeEl) return;
                
                // Set up iframe load handlers
                iframeEl.onload = function() {
                    loadingEl.style.display = 'none';
                    iframeEl.style.display = 'block';
                    canvasEl.style.display = 'none';
                };
                
                iframeEl.onerror = function() {
                    // If iframe fails, try PDF.js
                    tryPDFJS();
                };
                
                // Set source
                iframeEl.src = pdfUrl;
                
                // Timeout fallback: if iframe doesn't load in 5 seconds, try PDF.js
                setTimeout(() => {
                    if (loadingEl.style.display !== 'none') {
                        tryPDFJS();
                    }
                }, 5000);
            }

            function tryPDFJS() {
                // Check if PDF.js is available
                if (typeof pdfjsLib === 'undefined') {
                    console.error('PDF.js library not loaded');
                    showError();
                    return;
                }

                // Set PDF.js worker
                pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

                // Load PDF
                pdfjsLib.getDocument(pdfUrl).promise
                    .then(function(pdf) {
                        // Render first page
                        pdf.getPage(1).then(function(page) {
                            const viewport = page.getViewport({ scale: 1.5 });
                            const context = canvasEl.getContext('2d');
                            
                            canvasEl.height = viewport.height;
                            canvasEl.width = viewport.width;
                            
                            const renderContext = {
                                canvasContext: context,
                                viewport: viewport
                            };
                            
                            page.render(renderContext).promise.then(function() {
                                loadingEl.style.display = 'none';
                                const canvasContainer = document.getElementById(`${viewerId}-canvas-container`);
                                if (canvasContainer) {
                                    canvasContainer.style.display = 'block';
                                }
                                canvasEl.style.display = 'block';
                                iframeEl.style.display = 'none';
                                
                                // Render remaining pages if needed (for full document)
                                renderAllPages(pdf, canvasEl, viewport);
                            }).catch(function(error) {
                                console.error('Error rendering PDF page:', error);
                                showError();
                            });
                        }).catch(function(error) {
                            console.error('Error getting PDF page:', error);
                            showError();
                        });
                    })
                    .catch(function(error) {
                        console.error('Error loading PDF:', error);
                        showError();
                    });
            }

            function renderAllPages(pdf, canvas, viewport) {
                const numPages = pdf.numPages;
                if (numPages <= 1) return; // Already rendered first page
                
                // Get container (canvas-container)
                const container = canvas.parentElement;
                if (!container) return;
                
                // Wrap first page canvas in a div
                const firstPageDiv = document.createElement('div');
                firstPageDiv.className = 'pdf-page mb-4 border-b border-gray-200 pb-4 text-center';
                firstPageDiv.style.backgroundColor = '#f9fafb';
                canvas.parentElement.removeChild(canvas);
                firstPageDiv.appendChild(canvas);
                container.appendChild(firstPageDiv);
                
                // Render remaining pages
                for (let pageNum = 2; pageNum <= numPages; pageNum++) {
                    pdf.getPage(pageNum).then(function(page) {
                        const pageCanvas = document.createElement('canvas');
                        const pageContext = pageCanvas.getContext('2d');
                        pageCanvas.height = viewport.height;
                        pageCanvas.width = viewport.width;
                        
                        const pageDiv = document.createElement('div');
                        pageDiv.className = 'pdf-page mb-4 border-b border-gray-200 pb-4 text-center';
                        pageDiv.style.backgroundColor = '#f9fafb';
                        pageDiv.appendChild(pageCanvas);
                        container.appendChild(pageDiv);
                        
                        const renderContext = {
                            canvasContext: pageContext,
                            viewport: viewport
                        };
                        
                        page.render(renderContext).promise.then(function() {
                            console.log(`Page ${pageNum} rendered`);
                        }).catch(function(error) {
                            console.error(`Error rendering page ${pageNum}:`, error);
                        });
                    }).catch(function(error) {
                        console.error(`Error getting page ${pageNum}:`, error);
                    });
                }
            }

            function showError() {
                loadingEl.style.display = 'none';
                iframeEl.style.display = 'none';
                const canvasContainerEl = document.getElementById(`${viewerId}-canvas-container`);
                if (canvasContainerEl) {
                    canvasContainerEl.style.display = 'none';
                }
                canvasEl.style.display = 'none';
                if (errorEl) {
                    errorEl.classList.remove('hidden');
                }
            }
        }

        // Load notifications on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadNotifikasis();
            
            // Auto-refresh notifications every 10 seconds for real-time updates
            setInterval(loadNotifikasis, 10000);
            
            const dp = document.getElementById('calendar-date-picker');
            if (dp) {
                dp.addEventListener('change', function() {
                    const month = this.value; // YYYY-MM from <input type="month">
                    if (!month) return;
                    const url = new URL(window.location.href);
                    url.searchParams.set('month', month);
                    url.searchParams.delete('date');
                    window.location.href = url.toString();
                });
            }
            
            // Setup calendar day click handlers for mobile
            setupCalendarDayClickHandlers();
        });
        
        // Setup calendar day click handlers using event delegation
        function setupCalendarDayClickHandlers() {
            // Use event delegation for better reliability
            document.addEventListener('click', function(e) {
                // Only handle on mobile
                if (window.innerWidth >= 640) {
                    return;
                }
                
                // Don't trigger if clicking on calendar item directly - let item handler work
                if (e.target.closest('.calendar-item')) {
                    return;
                }
                
                // Find the closest calendar-day element
                const calendarDay = e.target.closest('.mobile-calendar-day');
                if (!calendarDay) {
                    return;
                }
                
                // Check where user clicked
                const clickedOnHeader = e.target.closest('.calendar-day-header');
                const clickedOnContainer = e.target.closest('.calendar-items-container');
                const clickedOnItem = e.target.closest('.calendar-item');
                
                // If clicked on item, let item handler work (don't show modal)
                if (clickedOnItem) {
                    return;
                }
                
                // Show modal if:
                // 1. Clicked on header, OR
                // 2. Clicked on container (empty space or "Tidak ada" text), OR  
                // 3. Clicked on calendar-day itself (outside header and container)
                // Note: Scroll is handled by touchmove/wheel events, not click, so it will still work
                
                // Get day data
                const dayDataStr = calendarDay.getAttribute('data-day-data');
                if (!dayDataStr) {
                    return;
                }
                
                try {
                    const dayData = JSON.parse(dayDataStr);
                    
                    // Show modal
                    e.preventDefault();
                    e.stopPropagation();
                    showDayMeetingsModal(dayData);
                } catch (err) {
                    console.error('Error parsing day data:', err);
                }
            });
        }


        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Show modal with all meetings for a specific day
        function showDayMeetingsModal(day) {
            const dateStr = day.date || `${day.day} ${day.month || ''}`;
            const items = day.items || [];
            
            let meetingsHtml = '';
            
            if (items.length === 0) {
                meetingsHtml = `
                    <div class="text-center py-8">
                        <i class="fas fa-calendar-times text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-600">Tidak ada meeting pada hari ini</p>
                    </div>
                `;
            } else {
                // Sort items by start_time
                const sortedItems = [...items].sort((a, b) => {
                    const timeA = a.start_time || '00:00';
                    const timeB = b.start_time || '00:00';
                    return timeA.localeCompare(timeB);
                });
                
                meetingsHtml = sortedItems.map((item, index) => {
                    let itemHtml = `
                        <div class="bg-white border border-gray-200 rounded-lg p-4 mb-3 cursor-pointer hover:shadow-md transition-shadow day-meeting-item"
                             data-item-index="${index}"
                             data-day-number="${day.day}"
                             onclick="handleDayMeetingItemClick(event, this)">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900 mb-2">${escapeHtml(item.title || 'Meeting')}</h4>
                                    <div class="space-y-1 text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <i class="fas fa-clock text-blue-500 mr-2 w-4"></i>
                                            <span>${escapeHtml(item.start_time || '')} - ${escapeHtml(item.end_time || '')}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-door-open text-green-500 mr-2 w-4"></i>
                                            <span>${escapeHtml(item.room || 'Ruang tidak ditentukan')}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-user text-purple-500 mr-2 w-4"></i>
                                            <span>${escapeHtml(item.pic_name || 'PIC tidak ditentukan')}</span>
                                        </div>
                                        ${item.unit_kerja ? `
                                        <div class="flex items-center">
                                            <i class="fas fa-building text-orange-500 mr-2 w-4"></i>
                                            <span>${escapeHtml(item.unit_kerja)}</span>
                                        </div>
                                        ` : ''}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <i class="fas fa-chevron-right text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                    `;
                    return itemHtml;
                }).join('');
                
                // Store items data for click handler
                if (!window.dayMeetingsData) {
                    window.dayMeetingsData = {};
                }
                window.dayMeetingsData[day.day] = sortedItems;
            }
            
            const modalContent = `
                <div id="dayMeetingsModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" onclick="closeDayMeetingsModal()">
                    <div class="bg-white rounded-2xl max-w-md w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()" data-day-number="${day.day}">
                        <div class="sticky top-0 bg-white border-b border-gray-200 z-10 p-4 sm:p-6 pb-4 flex justify-between items-center">
                            <div>
                                <h3 class="text-lg sm:text-xl font-bold text-gray-800">Meeting Hari ${day.day}</h3>
                                <p class="text-sm text-gray-500 mt-1">${dateStr} • ${items.length} meeting</p>
                            </div>
                            <button type="button" onclick="closeDayMeetingsModal()" class="text-gray-500 hover:text-gray-700 p-2 -mr-2">
                                <i class="fas fa-times text-xl sm:text-2xl"></i>
                            </button>
                        </div>
                        
                        <div class="p-4 sm:p-6">
                            ${meetingsHtml}
                        </div>
                        
                        <div class="sticky bottom-0 bg-white border-t border-gray-200 p-4 sm:p-6 pt-4">
                            <button type="button" onclick="closeDayMeetingsModal()" class="w-full px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            // Remove existing modal if any
            const existingModal = document.getElementById('dayMeetingsModal');
            if (existingModal) {
                existingModal.remove();
            }
            
            // Add modal to body
            document.body.insertAdjacentHTML('beforeend', modalContent);
            
            // Add event listener for ESC key
            document.addEventListener('keydown', function escHandler(e) {
                if (e.key === 'Escape') {
                    const modal = document.getElementById('dayMeetingsModal');
                    if (modal && !modal.classList.contains('hidden')) {
                        closeDayMeetingsModal();
                        document.removeEventListener('keydown', escHandler);
                    }
                }
            });
        }

        // Close day meetings modal
        function closeDayMeetingsModal() {
            const modal = document.getElementById('dayMeetingsModal');
            if (modal) {
                modal.remove();
            }
        }

        // Handle click on meeting item in modal - using direct function call
        function handleDayMeetingItemClick(event, element) {
            // Prevent event from bubbling up
            event.preventDefault();
            event.stopPropagation();
            
            // Get item index and day number from element
            const itemIndex = parseInt(element.getAttribute('data-item-index'));
            const dayNumber = element.getAttribute('data-day-number');
            
            // Get items data from window.dayMeetingsData using day number
            if (window.dayMeetingsData && window.dayMeetingsData[dayNumber]) {
                const items = window.dayMeetingsData[dayNumber];
                
                if (items && items[itemIndex]) {
                    // Close modal first
                    closeDayMeetingsModal();
                    
                    // Small delay to ensure modal is closed before showing detail
                    setTimeout(() => {
                        showCalendarItemDetails(items[itemIndex]);
                    }, 100);
                    return;
                }
            }
            
            // Fallback: try to find modal and get data
            const modal = document.getElementById('dayMeetingsModal');
            if (modal) {
                const modalContainer = modal.querySelector('[data-day-number]');
                if (modalContainer && window.dayMeetingsData) {
                    const dayNum = modalContainer.getAttribute('data-day-number');
                    const items = window.dayMeetingsData[dayNum];
                    
                    if (items && items[itemIndex]) {
                        closeDayMeetingsModal();
                        setTimeout(() => {
                            showCalendarItemDetails(items[itemIndex]);
                        }, 100);
                        return;
                    }
                }
                
                // Final fallback: try using header text to find day
                const dayHeader = modal.querySelector('h3');
                if (dayHeader) {
                    const dayMatch = dayHeader.textContent.match(/Hari (\d+)/);
                    if (dayMatch && window.dayMeetingsData) {
                        const day = dayMatch[1];
                        const items = window.dayMeetingsData[day];
                        if (items && items[itemIndex]) {
                            closeDayMeetingsModal();
                            setTimeout(() => {
                                showCalendarItemDetails(items[itemIndex]);
                            }, 100);
                        }
                    }
                }
            }
        }
        
        // Also handle click with event delegation as backup
        document.addEventListener('click', function(e) {
            const meetingItem = e.target.closest('.day-meeting-item');
            if (meetingItem) {
                const modal = document.getElementById('dayMeetingsModal');
                if (modal) {
                    // Prevent event from bubbling up
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const itemIndex = parseInt(meetingItem.getAttribute('data-item-index'));
                    const modalContainer = modal.querySelector('[data-day-number]');
                    
                    if (modalContainer && window.dayMeetingsData) {
                        const dayNumber = modalContainer.getAttribute('data-day-number');
                        const items = window.dayMeetingsData[dayNumber];
                        
                        if (items && items[itemIndex]) {
                            closeDayMeetingsModal();
                            setTimeout(() => {
                                showCalendarItemDetails(items[itemIndex]);
                            }, 100);
                        }
                    }
                }
            }
        });

        // Calendar item detail modal
        function showCalendarItemDetails(item) {
            console.log('Calendar item data:', item); // Debug log
            
            let descriptionHtml = '';
            
            if (item.can_see_description && item.description) {
                // Process links in description (Zoom/Meet links)
                const processedDescription = item.description.replace(
                    /(https?:\/\/[^\s]+)/g, 
                    '<a href="$1" target="_blank" class="text-blue-400 hover:text-blue-300 underline">$1</a>'
                );
                
                descriptionHtml = `
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="font-medium text-blue-800 mb-2">Deskripsi Meeting</h4>
                        <div class="text-sm text-blue-700">${processedDescription}</div>
                    </div>
                `;
            } else if (item.is_invited_pic) {
                descriptionHtml = `
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div class="text-sm text-gray-600">
                            <i class="fas fa-user-check mr-2"></i>
                            Anda adalah PIC yang diundang ke meeting ini
                        </div>
                    </div>
                `;
            } else if (item.description && !item.can_see_description) {
                // Show message that description exists but user can't see it
                descriptionHtml = `
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div class="text-sm text-gray-600">
                            <i class="fas fa-lock mr-2"></i>
                            Deskripsi meeting tersedia namun hanya untuk PIC yang diundang
                        </div>
                    </div>
                `;
            } else if (!item.description) {
                // Show message that no description is available
                descriptionHtml = `
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div class="text-sm text-gray-600">
                            <i class="fas fa-info-circle mr-2"></i>
                            Tidak ada deskripsi meeting
                        </div>
                    </div>
                `;
            }
            
            // PDF Document section
            let documentHtml = '';
            if (item.has_document) {
                if (item.can_see_document && item.document_url) {
                    // Detect if device is mobile
                    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
                    const pdfViewerId = `pdf-viewer-${item.id}`;
                    
                    // Render PDF with multiple fallback options
                    documentHtml = `
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mt-4">
                            <h4 class="font-medium text-gray-800 mb-3 flex items-center">
                                <i class="fas fa-file-pdf text-red-500 mr-2"></i>
                                Dokumen Tambahan
                            </h4>
                            <div class="bg-white rounded-lg border border-gray-300 p-4">
                                <div class="mb-3 flex flex-wrap gap-2">
                                    <a href="${item.document_url}" 
                                       target="_blank" 
                                       rel="noopener noreferrer"
                                       class="inline-flex items-center px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded text-sm transition-colors duration-300">
                                        <i class="fas fa-external-link-alt mr-2"></i>
                                        Buka di tab baru
                                    </a>
                                    <button onclick="downloadPDF('${item.document_url}')" 
                                            class="inline-flex items-center px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white rounded text-sm transition-colors duration-300">
                                        <i class="fas fa-download mr-2"></i>
                                        Download
                                    </button>
                                    ${isMobile ? `
                                    <a href="https://docs.google.com/viewer?url=${encodeURIComponent(window.location.origin + item.document_url)}&embedded=true" 
                                       target="_blank" 
                                       rel="noopener noreferrer"
                                       class="inline-flex items-center px-3 py-1.5 bg-purple-500 hover:bg-purple-600 text-white rounded text-sm transition-colors duration-300">
                                        <i class="fas fa-mobile-alt mr-2"></i>
                                        Mobile Viewer
                                    </a>
                                    ` : ''}
                                </div>
                                <div class="border border-gray-300 rounded-lg overflow-hidden relative bg-gray-50" style="min-height: 400px; height: ${isMobile ? '400px' : '600px'};">
                                    <!-- Native iframe (works in Chrome, Edge, Firefox) -->
                                    <iframe id="${pdfViewerId}-iframe" 
                                            src="${item.document_url}" 
                                            class="w-full h-full border-0" 
                                            frameborder="0"
                                            title="PDF Document Viewer"
                                            style="display: none;">
                                    </iframe>
                                    
                                    <!-- PDF.js Canvas Container (fallback for Safari and other browsers) -->
                                    <div id="${pdfViewerId}-canvas-container" class="w-full h-full overflow-y-auto" style="display: none;">
                                        <canvas id="${pdfViewerId}-canvas" 
                                                class="mx-auto block">
                                        </canvas>
                                    </div>
                                    
                                    <!-- Loading indicator -->
                                    <div id="${pdfViewerId}-loading" class="absolute inset-0 flex items-center justify-center bg-gray-100">
                                        <div class="text-center">
                                            <i class="fas fa-spinner fa-spin text-4xl text-blue-500 mb-3"></i>
                                            <p class="text-gray-600">Memuat PDF...</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Error message -->
                                    <div id="${pdfViewerId}-error" class="hidden absolute inset-0 flex items-center justify-center bg-gray-100 p-4">
                                        <div class="text-center">
                                            <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-3"></i>
                                            <p class="text-gray-700 mb-2">Gagal memuat PDF di viewer</p>
                                            <a href="${item.document_url}" 
                                               target="_blank" 
                                               class="text-blue-500 hover:underline">
                                                Klik di sini untuk membuka PDF di tab baru
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    // Load PDF after modal is rendered
                    setTimeout(() => {
                        loadPDFViewer(item.document_url, pdfViewerId, isMobile);
                    }, 100);
                } else {
                    // Show message that document exists but user can't see it
                    documentHtml = `
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mt-4">
                            <div class="text-sm text-gray-600">
                                <i class="fas fa-lock mr-2"></i>
                                Dokumen tambahan tersedia namun hanya untuk PIC yang diundang
                            </div>
                        </div>
                    `;
                }
            } else {
                // No document available
                documentHtml = `
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mt-4 hidden">
                        <!-- Hidden if no document -->
                    </div>
                `;
            }
            
            // Invited PICs block
            let invitedHtml = '';
            if (Array.isArray(item.invited_pics) && item.invited_pics.length > 0) {
                invitedHtml = `
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <h4 class="font-medium text-gray-800 mb-3">PIC yang Diundang</h4>
                        <ul class="list-disc pl-5 text-sm text-gray-700">
                            ${item.invited_pics.map(p => `<li>${(p.name || 'Tidak diketahui')}${p.unit_kerja ? ' - ' + p.unit_kerja : ''}</li>`).join('')}
                        </ul>
                    </div>
                `;
            }
            
            const modalContent = `
                <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-2 sm:p-4" onclick="closeBookingModal()">
                    <div class="bg-white rounded-2xl max-w-md w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
                        <div class="sticky top-0 bg-white border-b border-gray-200 z-10 p-4 sm:p-6 pb-4 flex justify-between items-center">
                            <h3 class="text-lg sm:text-xl font-bold text-gray-800">Detail Meeting</h3>
                            <button onclick="closeBookingModal()" class="text-gray-500 hover:text-gray-700 p-2 -mr-2">
                                <i class="fas fa-times text-xl sm:text-2xl"></i>
                            </button>
                        </div>
                        
                        <div class="p-4 sm:p-6 space-y-4">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-blue-600 font-medium">Judul:</span>
                                        <span class="text-blue-800 font-medium">${item.title}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-blue-600 font-medium">Waktu:</span>
                                        <span class="text-blue-800">${item.start_time} - ${item.end_time}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-blue-600 font-medium">Ruangan:</span>
                                        <span class="text-blue-800">${item.room}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <h4 class="font-medium text-gray-800 mb-3">PIC Penyelenggara</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 font-medium">Nama PIC:</span>
                                        <span class="text-gray-800">${item.pic_name}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 font-medium">Unit Kerja:</span>
                                        <span class="text-gray-800">${item.unit_kerja}</span>
                                    </div>
                                </div>
                            </div>
                            
                            ${descriptionHtml}
                            ${documentHtml}
                            ${invitedHtml}
                        </div>
                        
                        <div class="sticky bottom-0 bg-white border-t border-gray-200 p-4 sm:p-6 pt-4 flex justify-end">
                            <button onclick="closeBookingModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 w-full sm:w-auto">Tutup</button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalContent);
        }

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
                <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-2 sm:p-4" onclick="closeBookingModal()">
                    <div class="bg-white rounded-2xl max-w-md w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
                        <div class="sticky top-0 bg-white border-b border-gray-200 z-10 p-4 sm:p-6 pb-4 flex justify-between items-center">
                            <h3 class="text-lg sm:text-xl font-bold text-gray-800">Detail Booking Aktif</h3>
                            <button onclick="closeBookingModal()" class="text-gray-500 hover:text-gray-700 p-2 -mr-2">
                                <i class="fas fa-times text-xl sm:text-2xl"></i>
                            </button>
                        </div>
                        
                        <div class="p-4 sm:p-6 space-y-4">
                            <!-- Status Alert -->
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <div class="flex items-center space-x-2 mb-2">
                                    <i class="fas fa-times-circle text-red-500"></i>
                                    <span class="font-medium text-red-800">Status: Sedang Dibooking</span>
                                </div>
                                <p class="text-red-700 text-sm">Ruang ini sedang digunakan untuk meeting</p>
                            </div>
                            
                            <!-- Meeting Details -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h4 class="font-medium text-blue-800 mb-3">Informasi Meeting</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-blue-600 font-medium">Judul:</span>
                                        <span class="text-blue-800 font-medium">${booking.title}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-blue-600 font-medium">Waktu:</span>
                                        <span class="text-blue-800">${booking.start_time} - ${booking.end_time}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-blue-600 font-medium">Status:</span>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium
                                            ${booking.status === 'pending' ? 'bg-yellow-500 text-white' : 
                                              booking.status === 'confirmed' ? 'bg-green-500 text-white' : 
                                              'bg-gray-500 text-white'}">
                                            ${booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Organizer Details -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <h4 class="font-medium text-gray-800 mb-3">Informasi Penyelenggara</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 font-medium">Nama:</span>
                                        <span class="text-gray-800">${booking.user_name}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 font-medium">Unit Kerja:</span>
                                        <span class="text-gray-800">${booking.unit_kerja}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Additional Info -->
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-info-circle text-yellow-500"></i>
                                    <span class="text-yellow-800 text-sm">Ruang tidak tersedia untuk waktu ini</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="sticky bottom-0 bg-white border-t border-gray-200 p-4 sm:p-6 pt-4 flex justify-end">
                            <button onclick="closeBookingModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 w-full sm:w-auto">
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
                <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-2 sm:p-4" onclick="closeBookingModal()">
                    <div class="bg-white rounded-2xl max-w-md w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
                        <div class="sticky top-0 bg-white border-b border-gray-200 z-10 p-4 sm:p-6 pb-4 flex justify-between items-center">
                            <h3 class="text-lg sm:text-xl font-bold text-gray-800">Riwayat Penggunaan</h3>
                            <button onclick="closeBookingModal()" class="text-gray-500 hover:text-gray-700 p-2 -mr-2">
                                <i class="fas fa-times text-xl sm:text-2xl"></i>
                            </button>
                        </div>
                        
                        <div class="p-4 sm:p-6 space-y-4">
                            <!-- Status Alert -->
                            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                                <div class="flex items-center space-x-2 mb-2">
                                    <i class="fas fa-history text-orange-500"></i>
                                    <span class="font-medium text-orange-800">Status: Pernah Digunakan</span>
                                </div>
                                <p class="text-orange-700 text-sm">Ruang ini pernah digunakan dan sekarang tersedia</p>
                            </div>
                            
                            <!-- Previous Meeting Details -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h4 class="font-medium text-blue-800 mb-3">Informasi Meeting Sebelumnya</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-blue-600 font-medium">Judul:</span>
                                        <span class="text-blue-800 font-medium">${booking.title}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-blue-600 font-medium">Waktu:</span>
                                        <span class="text-blue-800">${booking.start_time} - ${booking.end_time}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-blue-600 font-medium">Status:</span>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-500 text-white">
                                            Selesai
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Previous Organizer Details -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <h4 class="font-medium text-gray-800 mb-3">Penyelenggara Sebelumnya</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 font-medium">Nama:</span>
                                        <span class="text-gray-800">${booking.user_name}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 font-medium">Unit Kerja:</span>
                                        <span class="text-gray-800">${booking.unit_kerja}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Current Availability -->
                            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-check-circle text-green-500"></i>
                                    <span class="text-green-800 text-sm">Ruang sekarang tersedia untuk dipesan</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="sticky bottom-0 bg-white border-t border-gray-200 p-4 sm:p-6 pt-4 flex justify-end">
                            <button onclick="closeBookingModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 w-full sm:w-auto">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            // Add modal to body
            document.body.insertAdjacentHTML('beforeend', modalContent);
        }

        function showPreviousBookingDetailsWithBooking(bookingData, roomId, roomName, roomLocation, roomCapacity, time, datetime) {
            const booking = JSON.parse(bookingData);
            
            // Create modal content
            const modalContent = `
                <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-2 sm:p-4" onclick="closeBookingModal()">
                    <div class="bg-white rounded-2xl max-w-md w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
                        <div class="sticky top-0 bg-white border-b border-gray-200 z-10 p-4 sm:p-6 pb-4 flex justify-between items-center">
                            <h3 class="text-lg sm:text-xl font-bold text-gray-800">Riwayat Penggunaan</h3>
                            <button onclick="closeBookingModal()" class="text-gray-500 hover:text-gray-700 p-2 -mr-2">
                                <i class="fas fa-times text-xl sm:text-2xl"></i>
                            </button>
                        </div>
                        
                        <div class="p-4 sm:p-6 space-y-4">
                            <!-- Status Alert -->
                            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                                <div class="flex items-center space-x-2 mb-2">
                                    <i class="fas fa-history text-orange-500"></i>
                                    <span class="font-medium text-orange-800">Status: Pernah Digunakan</span>
                                </div>
                                <p class="text-orange-700 text-sm">Ruang ini pernah digunakan dan sekarang tersedia</p>
                            </div>
                            
                            <!-- Previous Meeting Details -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h4 class="font-medium text-blue-800 mb-3">Informasi Meeting Sebelumnya</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-blue-600 font-medium">Judul:</span>
                                        <span class="text-blue-800 font-medium">${booking.title}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-blue-600 font-medium">Waktu:</span>
                                        <span class="text-blue-800">${booking.start_time} - ${booking.end_time}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-blue-600 font-medium">Status:</span>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-500 text-white">
                                            Selesai
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Previous Organizer Details -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <h4 class="font-medium text-gray-800 mb-3">Penyelenggara Sebelumnya</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 font-medium">Nama:</span>
                                        <span class="text-gray-800">${booking.user_name}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 font-medium">Unit Kerja:</span>
                                        <span class="text-gray-800">${booking.unit_kerja}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Current Room Info -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h4 class="font-medium text-blue-800 mb-3">Informasi Ruang Saat Ini</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-blue-600 font-medium">Nama:</span>
                                        <span class="text-blue-800">${roomName}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-blue-600 font-medium">Lokasi:</span>
                                        <span class="text-blue-800">${roomLocation}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-blue-600 font-medium">Kapasitas:</span>
                                        <span class="text-blue-800">${roomCapacity} kursi</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-blue-600 font-medium">Waktu:</span>
                                        <span class="text-blue-800">${time}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Current Availability -->
                            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-check-circle text-green-500"></i>
                                    <span class="text-green-800 text-sm">Ruang sekarang tersedia untuk dipesan</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="sticky bottom-0 bg-white border-t border-gray-200 p-4 sm:p-6 pt-4 flex flex-col sm:flex-row justify-end gap-2 sm:gap-3">
                            <button onclick="closeBookingModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 w-full sm:w-auto">
                                Tutup
                            </button>
                            <button onclick="proceedToBooking('${roomId}', '${datetime}', '${roomName}')" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 w-full sm:w-auto">
                                <i class="fas fa-calendar-plus mr-2"></i>Booking Sekarang
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
                // Quick click - show appropriate popup
                const isAvailable = element.getAttribute('data-is-available') === 'true';
                const isPastTime = element.getAttribute('data-is-past-time') === 'true';
                const wasUsed = element.getAttribute('data-was-used') === 'true';
                const roomId = element.getAttribute('data-room-id');
                const datetime = element.getAttribute('data-datetime');
                const roomName = element.getAttribute('data-room-name');
                const roomLocation = element.getAttribute('data-room-location');
                const roomCapacity = element.getAttribute('data-room-capacity');
                const time = element.getAttribute('data-time');
                const booking = element.getAttribute('data-booking');
                const previousBooking = element.getAttribute('data-previous-booking');
                
                if (isPastTime) {
                    // Gray slot - show past time message
                    showPastTimeMessage(time);
                } else if (isAvailable && !wasUsed) {
                    // Green slot - show room details with booking button
                    showRoomDetailsModal(roomId, roomName, roomLocation, roomCapacity, time, datetime);
                } else if (isAvailable && wasUsed) {
                    // Orange slot - show booking history with booking button
                    if (previousBooking && previousBooking !== 'null') {
                        showPreviousBookingDetailsWithBooking(previousBooking, roomId, roomName, roomLocation, roomCapacity, time, datetime);
                    }
                } else if (!isAvailable) {
                    // Red slot - show current booking details
                    if (booking && booking !== 'null') {
                        showBookingDetails(booking);
                    }
                }
            }
            isHolding = false;
        }

        function showSlotDetails(element) {
            const slotInfo = {
                room_id: element.getAttribute('data-room-id'),
                room_name: element.getAttribute('data-room-name'),
                room_location: element.getAttribute('data-room-location'),
                room_capacity: element.getAttribute('data-room-capacity'),
                time: element.getAttribute('data-time'),
                datetime: element.getAttribute('data-datetime'),
                isAvailable: element.getAttribute('data-is-available') === 'true',
                wasUsed: element.getAttribute('data-was-used') === 'true',
                booking: element.getAttribute('data-booking') !== 'null' ? JSON.parse(element.getAttribute('data-booking')) : null,
                previousBooking: element.getAttribute('data-previous-booking') !== 'null' ? JSON.parse(element.getAttribute('data-previous-booking')) : null
            };
            
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

        function showRoomDetailsModal(roomId, roomName, roomLocation, roomCapacity, time, datetime) {
            const modalContent = `
                <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" onclick="closeRoomDetailsModal()">
                    <div class="bg-white rounded-2xl max-w-md w-full p-6" onclick="event.stopPropagation()">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold text-gray-800">Detail Ruang Meeting</h3>
                            <button onclick="closeRoomDetailsModal()" class="text-gray-500 hover:text-gray-700">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <!-- Room Info -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-center space-x-2 mb-3">
                                    <i class="fas fa-door-open text-blue-500"></i>
                                    <h4 class="font-medium text-blue-800">${roomName}</h4>
                                </div>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-blue-600 font-medium">Lokasi:</span>
                                        <span class="text-blue-800">${roomLocation}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-blue-600 font-medium">Kapasitas:</span>
                                        <span class="text-blue-800">${roomCapacity} kursi</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-blue-600 font-medium">Waktu:</span>
                                        <span class="text-blue-800">${time}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Availability Status -->
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="flex items-center space-x-2 mb-2">
                                    <i class="fas fa-check-circle text-green-500"></i>
                                    <span class="font-medium text-green-800">Status: Tersedia</span>
                                </div>
                                <p class="text-green-700 text-sm">Ruang ini tersedia untuk dipesan pada jam ${time}</p>
                            </div>
                            
                            <!-- Booking Info -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <h4 class="font-medium text-gray-800 mb-2">Informasi Booking</h4>
                                <div class="space-y-1 text-sm">
                                    <div><span class="text-gray-600 font-medium">Waktu yang dipilih:</span> <span class="text-gray-800">${time}</span></div>
                                    <div><span class="text-gray-600 font-medium">Tanggal:</span> <span class="text-gray-800">${new Date(datetime).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</span></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-end space-x-3 mt-6">
                            <button onclick="closeRoomDetailsModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                Batal
                            </button>
                            <button onclick="proceedToBooking('${roomId}', '${datetime}', '${roomName}')" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                                <i class="fas fa-calendar-plus mr-2"></i>Booking Sekarang
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            // Add modal to body
            document.body.insertAdjacentHTML('beforeend', modalContent);
        }

        function closeRoomDetailsModal() {
            const modal = document.querySelector('.fixed.inset-0.bg-black.bg-opacity-50');
            if (modal) {
                modal.remove();
            }
        }

        function showPastTimeMessage(time) {
            // Remove any existing modal
            const existingModal = document.querySelector('.fixed.inset-0.bg-black.bg-opacity-50');
            if (existingModal) {
                existingModal.remove();
            }

            const modalContent = `
                <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white rounded-2xl max-w-md w-full">
                        <div class="p-6">
                            <div class="flex items-center justify-center mb-4">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-clock text-gray-500 text-2xl"></i>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <h3 class="text-xl font-bold text-gray-800 mb-2">Waktu Sudah Lewat</h3>
                                <p class="text-gray-600 mb-4">
                                    Slot waktu <strong>${time}</strong> sudah lewat dan tidak dapat dipesan lagi.
                                </p>
                                <p class="text-gray-500 text-sm mb-6">
                                    Silakan pilih slot waktu yang tersedia untuk hari ini atau hari berikutnya.
                                </p>
                            </div>
                            
                            <div class="flex justify-center">
                                <button onclick="closeBookingModal()" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                                    <i class="fas fa-times mr-2"></i>Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Add modal to body
            document.body.insertAdjacentHTML('beforeend', modalContent);
        }

        function proceedToBooking(roomId, datetime, roomName) {
            // Redirect to booking creation with pre-filled data
            const url = new URL('{{ route("user.bookings.create") }}', window.location.origin);
            url.searchParams.set('room_id', roomId);
            url.searchParams.set('start_time', datetime);
            window.location.href = url.toString();
        }
    </script>
</body>
</html>
