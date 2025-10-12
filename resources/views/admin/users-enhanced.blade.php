<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kelola Pengguna - Sistem Pemesanan Ruang Meeting</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen">
    <!-- Mobile Sidebar -->
    @include('components.mobile-sidebar', [
        'userRole' => 'admin',
        'pageTitle' => 'Kelola Pengguna'
    ])

    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Kelola Pengguna</h1>
                <p class="text-white/80">Kelola pengguna sistem dan role akses</p>
            </div>
            <div class="flex space-x-4">
                <button onclick="refreshUsers()" class="px-4 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-all duration-300">
                    <i class="fas fa-sync-alt mr-2"></i>Refresh
                </button>
                <button onclick="exportUsers()" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-all duration-300">
                    <i class="fas fa-download mr-2"></i>Export CSV
                </button>
            </div>
        </div>

        <!-- Users Table -->
        <div class="glass-effect rounded-2xl p-6 shadow-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-white">
                    <thead>
                        <tr class="border-b border-white/20">
                            <th class="text-left py-3 px-4">Nama</th>
                            <th class="text-left py-3 px-4">Email</th>
                            <th class="text-left py-3 px-4">Role</th>
                            <th class="text-left py-3 px-4">Google Login</th>
                            <th class="text-left py-3 px-4">Terakhir Login</th>
                            <th class="text-left py-3 px-4">Bergabung</th>
                            <th class="text-left py-3 px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                        <tr>
                            <td colspan="7" class="text-center py-8 text-white/60">
                                <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                                <p>Memuat data pengguna...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Role Change Modal -->
    <div id="roleChangeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-800">Ubah Role Pengguna</h3>
                    <button onclick="closeModal('roleChangeModal')" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div id="roleChangeContent">
                    <!-- Content will be loaded here -->
                </div>
                <div class="flex justify-end space-x-4 mt-6">
                    <button onclick="closeModal('roleChangeModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Batal
                    </button>
                    <button onclick="confirmRoleChange()" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        Ubah Role
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div id="messageContainer" class="fixed top-4 right-4 z-50"></div>

    <script>
        let users = [];
        let currentUserId = null;
        let currentUserRole = null;

        // Load users on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadUsers();
        });

        // Load users from API
        async function loadUsers() {
            console.log('Loading users...');
            console.log('Request URL:', '/admin/users/api');
            console.log('Request method:', 'GET');
            console.log('Request headers:', {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            });
            try {
                // Add cache-busting parameter
                const timestamp = new Date().getTime();
                const response = await fetch(`/admin/users/api?t=${timestamp}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Cache-Control': 'no-cache',
                        'Pragma': 'no-cache'
                    }
                });

                console.log('Load users response status:', response.status);
                console.log('Load users response OK:', response.ok);

                const data = await response.json();
                console.log('Load users response data:', data);

                if (data.success) {
                    users = data.users;
                    console.log('Users loaded:', users);
                    console.log('Users count:', users.length);
                    users.forEach((user, index) => {
                        console.log(`User ${index}: ID=${user.id}, Name=${user.name}, Role=${user.role}`);
                    });
                    renderUsersTable();
                    showMessage(`Data berhasil dimuat: ${users.length} pengguna ditemukan`, 'success');
                } else {
                    showMessage('Gagal memuat data pengguna: ' + data.error, 'error');
                }
            } catch (error) {
                console.error('Error loading users:', error);
                showMessage('Terjadi kesalahan saat memuat data pengguna', 'error');
            }
        }

        // Render users table
        function renderUsersTable() {
            const tbody = document.getElementById('usersTableBody');
            
            if (users.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center py-8 text-white/60">
                            <i class="fas fa-users text-2xl mb-2"></i>
                            <p>Tidak ada pengguna ditemukan</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = users.map(user => `
                <tr class="border-b border-white/10 hover:bg-white/5">
                    <td class="py-3 px-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-user text-white/80"></i>
                            </div>
                            <div>
                                <div class="font-medium">${user.name}</div>
                                <div class="text-sm text-white/60">@${user.username}</div>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 px-4">${user.email}</td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-1 rounded-full text-xs font-medium ${
                            user.role === 'admin' 
                                ? 'bg-red-500/20 text-red-300' 
                                : 'bg-blue-500/20 text-blue-300'
                        }">
                            ${user.role === 'admin' ? 'Admin' : 'User'}
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-1 rounded-full text-xs font-medium ${
                            user.google_id === 'Yes' 
                                ? 'bg-green-500/20 text-green-300' 
                                : 'bg-gray-500/20 text-gray-300'
                        }">
                            <i class="fas fa-${user.google_id === 'Yes' ? 'check' : 'times'} mr-1"></i>
                            ${user.google_id}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-sm text-white/80">${user.last_login_at}</td>
                    <td class="py-3 px-4 text-sm text-white/80">${user.created_at}</td>
                    <td class="py-3 px-4">
                        <div class="flex space-x-2">
                            <button onclick="changeUserRole(${user.id}, '${user.role}', '${user.name}')" 
                                    class="px-3 py-1 bg-yellow-500/20 text-yellow-300 rounded-lg hover:bg-yellow-500/30 text-sm">
                                <i class="fas fa-user-edit mr-1"></i>Role
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        // Change user role
        function changeUserRole(userId, currentRole, userName) {
            console.log('changeUserRole called with:', {userId, currentRole, userName});
            currentUserId = userId;
            currentUserRole = currentRole;
            
            const newRole = currentRole === 'admin' ? 'user' : 'admin';
            console.log('New role will be:', newRole);
            
            document.getElementById('roleChangeContent').innerHTML = `
                <div class="mb-4">
                    <p class="text-gray-700 mb-2">Ubah role untuk pengguna:</p>
                    <p class="font-semibold text-gray-800">${userName}</p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role saat ini:</label>
                    <span class="px-3 py-1 rounded-full text-sm font-medium ${
                        currentRole === 'admin' 
                            ? 'bg-red-500/20 text-red-700' 
                            : 'bg-blue-500/20 text-blue-700'
                    }">
                        ${currentRole === 'admin' ? 'Admin' : 'User'}
                    </span>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role baru:</label>
                    <span class="px-3 py-1 rounded-full text-sm font-medium ${
                        newRole === 'admin' 
                            ? 'bg-red-500/20 text-red-700' 
                            : 'bg-blue-500/20 text-blue-700'
                    }">
                        ${newRole === 'admin' ? 'Admin' : 'User'}
                    </span>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <div class="flex">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-2 mt-0.5"></i>
                        <div class="text-sm text-yellow-800">
                            <p class="font-medium">Perhatian:</p>
                            <p>Perubahan role akan mempengaruhi akses pengguna ke sistem. Pastikan perubahan ini sesuai dengan kebutuhan.</p>
                        </div>
                    </div>
                </div>
            `;
            
            openModal('roleChangeModal');
        }

        // Confirm role change
        async function confirmRoleChange() {
            if (!currentUserId) {
                console.error('No user ID selected');
                showMessage('Tidak ada pengguna yang dipilih', 'error');
                return;
            }
            
            const newRole = currentUserRole === 'admin' ? 'user' : 'admin';
            
            console.log('Attempting to change role:', {
                userId: currentUserId,
                currentRole: currentUserRole,
                newRole: newRole
            });
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                console.log('CSRF Token:', csrfToken);
                
                const response = await fetch(`/admin/users/${currentUserId}/role`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        role: newRole
                    })
                });

                console.log('Response status:', response.status);
                console.log('Response OK:', response.ok);

                const data = await response.json();
                console.log('Response data:', data);

                if (data.success) {
                    showMessage(data.message, 'success');
                    closeModal('roleChangeModal');
                    
                    // Check if redirect is required (current user role changed to admin)
                    if (data.redirect_required) {
                        showMessage('Role Anda telah diubah ke Admin. Anda akan diarahkan ke dashboard admin.', 'info');
                        setTimeout(() => {
                            window.location.href = '/admin/dashboard';
                        }, 2000);
                    } else {
                        loadUsers(); // Reload users
                    }
                } else {
                    showMessage(data.error || 'Gagal mengubah role pengguna', 'error');
                }
            } catch (error) {
                console.error('Error changing user role:', error);
                showMessage('Terjadi kesalahan saat mengubah role pengguna: ' + error.message, 'error');
            }
        }

        // Refresh users
        function refreshUsers() {
            console.log('Refreshing users...');
            
            // Disable refresh button and show loading
            const refreshBtn = document.querySelector('button[onclick="refreshUsers()"]');
            const originalText = refreshBtn.innerHTML;
            refreshBtn.disabled = true;
            refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memuat...';
            
            showMessage('Memuat data terbaru dari database...', 'info');
            
            // Call loadUsers with callback to re-enable button
            loadUsers().finally(() => {
                refreshBtn.disabled = false;
                refreshBtn.innerHTML = originalText;
            });
        }

        // Export users to CSV
        function exportUsers() {
            if (users.length === 0) {
                showMessage('Tidak ada data untuk diekspor', 'warning');
                return;
            }

            let csv = 'ID,Username,Nama,Email,Role,Google Login,Terakhir Login,Bergabung\n';
            
            users.forEach(user => {
                csv += `"${user.id}","${user.username}","${user.name}","${user.email}","${user.role}","${user.google_id}","${user.last_login_at}","${user.created_at}"\n`;
            });
            
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `users-export-${new Date().toISOString().split('T')[0]}.csv`;
            a.click();
            window.URL.revokeObjectURL(url);
            
            showMessage('Data pengguna berhasil diekspor', 'success');
        }

        // Modal functions
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Show message
        function showMessage(message, type) {
            const container = document.getElementById('messageContainer');
            const messageDiv = document.createElement('div');
            
            const bgColor = type === 'success' ? 'bg-green-500' : 
                           type === 'error' ? 'bg-red-500' : 
                           type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500';
            
            const icon = type === 'success' ? 'fa-check-circle' : 
                        type === 'error' ? 'fa-exclamation-circle' : 
                        type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';
            
            messageDiv.className = `${bgColor} text-white px-6 py-3 rounded-lg shadow-lg mb-2 flex items-center`;
            messageDiv.innerHTML = `
                <i class="fas ${icon} mr-2"></i>
                ${message}
            `;
            
            container.appendChild(messageDiv);
            
            // Auto remove after 1 second
            setTimeout(() => {
                if (messageDiv.parentNode) {
                    messageDiv.parentNode.removeChild(messageDiv);
                }
            }, 1000);
        }
    </script>
</body>
</html>
