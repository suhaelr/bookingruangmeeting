<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profile - Meeting Room Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/dropdown-fix.css') }}" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Fix dropdown styling for profile page */
        select {
            background-color: rgba(255, 255, 255, 0.2) !important;
            color: white !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
        }
        
        select option {
            background-color: #1a202c !important;
            color: white !important;
            padding: 8px 12px !important;
        }
        
        select option:hover {
            background-color: #2d3748 !important;
            color: white !important;
        }
        
        select option:checked {
            background-color: #3182ce !important;
            color: white !important;
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
                        <a href="{{ route('logout') }}" 
                           class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors duration-300 flex items-center">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Keluar
                        </a>
                    </div>
                </div>
            </div>
        </nav>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Profile Card -->
            <div class="lg:col-span-1">
                <div class="glass-effect rounded-2xl p-6 shadow-2xl">
                    <div class="text-center">
                        <div class="w-24 h-24 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user text-white text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">{{ $user['full_name'] }}</h3>
                        <p class="text-white/80 mb-1">{{ $user['email'] }}</p>
                        <p class="text-white/60 text-sm">{{ $user['department'] }}</p>
                        <span class="inline-block px-3 py-1 bg-blue-500/20 text-blue-300 text-xs rounded-full mt-2">
                            {{ ucfirst($user['role']) }}
                        </span>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="glass-effect rounded-2xl p-6 shadow-2xl mt-6">
                    <h4 class="text-lg font-bold text-white mb-4">Quick Stats</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-white/80 text-sm">Member Since</span>
                            <span class="text-white text-sm">Jan 2024</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-white/80 text-sm">Last Login</span>
                            <span class="text-white text-sm">{{ now()->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-white/80 text-sm">Total Bookings</span>
                            <span class="text-white text-sm">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Form -->
            <div class="lg:col-span-2">
                <div class="glass-effect rounded-2xl p-8 shadow-2xl">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-white mb-2">Edit Profile</h2>
                        <p class="text-white/80">Perbarui your personal information</p>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                {{ session('success') }}
                            </div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                <div>
                                    <strong>Please correct the following errors:</strong>
                                    <ul class="mt-1 list-disc list-inside">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('user.profile.update') }}" class="space-y-6">
                        @csrf
                        
                        <!-- Full Nama -->
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-white mb-2">
                                <i class="fas fa-user mr-2"></i>Full Nama *
                            </label>
                            <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $user['full_name']) }}" required
                                   class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all duration-300"
                                   placeholder="Enter your full name">
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-white mb-2">
                                <i class="fas fa-envelope mr-2"></i>Email Address *
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user['email']) }}" required
                                   class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all duration-300"
                                   placeholder="Enter your email address">
                        </div>

                        <!-- Telepon -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-white mb-2">
                                <i class="fas fa-phone mr-2"></i>Telepon Number
                            </label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone', $user['phone'] ?? '') }}"
                                   class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all duration-300"
                                   placeholder="Enter your phone number">
                        </div>

                        <!-- Departemen -->
                        <div>
                            <label for="department" class="block text-sm font-medium text-white mb-2">
                                <i class="fas fa-building mr-2"></i>Departemen
                            </label>
                            <select id="department" name="department"
                                    class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all duration-300">
                                <option value="">Select Departemen</option>
                                <option value="IT" {{ old('department', $user['department'] ?? '') == 'IT' ? 'selected' : '' }}>IT</option>
                                <option value="HR" {{ old('department', $user['department'] ?? '') == 'HR' ? 'selected' : '' }}>HR</option>
                                <option value="Finance" {{ old('department', $user['department'] ?? '') == 'Finance' ? 'selected' : '' }}>Finance</option>
                                <option value="Marketing" {{ old('department', $user['department'] ?? '') == 'Marketing' ? 'selected' : '' }}>Marketing</option>
                                <option value="Sales" {{ old('department', $user['department'] ?? '') == 'Sales' ? 'selected' : '' }}>Sales</option>
                                <option value="Operations" {{ old('department', $user['department'] ?? '') == 'Operations' ? 'selected' : '' }}>Operations</option>
                                <option value="General" {{ old('department', $user['department'] ?? '') == 'General' ? 'selected' : '' }}>General</option>
                            </select>
                        </div>

                        <!-- Kirim Button -->
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('user.dashboard') }}" 
                               class="px-6 py-3 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-colors duration-300">
                                Batal
                            </a>
                            <button type="submit" 
                                    class="px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-300 flex items-center">
                                <i class="fas fa-save mr-2"></i>
                                Perbarui Profile
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>


    <script>
        // Auto-hide success message
        setTimeout(() => {
            const successMessage = document.querySelector('.bg-green-100');
            if (successMessage) {
                successMessage.style.transition = 'opacity 0.5s';
                successMessage.style.opacity = '0';
                setTimeout(() => successMessage.remove(), 500);
            }
        }, 5000);

        // Form validation
        document.querySelector('form[action="{{ route("user.profile.update") }}"]').addEventListener('submit', function(e) {
            const fullNama = document.getElementById('full_name').value.trim();
            const email = document.getElementById('email').value.trim();
            
            if (!fullNama) {
                e.preventDefault();
                alert('Please enter your full name');
                return;
            }
            
            if (!email || !email.includes('@')) {
                e.preventDefault();
                alert('Please enter a valid email address');
                return;
            }
        });
    </script>

    <!-- Mobile Sidebar -->
    @include('components.mobile-sidebar', [
        'userRole' => 'user',
        'userName' => session('user_data.full_name'),
        'userEmail' => session('user_data.email'),
        'pageTitle' => 'Profil'
    ])

    <!-- WhatsApp Floating Button -->
    @include('components.whatsapp-float')
</body>
</html>
