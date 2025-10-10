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
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar-alt text-2xl text-white"></i>
                    </div>
                    <div class="ml-4">
                        <h1 class="text-xl font-bold text-white">User Panel</h1>
                        <p class="text-white/80 text-sm">{{ session('user_data.full_name') ?? 'Pengguna' }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('user.dashboard') }}" class="text-white/80 hover:text-white transition-colors">
                        <i class="fas fa-arrow-left mr-1"></i>Kembali ke Beranda
                    </a>
                    <div class="flex items-center space-x-2">
                        <span class="text-white/80 text-sm">
                            <i class="fas fa-user mr-1"></i>
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

                <!-- Account Pengaturan -->
                <div class="glass-effect rounded-2xl p-8 shadow-2xl mt-8">
                    <h3 class="text-xl font-bold text-white mb-4">Account Pengaturan</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-white/10 rounded-lg">
                            <div>
                                <h4 class="text-white font-medium">Change Kata Sandi</h4>
                                <p class="text-white/60 text-sm">Perbarui your account password</p>
                            </div>
                            <button onclick="openChangeKata SandiModal()" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-colors duration-300">
                                Change
                            </button>
                        </div>
                        
                        <div class="flex items-center justify-between p-4 bg-white/10 rounded-lg">
                            <div>
                                <h4 class="text-white font-medium">Notifikasi Pengaturan</h4>
                                <p class="text-white/60 text-sm">Manage your notification preferences</p>
                            </div>
                            <button onclick="openNotifikasiModal()" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors duration-300">
                                Pengaturan
                            </button>
                        </div>
                        
                        <div class="flex items-center justify-between p-4 bg-white/10 rounded-lg">
                            <div>
                                <h4 class="text-white font-medium">Unduh Data</h4>
                                <p class="text-white/60 text-sm">Unduh your booking history</p>
                            </div>
                            <button onclick="downloadUserData()" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-300">
                                Unduh
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Kata Sandi Modal -->
    <div id="changeKata SandiModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Change Kata Sandi</h3>
                    <button onclick="closeModal('changeKata SandiModal')" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form id="changeKata SandiForm">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Kata Sandi</label>
                            <input type="password" name="current_password" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">New Kata Sandi</label>
                            <input type="password" name="new_password" required minlength="6"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Kata Sandi</label>
                            <input type="password" name="new_password_confirmation" required minlength="6"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="flex justify-end space-x-4 mt-6">
                        <button type="button" onclick="closeModal('changeKata SandiModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
                            Change Kata Sandi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Notifikasi Pengaturan Modal -->
    <div id="notificationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Notifikasi Pengaturan</h3>
                    <button onclick="closeModal('notificationModal')" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form id="notificationForm">
                    @csrf
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-gray-800 font-medium">Email Notifikasis</h4>
                                <p class="text-gray-600 text-sm">Receive email notifications for booking updates</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="email_notifications" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-gray-800 font-medium">Daily Reminders</h4>
                                <p class="text-gray-600 text-sm">Get reminded about upcoming meetings every 24 hours</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="daily_reminders" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-gray-800 font-medium">Booking Confirmations</h4>
                                <p class="text-gray-600 text-sm">Notify when bookings are confirmed or cancelled</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="booking_confirmations" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-4 mt-6">
                        <button type="button" onclick="closeModal('notificationModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                            Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Modal functions
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Account settings functions
        function openChangeKata SandiModal() {
            openModal('changeKata SandiModal');
        }

        function openNotifikasiModal() {
            openModal('notificationModal');
        }

        function downloadUserData() {
            // Create CSV data
            const userData = {
                name: '{{ $user["full_name"] }}',
                email: '{{ $user["email"] }}',
                phone: '{{ $user["phone"] ?? "" }}',
                department: '{{ $user["department"] ?? "" }}',
                role: '{{ $user["role"] }}',
                member_since: 'Jan 2024',
                last_login: '{{ now()->format("M d, Y") }}'
            };

            let csv = 'Field,Value\n';
            Object.keys(userData).forEach(key => {
                csv += `${key.charAt(0).toUpperCase() + key.replace('_', ' ')},${userData[key]}\n`;
            });

            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'user-profile-data.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Change password form
            const changeKata SandiForm = document.getElementById('changeKata SandiForm');
            if (changeKata SandiForm) {
                changeKata SandiForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const newKata Sandi = formData.get('new_password');
                    const confirmKata Sandi = formData.get('new_password_confirmation');
                    
                    if (newKata Sandi !== confirmKata Sandi) {
                        alert('New passwords do not match!');
                        return;
                    }
                    
                    if (newKata Sandi.length < 6) {
                        alert('Kata Sandi must be at least 6 characters long!');
                        return;
                    }
                    
                    // Kirim form
                    fetch('{{ route("user.change-password") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Kata Sandi changed successfully!');
                            closeModal('changeKata SandiModal');
                            this.reset();
                        } else {
                            alert(data.message || 'Error changing password');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error changing password');
                    });
                });
            }

            // Notifikasi settings form
            const notificationForm = document.getElementById('notificationForm');
            if (notificationForm) {
                notificationForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    
                    fetch('{{ route("user.notification-settings") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Notifikasi settings saved successfully!');
                            closeModal('notificationModal');
                        } else {
                            alert(data.message || 'Error saving settings');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error saving settings');
                    });
                });
            }

            // Close modal on outside click
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('fixed')) {
                    const modals = ['changeKata SandiModal', 'notificationModal'];
                    modals.forEach(modalId => {
                        if (!document.getElementById(modalId).classList.contains('hidden')) {
                            closeModal(modalId);
                        }
                    });
                }
            });
        });

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

    <!-- WhatsApp Floating Button -->
    @include('components.whatsapp-float')
</body>
</html>
