<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - Admin Panel</title>
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
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar-alt text-2xl text-white"></i>
                    </div>
                    <div class="ml-4">
                        <h1 class="text-xl font-bold text-white">Add User</h1>
                        <p class="text-white/80 text-sm">Admin Panel</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="hidden md:flex space-x-6">
                        <a href="{{ route('admin.dashboard') }}" class="text-white/80 hover:text-white transition-colors">
                            <i class="fas fa-tachometer-alt mr-1"></i>Dashboard
                        </a>
                        <a href="{{ route('admin.users') }}" class="text-white hover:text-white/80 transition-colors">
                            <i class="fas fa-users mr-1"></i>Users
                        </a>
                        <a href="{{ route('admin.rooms') }}" class="text-white/80 hover:text-white transition-colors">
                            <i class="fas fa-door-open mr-1"></i>Rooms
                        </a>
                        <a href="{{ route('admin.bookings') }}" class="text-white/80 hover:text-white transition-colors">
                            <i class="fas fa-calendar-check mr-1"></i>Bookings
                        </a>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-white/80 text-sm">
                            <i class="fas fa-user-shield mr-1"></i>
                            {{ session('user_data.full_name') }}
                        </span>
                        <a href="{{ route('logout') }}" 
                           class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors duration-300 flex items-center">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="glass-effect rounded-2xl p-6 mb-8 shadow-2xl">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white mb-2">Add New User</h2>
                    <p class="text-white/80">Create a new user account</p>
                </div>
                <a href="{{ route('admin.users') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors duration-300 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Users
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="glass-effect rounded-2xl p-6 shadow-2xl">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                
                @if ($errors->any())
                    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-white mb-2">Username *</label>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" 
                               class="w-full px-3 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                               placeholder="Enter username" required>
                    </div>

                    <!-- Full Name -->
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-white mb-2">Full Name *</label>
                        <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" 
                               class="w-full px-3 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                               placeholder="Enter full name" required>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-white mb-2">Email *</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" 
                               class="w-full px-3 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                               placeholder="Enter email address" required>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-white mb-2">Phone</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" 
                               class="w-full px-3 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                               placeholder="Enter phone number">
                    </div>

                    <!-- Department -->
                    <div>
                        <label for="department" class="block text-sm font-medium text-white mb-2">Department</label>
                        <input type="text" id="department" name="department" value="{{ old('department') }}" 
                               class="w-full px-3 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                               placeholder="Enter department">
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-white mb-2">Role *</label>
                        <select id="role" name="role" 
                                class="w-full px-3 py-2 bg-white/20 border border-white/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Select role</option>
                            <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                </div>

                <!-- Password -->
                <div class="mt-6">
                    <label for="password" class="block text-sm font-medium text-white mb-2">Password *</label>
                    <input type="password" id="password" name="password" 
                           class="w-full px-3 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="Enter password (min 8 characters)" required>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4 mt-8">
                    <a href="{{ route('admin.users') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors duration-300">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-300 flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
