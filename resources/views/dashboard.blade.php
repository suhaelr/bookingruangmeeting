<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Panel</title>
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
                        <i class="fas fa-shield-alt text-2xl text-white"></i>
                    </div>
                    <div class="ml-4">
                        <h1 class="text-xl font-bold text-white">Admin Dashboard</h1>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-white/80 text-sm">
                        <i class="fas fa-user mr-1"></i>
                        Selamat Datang, {{ session('admin_username') }}
                    </span>
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
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Selamat Datang Card -->
        <div class="glass-effect rounded-2xl p-6 mb-8 shadow-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-white mb-2">Selamat Datang di Dashboard</h2>
                    <p class="text-white/80">Kelola data dan monitor sistem Anda</p>
                </div>
                <div class="hidden md:block">
                    <div class="flex space-x-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-white">1,234</div>
                            <div class="text-white/60 text-sm">Total Users</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-white">567</div>
                            <div class="text-white/60 text-sm">Active Sessions</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-white">89</div>
                            <div class="text-white/60 text-sm">New Today</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="glass-effect rounded-2xl p-6 shadow-2xl">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-white">Data Management</h3>
                <div class="flex space-x-2">
                    <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors duration-300 flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        Add New
                    </button>
                    <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors duration-300 flex items-center">
                        <i class="fas fa-download mr-2"></i>
                        Export
                    </button>
                </div>
            </div>

            <!-- Cari and Filter -->
            <div class="mb-6 flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <input 
                            type="text" 
                            placeholder="Cari data..." 
                            class="w-full px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent"
                        >
                        <i class="fas fa-search absolute right-3 top-1/2 transform -translate-y-1/2 text-white/60"></i>
                    </div>
                </div>
                <select class="px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-white/50">
                    <option class="bg-gray-800">All Categories</option>
                    <option class="bg-gray-800">Category 1</option>
                    <option class="bg-gray-800">Category 2</option>
                </select>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="w-full text-white">
                    <thead>
                        <tr class="border-b border-white/20">
                            <th class="text-left py-3 px-4 font-semibold">ID</th>
                            <th class="text-left py-3 px-4 font-semibold">Nama</th>
                            <th class="text-left py-3 px-4 font-semibold">Email</th>
                            <th class="text-left py-3 px-4 font-semibold">Status</th>
                            <th class="text-left py-3 px-4 font-semibold">Created</th>
                            <th class="text-left py-3 px-4 font-semibold">Aksis</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $item)
                        <tr class="border-b border-white/10 hover:bg-white/5 transition-colors">
                            <td class="py-3 px-4">{{ $item['id'] }}</td>
                            <td class="py-3 px-4 font-medium">{{ $item['name'] }}</td>
                            <td class="py-3 px-4">{{ $item['email'] }}</td>
                            <td class="py-3 px-4">
                                @php
                                    $statusWarnas = [
                                        'Active' => 'bg-green-500',
                                        'Pending' => 'bg-yellow-500',
                                        'Inactive' => 'bg-red-500',
                                        'New' => 'bg-blue-500'
                                    ];
                                    $color = $statusWarnas[$item['status']] ?? 'bg-gray-500';
                                @endphp
                                <span class="{{ $color }} text-white px-2 py-1 rounded-full text-xs">{{ $item['status'] }}</span>
                            </td>
                            <td class="py-3 px-4">{{ $item['created'] }}</td>
                            <td class="py-3 px-4">
                                <div class="flex space-x-2">
                                    <button class="text-blue-400 hover:text-blue-300 transition-colors">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-red-400 hover:text-red-300 transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex justify-between items-center mt-6">
                <div class="text-white/80 text-sm">
                    Showing 1 to {{ count($data) }} of {{ count($data) }} entries
                </div>
                <div class="flex space-x-2">
                    <button class="px-3 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-colors">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="px-3 py-2 bg-white text-indigo-600 rounded-lg font-semibold">1</button>
                    <button class="px-3 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-colors">2</button>
                    <button class="px-3 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-colors">3</button>
                    <button class="px-3 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-colors">
                        <i class="fas fa-chevron-right"></i>
                    </button>
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

    <!-- WhatsApp Floating Button -->
    @include('components.whatsapp-float')
</body>
</html>
