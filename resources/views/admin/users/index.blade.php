@extends('layouts.admin')

@section('title', 'Kelola Pengguna - Sistem Pemesanan Ruang Meeting')

@php
    $pageTitle = 'Kelola Pengguna';
@endphp

@push('head')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
@endpush

@push('styles')
<style>
    body,
    body.gradient-bg {
        background: #ffffff !important;
        background-image: none !important;
        color: #000000 !important;
    }

    /* Mobile responsive table */
    @media (max-width: 768px) {
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .table-responsive table {
            min-width: 800px;
        }
        
        .table-responsive th,
        .table-responsive td {
            white-space: nowrap;
            min-width: 120px;
        }
        
        .table-responsive th:first-child,
        .table-responsive td:first-child {
            min-width: 60px;
        }
        
        .table-responsive th:last-child,
        .table-responsive td:last-child {
            min-width: 100px;
        }
    }
</style>
@endpush

@section('main-content')
    <!-- Header -->
    <div class="border border-gray-200 rounded-2xl p-6 mb-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-black mb-2">Kelola Pengguna</h2>
                <p class="text-black">Lihat dan kelola semua akun pengguna</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-4">
                <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-300 flex items-center justify-center">
                    <i data-feather="plus" class="mr-2" style="width: 18px; height: 18px;"></i>Tambah Pengguna
                </a>
                <button onclick="exportUsers()" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors duration-300 flex items-center justify-center">
                    <i data-feather="download" class="mr-2" style="width: 18px; height: 18px;"></i>Export
                </button>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="rounded-2xl p-6 border border-gray-200">
        <div class="overflow-x-auto table-responsive bg-white rounded-xl border border-gray-200">
            <table class="w-full text-black">
                <thead class="bg-gray-100">
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 font-semibold text-black">Nama</th>
                        <th class="text-left py-3 px-4 font-semibold text-black">Email</th>
                        <th class="text-left py-3 px-4 font-semibold text-black">Unit Kerja</th>
                        <th class="text-left py-3 px-4 font-semibold text-black">Role</th>
                        <th class="text-left py-3 px-4 font-semibold text-black">Terakhir Login</th>
                        <th class="text-left py-3 px-4 font-semibold text-black">Bergabung</th>
                        <th class="text-left py-3 px-4 font-semibold text-black">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white" id="usersTableBody">
                    <tr>
                        <td colspan="7" class="text-center py-8 text-black">
                            <i data-feather="loader" class="text-2xl mb-2 animate-spin" style="width: 24px; height: 24px;"></i>
                            <p>Memuat data pengguna...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('modals')
    <!-- Role Change Modal -->
    <div id="roleChangeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-full w-[700px]">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-800">Ubah Role Pengguna</h3>
                    <button onclick="closeModal('roleChangeModal')" class="text-gray-500 hover:text-gray-700">
                        <i data-feather="x" style="width: 20px; height: 20px;"></i>
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
@endpush

@push('scripts')
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
            } else {
                showMessage('Gagal memuat data pengguna: ' + data.error, 'error');
            }
        } catch (error) {
            showMessage('Terjadi kesalahan saat memuat data pengguna', 'error');
        }
    }

    // Get user initials from name
    function getUserInitials(name) {
        if (!name) return '?';
        const words = name.trim().split(/\s+/);
        if (words.length === 1) {
            return words[0].charAt(0).toUpperCase();
        }
        return (words[0].charAt(0) + words[words.length - 1].charAt(0)).toUpperCase();
    }

    // Get color for avatar based on name (for consistent coloring)
    function getAvatarColor(name) {
        const colors = [
            'bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-pink-500',
            'bg-indigo-500', 'bg-yellow-500', 'bg-red-500', 'bg-teal-500'
        ];
        let hash = 0;
        for (let i = 0; i < name.length; i++) {
            hash = name.charCodeAt(i) + ((hash << 5) - hash);
        }
        return colors[Math.abs(hash) % colors.length];
    }

    // Render users table
    function renderUsersTable() {
        const tbody = document.getElementById('usersTableBody');
        
        if (users.length === 0) {
            tbody.innerHTML = `
                <tr class="border-b border-gray-200">
                    <td colspan="7" class="text-center py-8 text-black">
                        <i data-feather="users" class="text-2xl mb-2" style="width: 24px; height: 24px;"></i>
                        <p>Tidak ada pengguna ditemukan</p>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = users.map(user => `
            <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
                <td class="py-3 px-4">
                    <div class="flex items-center">
                        <div class="w-8 h-8 ${getAvatarColor(user.name)} rounded-full flex items-center justify-center mr-3 text-white text-xs font-semibold">
                            ${getUserInitials(user.name)}
                        </div>
                        <div>
                            <div class="font-medium text-black">${user.name}</div>
                            <div class="text-sm text-black">@${user.username}</div>
                        </div>
                    </div>
                </td>
                <td class="py-3 px-4 text-black">${user.email}</td>
                <td class="py-3 px-4">
                    <span class="text-black">${user.unit_kerja || 'N/A'}</span>
                </td>
                <td class="py-3 px-4">
                    <span class="px-2 py-1 rounded-full text-xs font-medium ${
                        user.role === 'admin' 
                            ? 'bg-red-500/20 text-red-700' 
                            : 'bg-blue-500/20 text-blue-700'
                    }">
                        ${user.role === 'admin' ? 'Admin' : 'User'}
                    </span>
                </td>
                <td class="py-3 px-4 text-sm text-black">${user.last_login_at}</td>
                <td class="py-3 px-4 text-sm text-black">${user.created_at}</td>
                <td class="py-3 px-4">
                    <div class="flex space-x-2">
                        <button onclick="changeUserRole(${user.id}, '${user.role}', '${user.name}')" 
                                class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg text-sm">
                            <i data-feather="user" class="mr-1 inline" style="width: 14px; height: 14px;"></i>Role
                        </button>
                        ${!(user.username === 'admin' && user.email === 'admin@pusdatinbgn.web.id') ? `
                        <button onclick="deleteUser(${user.id}, '${user.name}')" 
                                class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm">
                            <i data-feather="trash-2" class="mr-1 inline" style="width: 14px; height: 14px;"></i>Hapus
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
                    <i data-feather="alert-triangle" class="text-yellow-600 mr-2 mt-0.5" style="width: 18px; height: 18px;"></i>
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
        if (refreshBtn) {
            const originalText = refreshBtn.innerHTML;
            refreshBtn.disabled = true;
            refreshBtn.innerHTML = '<i data-feather="loader" class="mr-2 animate-spin inline" style="width: 16px; height: 16px;"></i>Memuat...';
            
            showMessage('Memuat data terbaru dari database...', 'info');
            
            // Call loadUsers with callback to re-enable button
            loadUsers().finally(() => {
                refreshBtn.disabled = false;
                refreshBtn.innerHTML = originalText;
            });
        }
    }

    // Export users to Excel
    function exportUsers() {
        if (users.length === 0) {
            showMessage('Tidak ada data untuk diekspor', 'warning');
            return;
        }

        // Prepare data for Excel
        const data = users.map(user => ({
            'ID': user.id,
            'Username': user.username,
            'Nama': user.name,
            'Email': user.email,
            'Role': user.role,
            'Terakhir Login': user.last_login_at || '',
            'Bergabung': user.created_at || ''
        }));

        // Create workbook
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.json_to_sheet(data);

        // Set column widths
        ws['!cols'] = [
            { wch: 10 }, // ID
            { wch: 15 }, // Username
            { wch: 25 }, // Nama
            { wch: 30 }, // Email
            { wch: 15 }, // Role
            { wch: 20 }, // Terakhir Login
            { wch: 20 }  // Bergabung
        ];

        // Add worksheet to workbook
        XLSX.utils.book_append_sheet(wb, ws, 'Data Pengguna');

        // Generate Excel file
        const filename = `users-export-${new Date().toISOString().split('T')[0]}.xlsx`;
        XLSX.writeFile(wb, filename);
        
        showMessage('Data pengguna berhasil diekspor ke Excel', 'success');
    }

    // Modal functions
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
        document.body.style.overflow = 'auto';
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
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.style.transition = 'opacity 0.3s';
                messageDiv.style.opacity = '0';
                setTimeout(() => {
                    if (messageDiv.parentNode) {
                        messageDiv.parentNode.removeChild(messageDiv);
                    }
                }, 300);
            }
        }, 5000);
    }
</script>
@endpush
