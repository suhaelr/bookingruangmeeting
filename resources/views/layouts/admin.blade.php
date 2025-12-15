@extends('layouts.app')

@section('body-class', 'gradient-bg min-h-screen')

@push('styles')
<style>
    body.gradient-bg {
        background: #ffffff !important;
    }

    .glass-effect {
        background: #ffffff !important;
        border: 1px solid #e5e7eb !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
    }
</style>
@endpush

@section('content')
    <!-- Navigation -->
    <nav class="border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <button onclick="toggleMobileSidebar()" class="mobile-menu-btn mr-4">
                        <i data-feather="menu" class="text-black w-[20px] h-[20px]"></i>
                    </button>
                    <div class="flex-shrink-0 pl-[50px] ">
                        <img src="{{ asset('/logo-bgn.png') }}" alt="Logo" class="w-[40px] h-[40px]">
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    @stack('nav-actions')
                    <!-- Avatar Dropdown -->
                    <div class="relative">
                        <button id="avatarDropdownBtn" 
                                class="flex items-center focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 rounded-full transition-transform hover:scale-105">
                            @if(session('user_data.avatar'))
                                <img src="{{ asset('storage/' . session('user_data.avatar')) }}" 
                                     alt="{{ session('user_data.full_name') ?? 'Admin' }}"
                                     class="w-10 h-10 rounded-full object-cover border-2 border-purple-500">
                            @else
                                <div class="w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center border-2 border-indigo-600">
                                    <i data-feather="user" class="text-white" style="width: 18px; height: 18px;"></i>
                                </div>
                            @endif
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div id="avatarDropdown" 
                             class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                            <div class="px-4 py-3 border-b border-gray-200">
                                <p class="text-sm font-semibold text-gray-900">{{ session('user_data.full_name') ?? 'Administrator' }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ session('user_data.email') ?? 'N/A' }}</p>
                            </div>
                            <a href="{{ route('admin.profile') }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-colors duration-150 flex items-center">
                                <i data-feather="user" class="mr-2" style="width: 18px; height: 18px;"></i>
                                Profil
                            </a>
                            <a href="{{ route('logout') }}" 
                               class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-150 flex items-center">
                                <i data-feather="log-out" class="mr-2" style="width: 18px; height: 18px;"></i>
                                Keluar
                            </a>
                        </div>
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
        'pageTitle' => $pageTitle ?? 'Admin'
    ])

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if (session('success'))
            <div id="success-message" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
                <div class="flex items-center">
                    <i data-feather="check-circle" class="mr-2" style="width: 20px; height: 20px;"></i>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @yield('main-content')
    </main>

    <!-- WhatsApp Floating Button -->
    @include('components.whatsapp-float')

    @stack('modals')
@stop

@push('scripts')
<script>
    // Auto-hide success message
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            const successMessage = document.getElementById('success-message');
            if (successMessage) {
                successMessage.style.transition = 'opacity 0.3s';
                successMessage.style.opacity = '0';
                setTimeout(() => successMessage.remove(), 300);
            }
        }, 3000);

        // Avatar dropdown toggle
        const avatarBtn = document.getElementById('avatarDropdownBtn');
        const avatarDropdown = document.getElementById('avatarDropdown');

        if (avatarBtn && avatarDropdown) {
            avatarBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                avatarDropdown.classList.toggle('hidden');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!avatarBtn.contains(e.target) && !avatarDropdown.contains(e.target)) {
                    avatarDropdown.classList.add('hidden');
                }
            });

            // Close dropdown when clicking on a link inside
            const dropdownLinks = avatarDropdown.querySelectorAll('a');
            dropdownLinks.forEach(link => {
                link.addEventListener('click', function() {
                    avatarDropdown.classList.add('hidden');
                });
            });
        }
    });
</script>
@endpush

