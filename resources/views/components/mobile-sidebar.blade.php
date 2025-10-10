{{-- Mobile Sidebar Component --}}
<div class="mobile-sidebar" id="mobileSidebar">
    <!-- Overlay -->
    <div class="mobile-sidebar-overlay" onclick="toggleMobileSidebar()"></div>
    
    <!-- Sidebar Content -->
    <div class="mobile-sidebar-content">
        <!-- Header -->
        <div class="mobile-sidebar-header">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-calendar-alt text-2xl text-white mr-3"></i>
                    <div>
                        <h1 class="text-lg font-bold text-white">Sistem Pemesanan</h1>
                        <p class="text-white/80 text-xs">Ruang Meeting</p>
                    </div>
                </div>
                <button onclick="toggleMobileSidebar()" class="text-white/80 hover:text-white p-2">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Logout Button - At Top -->
        @if(isset($userRole) && in_array($userRole, ['admin', 'user']))
            <div class="mobile-nav-actions px-4 py-2">
                <a href="{{ route('logout') }}" class="mobile-nav-item text-red-400 hover:text-red-300 bg-red-500/10 hover:bg-red-500/20 rounded-lg">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Keluar</span>
                </a>
            </div>
            <div class="mobile-nav-divider"></div>
        @endif

        <!-- Navigation Menu -->
        <nav class="mobile-sidebar-nav">
            @if(isset($userRole) && $userRole === 'admin')
                {{-- Admin Navigation --}}
                <a href="{{ route('admin.dashboard') }}" class="mobile-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Beranda</span>
                </a>
                
                <a href="{{ route('admin.users') }}" class="mobile-nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Pengguna</span>
                </a>
                
                <a href="{{ route('admin.rooms') }}" class="mobile-nav-item {{ request()->routeIs('admin.rooms*') ? 'active' : '' }}">
                    <i class="fas fa-door-open"></i>
                    <span>Ruang Meeting</span>
                </a>
                
                <a href="{{ route('admin.bookings') }}" class="mobile-nav-item {{ request()->routeIs('admin.bookings*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-check"></i>
                    <span>Pemesanan</span>
                </a>
                
                <div class="mobile-nav-divider"></div>
                
                <a href="{{ route('admin.users.create') }}" class="mobile-nav-item">
                    <i class="fas fa-user-plus"></i>
                    <span>Tambah Pengguna</span>
                </a>
                
                <a href="{{ route('admin.rooms.create') }}" class="mobile-nav-item">
                    <i class="fas fa-plus-circle"></i>
                    <span>Tambah Ruang</span>
                </a>
                
            @elseif(isset($userRole) && $userRole === 'user')
                {{-- User Navigation --}}
                <a href="{{ route('user.dashboard') }}" class="mobile-nav-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Beranda</span>
                </a>
                
                <a href="{{ route('user.bookings') }}" class="mobile-nav-item {{ request()->routeIs('user.bookings') ? 'active' : '' }}">
                    <i class="fas fa-calendar-check"></i>
                    <span>Pemesanan Saya</span>
                </a>
                
                <a href="{{ route('user.bookings.create') }}" class="mobile-nav-item {{ request()->routeIs('user.bookings.create') ? 'active' : '' }}">
                    <i class="fas fa-plus"></i>
                    <span>Pemesanan Baru</span>
                </a>
                
                <a href="{{ route('user.profile') }}" class="mobile-nav-item {{ request()->routeIs('user.profile') ? 'active' : '' }}">
                    <i class="fas fa-user"></i>
                    <span>Profil</span>
                </a>
                
            @else
                {{-- Guest Navigation --}}
                <a href="{{ route('login') }}" class="mobile-nav-item">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Masuk</span>
                </a>
                
                <a href="{{ route('register') }}" class="mobile-nav-item">
                    <i class="fas fa-user-plus"></i>
                    <span>Daftar</span>
                </a>
            @endif
        </nav>

        <!-- User Info & Actions -->
        @if(isset($userRole) && in_array($userRole, ['admin', 'user']))
            <div class="mobile-sidebar-footer">
                <div class="mobile-user-info">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-white font-medium text-sm truncate">{{ $userName ?? 'User' }}</p>
                            <p class="text-white/60 text-xs truncate">{{ $userEmail ?? 'user@example.com' }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="mobile-nav-divider"></div>
                
                <div class="mobile-nav-actions">
                    @if(isset($userRole) && $userRole === 'admin')
                        <!-- Admin Notifikasi -->
                        <button onclick="toggleAdminNotifikasis()" class="mobile-nav-item">
                            <i class="fas fa-bell"></i>
                            <span>Notifikasi</span>
                            <span id="admin-notification-badge" class="mobile-notification-badge hidden">0</span>
                        </button>
                    @elseif(isset($userRole) && $userRole === 'user')
                        <!-- User Notifikasi -->
                        <button onclick="toggleNotifikasis()" class="mobile-nav-item">
                            <i class="fas fa-bell"></i>
                            <span>Notifikasi</span>
                            <span id="notification-badge" class="mobile-notification-badge hidden">0</span>
                        </button>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Mobile Menu Button -->
<button onclick="toggleMobileSidebar()" class="mobile-menu-btn" id="mobileMenuBtn">
    <i class="fas fa-bars"></i>
</button>

<style>
/* Mobile Sidebar Styles */
.mobile-sidebar {
    position: fixed;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100vh;
    z-index: 9999;
    transition: left 0.3s ease;
}

.mobile-sidebar.active {
    left: 0;
}

.mobile-sidebar-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
}

.mobile-sidebar-content {
    position: absolute;
    top: 0;
    left: 0;
    width: 320px;
    max-width: 85vw;
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    overflow-y: auto;
    display: flex;
    flex-direction: column;
}

.mobile-sidebar-header {
    padding: 1.5rem 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    flex-shrink: 0;
}

.mobile-sidebar-nav {
    flex: 1;
    padding: 1rem 0;
    overflow-y: auto;
}

.mobile-nav-item {
    display: flex;
    align-items: center;
    padding: 0.875rem 1.5rem;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
    position: relative;
}

.mobile-nav-item:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border-left-color: rgba(255, 255, 255, 0.5);
}

.mobile-nav-item.active {
    background: rgba(255, 255, 255, 0.15);
    color: white;
    border-left-color: #fff;
}

.mobile-nav-item i {
    width: 24px;
    margin-right: 0.75rem;
    text-align: center;
    font-size: 1.1rem;
}

.mobile-nav-item span {
    flex: 1;
    font-size: 0.95rem;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.mobile-nav-divider {
    height: 1px;
    background: rgba(255, 255, 255, 0.1);
    margin: 0.5rem 1.5rem;
}

.mobile-sidebar-footer {
    padding: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    flex-shrink: 0;
}

.mobile-user-info {
    margin-bottom: 1rem;
}

.mobile-nav-actions {
    display: flex;
    flex-direction: column;
}

.mobile-notification-badge {
    background: #ef4444;
    color: white;
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 50%;
    min-width: 1.25rem;
    height: 1.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: auto;
}

.mobile-menu-btn {
    position: fixed;
    top: 0.75rem;
    left: 1rem;
    z-index: 1000;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    border: none;
    padding: 0.75rem;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    height: 2.5rem;
    width: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.mobile-menu-btn:hover {
    background: rgba(0, 0, 0, 0.9);
    transform: scale(1.05);
}

.mobile-menu-btn i {
    font-size: 1.25rem;
}

/* Show mobile menu button only on mobile */
@media (max-width: 768px) {
    .mobile-menu-btn {
        display: block;
    }
    
    /* Hide desktop navigation on mobile */
    .desktop-nav {
        display: none !important;
    }
}

/* Hide mobile sidebar on desktop */
@media (min-width: 769px) {
    .mobile-sidebar {
        display: none !important;
    }
    
    .mobile-menu-btn {
        display: none !important;
    }
}

/* Prevent horizontal scroll */
body {
    overflow-x: hidden;
}

/* Ensure content doesn't overflow */
.container, .max-w-7xl, .max-w-6xl, .max-w-5xl, .max-w-4xl, .max-w-3xl, .max-w-2xl, .max-w-xl, .max-w-lg, .max-w-md, .max-w-sm {
    max-width: 100%;
    padding-left: 1rem;
    padding-right: 1rem;
}

/* Fix for mobile layout */
@media (max-width: 768px) {
    .container {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    
    /* Ensure tables are responsive */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    /* Fix form elements */
    input, select, textarea {
        max-width: 100%;
        box-sizing: border-box;
    }
    
    /* Fix buttons */
    .btn, button {
        min-height: 44px; /* iOS touch target */
        padding: 0.75rem 1rem;
    }
    
    /* Mobile sidebar improvements */
    .mobile-sidebar-content {
        width: 100%;
        max-width: 90vw;
    }
    
    .mobile-nav-item {
        padding: 1rem 1.5rem;
        min-height: 48px;
    }
    
    .mobile-nav-item span {
        font-size: 1rem;
        line-height: 1.2;
    }
    
    .mobile-sidebar-header {
        padding: 1rem;
    }
    
    .mobile-sidebar-header h1 {
        font-size: 1.125rem;
        line-height: 1.2;
    }
    
    .mobile-sidebar-header p {
        font-size: 0.75rem;
        line-height: 1.2;
    }
    
    /* Ensure text doesn't wrap inappropriately */
    .mobile-nav-item i {
        flex-shrink: 0;
        width: 24px;
    }
    
    .mobile-nav-item span {
        flex: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    /* Fix user info section */
    .mobile-user-info {
        padding: 0.5rem 0;
    }
    
    .mobile-user-info p {
        line-height: 1.2;
        margin-bottom: 0.25rem;
    }
}
</style>

<script>
function toggleMobileSidebar() {
    const sidebar = document.getElementById('mobileSidebar');
    const body = document.body;
    
    if (sidebar.classList.contains('active')) {
        sidebar.classList.remove('active');
        body.style.overflow = '';
    } else {
        sidebar.classList.add('active');
        body.style.overflow = 'hidden';
    }
}

// Close sidebar when clicking outside
document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('mobileSidebar');
    const menuBtn = document.getElementById('mobileMenuBtn');
    
    if (sidebar && !sidebar.contains(event.target) && !menuBtn.contains(event.target)) {
        if (sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
            document.body.style.overflow = '';
        }
    }
});

// Close sidebar on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const sidebar = document.getElementById('mobileSidebar');
        if (sidebar && sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
            document.body.style.overflow = '';
        }
    }
});

// Prevent horizontal scroll
document.addEventListener('DOMContentLoaded', function() {
    // Disable horizontal scroll
    document.body.style.overflowX = 'hidden';
    
    // Fix viewport width
    const viewport = document.querySelector('meta[name="viewport"]');
    if (viewport) {
        viewport.setAttribute('content', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no');
    }
});

// Notification functions
function toggleNotifikasis() {
    // Toggle notification panel for user
    console.log('Toggle user notifications');
    // Add your notification logic here
}

function toggleAdminNotifikasis() {
    // Toggle notification panel for admin
    console.log('Toggle admin notifications');
    // Add your notification logic here
}
</script>
