<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna - Sistem Pemesanan Ruang Meeting</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/dropdown-fix.css') }}" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Fix dropdown styling */
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
    <!-- Desktop Navigation -->
    <nav class="glass-effect shadow-lg desktop-nav">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar-alt text-2xl text-white"></i>
                    </div>
                    <div class="ml-4">
                        <h1 class="text-xl font-bold text-white">Manage Users</h1>
                        <p class="text-white/80 text-sm">Admin Panel</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="hidden md:flex space-x-6">
                        <a href="{{ route('admin.dashboard') }}" class="text-white/80 hover:text-white transition-colors">
                            <i class="fas fa-tachometer-alt mr-1"></i>Dashboard
                        </a>
                        <a href="{{ route('admin.users') }}" class="text-white hover:text-white/80 transition-colors">
                            <i class="fas fa-users mr-1"></i>Pengguna
                        </a>
                        <a href="{{ route('admin.rooms') }}" class="text-white/80 hover:text-white transition-colors">
                            <i class="fas fa-door-open mr-1"></i>Ruang
                        </a>
                        <a href="{{ route('admin.bookings') }}" class="text-white/80 hover:text-white transition-colors">
                            <i class="fas fa-calendar-check mr-1"></i>Pemesanan
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
                            Keluar
                        </a>
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
        'pageTitle' => 'Panel Admin'
    ])

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="glass-effect rounded-2xl p-6 mb-8 shadow-2xl">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white mb-2">Manage Users</h2>
                    <p class="text-white/80">View and manage all system users</p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-300 flex items-center">
                        <i class="fas fa-plus mr-2"></i>Add User
                    </a>
                    <button id="export-btn" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors duration-300 flex items-center">
                        <i class="fas fa-download mr-2"></i>Export
                    </button>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="glass-effect rounded-2xl p-6 shadow-2xl">
            @if($users->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-white">
                        <thead>
                            <tr class="border-b border-white/20">
                                <th class="text-left py-3 px-4 font-semibold">ID</th>
                                <th class="text-left py-3 px-4 font-semibold">User</th>
                                <th class="text-left py-3 px-4 font-semibold">Email</th>
                                <th class="text-left py-3 px-4 font-semibold">Department</th>
                                <th class="text-left py-3 px-4 font-semibold">Last Login</th>
                                <th class="text-left py-3 px-4 font-semibold">Joined</th>
                                <th class="text-left py-3 px-4 font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr class="border-b border-white/10 hover:bg-white/5 transition-colors">
                                <td class="py-3 px-4">#{{ $user->id }}</td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                        <div>
                                            <p class="text-white font-medium">{{ $user->full_name ?? $user->name }}</p>
                                            <p class="text-white/60 text-sm">@{{ $user->username ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <p class="text-white">{{ $user->email }}</p>
                                    @if($user->phone)
                                    <p class="text-white/60 text-sm">{{ $user->phone }}</p>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <div>
                                        <span class="text-white">{{ $user->department ?? 'N/A' }}</span>
                                        @if($user->role)
                                        <div class="mt-1">
                                            <span class="inline-block px-2 py-1 rounded-full text-xs font-medium {{ $user->role === 'admin' ? 'bg-red-500 text-white' : 'bg-blue-500 text-white' }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    @if($user->last_login_at)
                                        <p class="text-white">{{ $user->last_login_at->format('M d, Y') }}</p>
                                        <p class="text-white/60 text-sm">{{ $user->last_login_at->format('H:i') }}</p>
                                    @else
                                        <span class="text-white/60">Never</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <p class="text-white">{{ $user->created_at->format('M d, Y') }}</p>
                                    <p class="text-white/60 text-sm">{{ $user->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex space-x-2">
                                        <button onclick="viewUser({{ $user->id }})" class="text-blue-400 hover:text-blue-300 transition-colors" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="editUser({{ $user->id }})" class="text-yellow-400 hover:text-yellow-300 transition-colors" title="Edit User">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @if($user->role !== 'admin')
                                        <button onclick="deleteUser({{ $user->id }})" class="text-red-400 hover:text-red-300 transition-colors" title="Delete User">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="flex justify-between items-center mt-8">
                    <div class="text-white/80 text-sm">
                        Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                    </div>
                    <div class="flex space-x-2">
                        @if($users->previousPageUrl())
                        <a href="{{ $users->previousPageUrl() }}" class="px-3 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-colors">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                        @endif
                        
                        @for($i = 1; $i <= $users->lastPage(); $i++)
                        <a href="{{ $users->url($i) }}" 
                           class="px-3 py-2 rounded-lg transition-colors {{ $users->currentPage() == $i ? 'bg-white text-indigo-600 font-semibold' : 'bg-white/20 text-white hover:bg-white/30' }}">
                            {{ $i }}
                        </a>
                        @endfor
                        
                        @if($users->nextPageUrl())
                        <a href="{{ $users->nextPageUrl() }}" class="px-3 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-colors">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        @endif
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-users text-white/40 text-6xl mb-4"></i>
                    <h3 class="text-xl font-bold text-white mb-2">No Users Found</h3>
                    <p class="text-white/60">There are no users in the system yet.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- User Detail Modal -->
    <div id="userDetailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-800">User Details</h3>
                    <button onclick="closeModal('userDetailModal')" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div id="userDetailContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- User Edit Modal -->
    <div id="userEditModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-800">Edit User</h3>
                    <button onclick="closeModal('userEditModal')" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form id="userEditForm">
                    @csrf
                    <div id="userEditContent">
                        <!-- Content will be loaded here -->
                    </div>
                    <div class="flex justify-end space-x-4 mt-6">
                        <button type="button" onclick="closeModal('userEditModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                            Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- User Delete Modal -->
    <div id="userDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Delete User</h3>
                        <p class="text-gray-600">This action cannot be undone</p>
                    </div>
                </div>
                <p class="text-gray-700 mb-6">Are you sure you want to delete this user? All their bookings will also be deleted.</p>
                <div class="flex justify-end space-x-4">
                    <button onclick="closeModal('userDeleteModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Cancel
                    </button>
                    <button onclick="confirmDeleteUser()" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                        Delete User
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

    <!-- Error Message -->
    @if (session('error'))
        <div id="error-message" class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <script>
        let currentUserId = null;

        // Modal functions
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // User actions
        function viewUser(userId) {
            currentUserId = userId;
            const user = @json($users->items()).find(u => u.id == userId);
            
            if (user) {
                document.getElementById('userDetailContent').innerHTML = `
                    <div class="space-y-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-white text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-gray-800">${user.full_name}</h4>
                                <p class="text-gray-600">@${user.username}</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <p class="text-gray-900">${user.email}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <p class="text-gray-900">${user.phone || 'Not provided'}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                                <p class="text-gray-900">${user.department || 'Not specified'}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                <span class="inline-block px-3 py-1 rounded-full text-sm font-medium ${user.role === 'admin' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800'}">
                                    ${user.role ? user.role.charAt(0).toUpperCase() + user.role.slice(1) : 'User'}
                                </span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Last Login</label>
                                <p class="text-gray-900">${user.last_login_at ? new Date(user.last_login_at).toLocaleString() : 'Never'}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Joined</label>
                                <p class="text-gray-900">${new Date(user.created_at).toLocaleDateString()}</p>
                            </div>
                        </div>
                    </div>
                `;
                openModal('userDetailModal');
            }
        }

        function editUser(userId) {
            currentUserId = userId;
            const user = @json($users->items()).find(u => u.id == userId);
            
            if (user) {
                document.getElementById('userEditContent').innerHTML = `
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                            <input type="text" name="full_name" value="${user.full_name || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" value="${user.email || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                            <input type="text" name="phone" value="${user.phone || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                            <input type="text" name="department" value="${user.department || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                            <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Select role</option>
                                <option value="user" ${user.role === 'user' ? 'selected' : ''}>User</option>
                                <option value="admin" ${user.role === 'admin' ? 'selected' : ''}>Admin</option>
                            </select>
                        </div>
                    </div>
                `;
                openModal('userEditModal');
            }
        }

        function deleteUser(userId) {
            currentUserId = userId;
            openModal('userDeleteModal');
        }

        function confirmDeleteUser() {
            if (currentUserId) {
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/users/${currentUserId}/delete`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Export functionality
            const exportBtn = document.getElementById('export-btn');
            if (exportBtn) {
                exportBtn.addEventListener('click', function() {
                    const users = @json($users->items());
                    let csv = 'ID,Username,Full Name,Email,Phone,Department,Role,Last Login,Joined\n';
                    
                    users.forEach(user => {
                        csv += `"${user.id}","${user.username}","${user.full_name}","${user.email}","${user.phone || ''}","${user.department || ''}","${user.role}","${user.last_login_at || ''}","${user.created_at}"\n`;
                    });
                    
                    const blob = new Blob([csv], { type: 'text/csv' });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'users-export.csv';
                    a.click();
                    window.URL.revokeObjectURL(url);
                });
            }

            // Edit form submission
            const editForm = document.getElementById('userEditForm');
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const userId = currentUserId;
                    
                    fetch(`/admin/users/${userId}`, {
                        method: 'PUT',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (response.ok) {
                            return response.json();
                        } else {
                            throw new Error('Network response was not ok');
                        }
                    })
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            closeModal('userEditModal');
                            location.reload();
                        } else {
                            alert(data.message || 'Error updating user');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error updating user: ' + error.message);
                    });
                });
            }

            // Close modal on outside click
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('fixed')) {
                    const modals = ['userDetailModal', 'userEditModal', 'userDeleteModal'];
                    modals.forEach(modalId => {
                        if (!document.getElementById(modalId).classList.contains('hidden')) {
                            closeModal(modalId);
                        }
                    });
                }
            });
        });

        // Auto-hide success and error messages
        setTimeout(() => {
            const successMessage = document.getElementById('success-message');
            const errorMessage = document.getElementById('error-message');
            
            if (successMessage) {
                successMessage.style.transition = 'opacity 0.5s';
                successMessage.style.opacity = '0';
                setTimeout(() => successMessage.remove(), 500);
            }
            
            if (errorMessage) {
                errorMessage.style.transition = 'opacity 0.5s';
                errorMessage.style.opacity = '0';
                setTimeout(() => errorMessage.remove(), 500);
            }
        }, 5000);
    </script>

    <!-- WhatsApp Floating Button -->
    @include('components.whatsapp-float')
</body>
</html>
