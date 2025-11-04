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

    <!-- Mobile Sidebar -->
    @include('components.mobile-sidebar', [
        'userRole' => 'admin',
        'pageTitle' => 'Kelola Pengguna'
    ])

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="glass-effect rounded-2xl p-6 mb-8 shadow-2xl">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-white mb-2">Kelola Pengguna</h2>
                    <p class="text-white/80">Lihat dan kelola semua akun pengguna</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-4">
                    <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-300 flex items-center justify-center">
                        <i class="fas fa-plus mr-2"></i>Tambah Pengguna
                    </a>
                    <button onclick="exportUsers()" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors duration-300 flex items-center justify-center">
                        <i class="fas fa-download mr-2"></i>Export
                    </button>
                </div>
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
                            <th class="text-left py-3 px-4">Unit Kerja</th>
                            <th class="text-left py-3 px-4">Role</th>
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

                const data = await response.json();
                if (data.success) {
                    users = data.users;
                    renderUsersTable();
                    showMessage(`Data berhasil dimuat: ${users.length} pengguna ditemukan`, 'success');
                } else {
                    showMessage('Gagal memuat data pengguna: ' + data.error, 'error');
                }
            } catch (error) {
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
                        <span class="text-white/80">${user.unit_kerja || 'N/A'}</span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-1 rounded-full text-xs font-medium ${
                            user.role === 'admin' 
                                ? 'bg-red-500/20 text-red-300' 
                                : 'bg-blue-500/20 text-blue-300'
                        }">
                            ${user.role === 'admin' ? 'Admin' : 'User'}
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
                            ${!(user.username === 'admin' && user.email === 'admin@pusdatinbgn.web.id') ? `
                            <button onclick="deleteUser(${user.id}, '${user.name}')" 
                                    class="px-3 py-1 bg-red-500/20 text-red-300 rounded-lg hover:bg-red-500/30 text-sm">
                                <i class="fas fa-trash mr-1"></i>Hapus
                            </button>
                            ` : ''}
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        // Change user role
        function changeUserRole(userId, currentRole, userName) {
            currentUserId = userId;
            currentUserRole = currentRole;
            
            const newRole = currentRole === 'admin' ? 'user' : 'admin';
            
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
                showMessage('Tidak ada pengguna yang dipilih', 'error');
                return;
            }
            
            const newRole = currentUserRole === 'admin' ? 'user' : 'admin';
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
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
                const data = await response.json();
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
                showMessage('Terjadi kesalahan saat mengubah role pengguna: ' + error.message, 'error');
            }
        }

        // Delete user
        function deleteUser(userId, userName) {
            if (confirm(`Apakah Anda yakin ingin menghapus pengguna "${userName}"? Semua data pengguna (bookings, notifications) akan dihapus secara permanen.`)) {
                // Show loading message
                showMessage('Menghapus pengguna...', 'info');
                
                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                // Use fetch API instead of form submission
                fetch(`/admin/users/${userId}/delete`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: `_token=${csrfToken}`
                })
                .then(response => {
                    if (response.ok) {
                        showMessage('Pengguna berhasil dihapus!', 'success');
                        // Reload users after successful deletion
                        setTimeout(() => {
                            loadUsers();
                        }, 1000);
                    } else {
                        return response.text().then(text => {
                            throw new Error(`HTTP ${response.status}: ${text}`);
                        });
                    }
                })
                .catch(error => {
                    showMessage('Gagal menghapus pengguna: ' + error.message, 'error');
                });
            }
        }

        // Refresh users
        function refreshUsers() {
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

            let csv = 'ID,Username,Nama,Email,Role,Terakhir Login,Bergabung\n';
            
            users.forEach(user => {
                csv += `"${user.id}","${user.username}","${user.name}","${user.email}","${user.role}","${user.last_login_at}","${user.created_at}"\n`;
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
