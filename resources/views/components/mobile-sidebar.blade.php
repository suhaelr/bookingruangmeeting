{{-- Mobile Sidebar Component --}}
<div class="mobile-sidebar" id="mobileSidebar">
    <!-- Overlay -->
    <div class="mobile-sidebar-overlay" onclick="toggleMobileSidebar()"></div>
    
    <!-- Sidebar Content -->
    <div class="mobile-sidebar-content">
        <!-- Header -->
        <div class="mobile-sidebar-header">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('/logo-bgn.png') }}" alt="Logo" class="w-[40px] h-[40px]">
                    <div>
                        <h1 class="text-lg font-bold text-black">Sistem Pemesanan</h1>
                        <p class="text-black text-xs text-gray-500">Ruang Meeting</p>
                    </div>
                </div>
                <button onclick="toggleMobileSidebar()" class="text-black hover:text-gray-800 p-2">
                    <i data-feather="x" style="width: 20px; height: 20px;"></i>
                </button>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="mobile-sidebar-nav">
            @if(isset($userRole) && $userRole === 'admin')
                {{-- Admin Navigation --}}
                <a href="{{ route('admin.dashboard') }}" class="mobile-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i data-feather="home" style="width: 20px; height: 20px;"></i>
                    <span>Beranda</span>
                </a>
                
                <a href="{{ route('admin.users') }}" class="mobile-nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    <i data-feather="users" style="width: 20px; height: 20px;"></i>
                    <span>Pengguna</span>
                </a>
                
                <a href="{{ route('admin.rooms') }}" class="mobile-nav-item {{ request()->routeIs('admin.rooms*') ? 'active' : '' }}">
                    <i data-feather="box" style="width: 20px; height: 20px;"></i>
                    <span>Ruang Meeting</span>
                </a>
                
                <a href="{{ route('admin.bookings') }}" class="mobile-nav-item {{ request()->routeIs('admin.bookings*') ? 'active' : '' }}">
                    <i data-feather="calendar" style="width: 20px; height: 20px;"></i>
                    <span>Pemesanan</span>
                </a>
            @elseif(isset($userRole) && $userRole === 'user')
                {{-- User Navigation --}}
                <a href="{{ route('user.dashboard') }}" class="mobile-nav-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                    <i data-feather="home" style="width: 20px; height: 20px;"></i>
                    <span>Beranda</span>
                </a>
                
                <a href="{{ route('user.bookings') }}" class="mobile-nav-item {{ request()->routeIs('user.bookings') ? 'active' : '' }}">
                    <i data-feather="calendar" style="width: 20px; height: 20px;"></i>
                    <span>Pemesanan Saya</span>
                </a>
            @else
                {{-- Guest Navigation --}}
                <a href="{{ route('login') }}" class="mobile-nav-item">
                    <i data-feather="log-in" style="width: 20px; height: 20px;"></i>
                    <span>Masuk</span>
                </a>
                
                <a href="{{ route('register') }}" class="mobile-nav-item">
                    <i data-feather="user-plus" style="width: 20px; height: 20px;"></i>
                    <span>Daftar</span>
                </a>
            @endif
        </nav>

        <!-- User Info & Actions -->
        @if(isset($userRole) && in_array($userRole, ['admin', 'user']))
            <div class="mobile-sidebar-footer">
                <div class="mobile-user-info">
                    <div class="relative">
                        <div onclick="toggleUserDropdown()" class="cursor-pointer">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center flex-1 min-w-0">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <i data-feather="user" class="text-indigo-600" style="width: 20px; height: 20px;"></i>
                                    </div> 
                                    <div class="flex-1 min-w-0">
                                        <p class="text-black font-medium text-sm truncate">{{ $userName ?? 'User' }}</p>
                                        <p class="text-black text-xs truncate">{{ $userEmail ?? 'user@example.com' }}</p>
                                    </div>
                                </div>
                                <div class="ml-3 flex-shrink-0">
                                    @if(isset($userRole) && $userRole === 'admin')
                                    <button onclick="event.stopPropagation(); toggleAdminNotifikasis();" class="relative p-2 text-black hover:text-gray-600">
                                        <i data-feather="bell" style="width: 20px; height: 20px;"></i>
                                        <span id="admin-notification-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                                    </button>
                                    @elseif(isset($userRole) && $userRole === 'user')
                                    <button onclick="event.stopPropagation(); toggleNotifikasis();" class="relative p-2 text-black hover:text-gray-600">
                                        <i data-feather="bell" style="width: 20px; height: 20px;"></i>
                                        <span id="notification-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- Dropdown Menu -->
                        <div id="userDropdown" class="mobile-user-dropdown">
                            <a href="{{ $userRole === 'admin' ? route('admin.profile') : route('user.profile') }}" class="mobile-dropdown-item">
                                <i data-feather="user" style="width: 18px; height: 18px;"></i>
                                <span>Profil</span>
                            </a>
                            <a href="{{ route('logout') }}" class="mobile-dropdown-item text-red-600 hover:text-red-700 hover:bg-red-50">
                                <i data-feather="log-out" style="width: 18px; height: 18px;"></i>
                                <span>Keluar</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Mobile Menu Button -->
<button onclick="toggleMobileSidebar()" class="mobile-menu-btn" id="mobileMenuBtn">
    <i data-feather="menu" style="width: 20px; height: 20px;"></i>
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
    background: #ffffff;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    overflow-y: auto;
    display: flex;
    flex-direction: column;
}

.mobile-sidebar-header {
    padding: 1.5rem 1rem;
    border-bottom: 1px solid #e5e7eb;
    flex-shrink: 0;
}

.mobile-sidebar-header i,
.mobile-sidebar-header svg {
    stroke: currentColor;
    stroke-width: 2;
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
    justify-content: space-between;
    padding: 0.875rem 1.5rem;
    color: #000000;
    text-decoration: none;
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
    position: relative;
}

.mobile-nav-item:hover {
    background: #f3f4f6;
    color: #000000;
    border-left-color: #6366f1;
}

.mobile-nav-item.active {
    background: #eef2ff;
    color: #000000;
    border-left-color: #4f46e5;
}

.mobile-nav-item i,
.mobile-nav-item svg {
    width: 20px;
    height: 20px;
    margin-right: 0.75rem;
    text-align: center;
    stroke: currentColor;
    stroke-width: 2;
    flex-shrink: 0;
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
    background: #e5e7eb;
    margin: 0.5rem 1.5rem;
}

.mobile-sidebar-footer {
    padding: 1rem;
    border-top: 1px solid #e5e7eb;
    flex-shrink: 0;
}

.mobile-user-info {
    margin-bottom: 1rem;
}

.mobile-user-info i,
.mobile-user-info svg {
    stroke: currentColor;
    stroke-width: 2;
    flex-shrink: 0;
}

.mobile-user-dropdown {
    position: absolute;
    bottom: 100%;
    left: 0;
    right: 0;
    margin-bottom: 0.5rem;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    overflow: hidden;
    z-index: 10;
    display: none;
}

.mobile-user-dropdown.show {
    display: block !important;
}

.mobile-dropdown-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    color: #000000;
    text-decoration: none;
    transition: all 0.2s ease;
    border-bottom: 1px solid #f3f4f6;
}

.mobile-dropdown-item:last-child {
    border-bottom: none;
}

.mobile-dropdown-item:hover {
    background: #f3f4f6;
}

.mobile-dropdown-item i,
.mobile-dropdown-item svg {
    width: 18px;
    height: 18px;
    margin-right: 0.75rem;
    text-align: center;
    stroke: currentColor;
    stroke-width: 2;
    flex-shrink: 0;
}

.mobile-dropdown-item span {
    font-size: 0.9rem;
    font-weight: 500;
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
    height: 2rem;
    width: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: auto;
}

.mobile-menu-btn {
    position: fixed;
    top: 0.75rem;
    left: 1rem;
    background: #ffffff;
    color: #000000;
    border: 1px solid #e5e7eb;
    padding: 0.75rem;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    height: 2.5rem;
    width: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.mobile-menu-btn:hover {
    background: #f3f4f6;
    color: #000000;
    transform: scale(1.05);
}

.mobile-menu-btn i,
.mobile-menu-btn svg {
    width: 20px;
    height: 20px;
    stroke: currentColor;
    stroke-width: 2;
}

/* Show mobile menu button on all devices */
.mobile-menu-btn {
    display: flex !important;
}

/* Show mobile sidebar on all devices when active */
.mobile-sidebar.active {
    display: block !important;
}

/* Hide desktop navigation on mobile */
@media (max-width: 768px) {
    .desktop-nav {
        display: none !important;
    }
}

/* Desktop specific styles */
@media (min-width: 769px) {
    .mobile-sidebar {
        width: 300px;
    }
    
    .mobile-menu-btn {
        position: fixed;
        top: 0.75rem;
        left: 1rem;
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
        width: 20px;
        height: 20px;
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

// User dropdown function
function toggleUserDropdown() {
    const dropdown = document.getElementById('userDropdown');
    if (dropdown) {
        dropdown.classList.toggle('show');
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('userDropdown');
    const userInfo = document.querySelector('.mobile-user-info');
    
    if (dropdown && userInfo && !userInfo.contains(event.target)) {
        dropdown.classList.remove('show');
    }
});
</script>
