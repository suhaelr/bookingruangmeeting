<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kelola Ruang - Meeting Room Booking</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
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
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="glass-effect rounded-2xl p-6 mb-8 shadow-2xl">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white mb-2">Kelola Meeting Ruang</h2>
                    <p class="text-white/80">Lihat dan kelola semua ruang meeting</p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('admin.rooms.create') }}" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-300 flex items-center">
                        <i class="fas fa-plus mr-2"></i>Tambah Ruang
                    </a>
                    <button id="export-btn" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors duration-300 flex items-center">
                        <i class="fas fa-download mr-2"></i>Export
                    </button>
                </div>
            </div>
        </div>

        <!-- Ruang Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($rooms as $room)
            <div class="glass-effect rounded-2xl p-6 shadow-2xl hover:shadow-3xl transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white">{{ $room->name }}</h3>
                    <span class="px-2 py-1 rounded-full text-xs font-medium
                        {{ $room->is_active ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                        {{ $room->is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                </div>
                
                <p class="text-white/80 text-sm mb-4">{{ $room->description }}</p>
                
                <div class="space-y-2 mb-4">
                    <div class="flex items-center justify-between">
                        <span class="text-white/60 text-sm">Kapasitas:</span>
                        <span class="text-white font-medium">{{ $room->capacity }} kursi</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-white/60 text-sm">Lokasi:</span>
                        <span class="text-white font-medium">{{ $room->location }}</span>
                    </div>
                </div>
                
                @if($room->getAmenitiesList())
                <div class="mb-4">
                    <p class="text-white/60 text-sm mb-2">Fasilitas:</p>
                    <div class="flex flex-wrap gap-1">
                        @foreach($room->getAmenitiesList() as $amenity)
                        <span class="px-2 py-1 bg-blue-500/20 text-blue-300 text-xs rounded">
                            {{ ucfirst(str_replace('_', ' ', $amenity)) }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <div class="flex items-center justify-between pt-4 border-t border-white/20">
                    <div class="text-white/60 text-sm">
                        <i class="fas fa-calendar mr-1"></i>
                        {{ $room->bookings_count ?? 0 }} pemesanan
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="viewRoom({{ $room->id }})" class="text-blue-400 hover:text-blue-300 transition-colors" title="Lihat Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button onclick="editRoom({{ $room->id }})" class="text-yellow-400 hover:text-yellow-300 transition-colors" title="Edit Ruang">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteRoom({{ $room->id }})" class="text-red-400 hover:text-red-300 transition-colors" title="Hapus Ruang">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <i class="fas fa-door-open text-white/40 text-6xl mb-4"></i>
                <h3 class="text-xl font-bold text-white mb-2">Tidak Ada Ruang</h3>
                <p class="text-white/60">Belum ada ruang meeting dalam sistem.</p>
            </div>
            @endforelse
        </div>
        
        @if($rooms->count() > 0)
        <!-- Pagination -->
        <div class="flex justify-between items-center mt-8">
            <div class="text-white/80 text-sm">
                Menampilkan {{ $rooms->firstItem() }} sampai {{ $rooms->lastItem() }} dari {{ $rooms->total() }} ruang
            </div>
            <div class="flex space-x-2">
                @if($rooms->previousPageUrl())
                <a href="{{ $rooms->previousPageUrl() }}" class="px-3 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-colors">
                    <i class="fas fa-chevron-left"></i>
                </a>
                @endif
                
                @for($i = 1; $i <= $rooms->lastPage(); $i++)
                <a href="{{ $rooms->url($i) }}" 
                   class="px-3 py-2 rounded-lg transition-colors {{ $rooms->currentPage() == $i ? 'bg-white text-indigo-600 font-semibold' : 'bg-white/20 text-white hover:bg-white/30' }}">
                    {{ $i }}
                </a>
                @endfor
                
                @if($rooms->nextPageUrl())
                <a href="{{ $rooms->nextPageUrl() }}" class="px-3 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-colors">
                    <i class="fas fa-chevron-right"></i>
                </a>
                @endif
            </div>
        </div>
        @endif
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

    <!-- Room Detail Modal -->
    <div id="roomDetailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-800">Detail Ruang</h3>
                    <button onclick="closeModal('roomDetailModal')" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div id="roomDetailContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Room Edit Modal -->
    <div id="roomEditModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-800">Edit Ruang</h3>
                    <button onclick="closeModal('roomEditModal')" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form id="roomEditForm">
                    @csrf
                    <div id="roomEditContent">
                        <!-- Content will be loaded here -->
                    </div>
                    <div class="flex justify-end space-x-4 mt-6">
                        <button type="button" onclick="closeModal('roomEditModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                            Perbarui Ruang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Room Hapus Modal -->
    <div id="roomHapusModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Hapus Ruang</h3>
                        <p class="text-gray-600">Tindakan ini tidak dapat dibatalkan</p>
                    </div>
                </div>
                <p class="text-gray-700 mb-6">Apakah Anda yakin ingin menghapus ruang ini? Semua pemesanan terkait juga akan dihapus.</p>
                <div class="flex justify-end space-x-4">
                    <button onclick="closeModal('roomHapusModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Batal
                    </button>
                    <button onclick="confirmHapusRoom()" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                        Hapus Ruang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentRoomId = null;

        // Modal functions
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Room actions
        function viewRoom(roomId) {
            currentRoomId = roomId;
            const room = @json($rooms->items()).find(r => r.id == roomId);
            
            if (room) {
                document.getElementById('roomDetailContent').innerHTML = `
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-800 mb-4">Informasi Dasar</h4>
                                <div class="space-y-3">
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Nama Ruang</label>
                                        <p class="text-gray-800">${room.name}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Deskripsi</label>
                                        <p class="text-gray-800">${room.description}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Kapasitas</label>
                                        <p class="text-gray-800">${room.capacity} kursi</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Lokasi</label>
                                        <p class="text-gray-800">${room.location}</p>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-800 mb-4">Status</h4>
                                <div class="space-y-3">
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Status</label>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium ${room.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                            ${room.is_active ? 'Aktif' : 'Tidak Aktif'}
                                        </span>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Total Pemesanan</label>
                                        <p class="text-gray-800">${room.bookings_count || 0} pemesanan</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        ${room.amenities && room.amenities.length > 0 ? `
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Fasilitas</h4>
                            <div class="flex flex-wrap gap-2">
                                ${room.amenities.map(amenity => `
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">
                                        ${amenity.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}
                                    </span>
                                `).join('')}
                            </div>
                        </div>
                        ` : ''}
                    </div>
                `;
                openModal('roomDetailModal');
            }
        }

        function editRoom(roomId) {
            currentRoomId = roomId;
            const room = @json($rooms->items()).find(r => r.id == roomId);
            
            if (room) {
                document.getElementById('roomEditContent').innerHTML = `
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Ruang</label>
                                <input type="text" name="name" value="${room.name}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kapasitas</label>
                                <input type="number" name="capacity" value="${room.capacity}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                            <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">${room.description}</textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi</label>
                                <input type="text" name="location" value="${room.location}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="is_active" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="1" ${room.is_active ? 'selected' : ''}>Aktif</option>
                                <option value="0" ${!room.is_active ? 'selected' : ''}>Tidak Aktif</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fasilitas (pisahkan dengan koma)</label>
                            <input type="text" name="amenities" value="${room.amenities ? room.amenities.join(', ') : ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="projector, whiteboard, wifi, ac">
                        </div>
                    </div>
                `;
                openModal('roomEditModal');
            }
        }

        function deleteRoom(roomId) {
            currentRoomId = roomId;
            openModal('roomHapusModal');
        }

        function confirmHapusRoom() {
            if (currentRoomId) {
                fetch(`/admin/rooms/${currentRoomId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        closeModal('roomHapusModal');
                        location.reload();
                    } else {
                        alert(data.message || 'Error deleting room');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting room');
                });
            }
        }

        // Debug: Log rooms data
        console.log('Ruang data:', @json($rooms->items()));
        
        // Export functionality
        document.getElementById('export-btn').addEventListener('click', function() {
            // Simple CSV export
            const rooms = @json($rooms->items());
            let csv = 'ID,Nama,Deskripsi,Kapasitas,Lokasi,Status,Fasilitas\n';
            
            rooms.forEach(room => {
                csv += `"${room.id}","${room.name}","${room.description}","${room.capacity}","${room.location}","${room.is_active ? 'Aktif' : 'Tidak Aktif'}","${room.amenities ? room.amenities.join(', ') : ''}"\n`;
            });
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'rooms-export.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        });

        // Edit form submission
        const editForm = document.getElementById('roomEditForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const roomId = currentRoomId;
                
                fetch(`/admin/rooms/${roomId}`, {
                    method: 'PUT',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(async response => {
                    let data = null;
                    try {
                        data = await response.json();
                    } catch (jsonError) {
                        // Ignore JSON parsing errors so we can surface generic message below
                    }

                    if (response.ok && data) {
                        return data;
                    }

                    const errorMessage = data?.message
                        ?? (data?.errors ? Object.values(data.errors).flat().join(', ') : null)
                        ?? `Permintaan gagal dengan status ${response.status}`;

                    throw new Error(errorMessage);
                })
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        closeModal('roomEditModal');
                        location.reload();
                    } else {
                        const validationDetails = data.errors
                            ? Object.values(data.errors).flat().join('\n')
                            : '';
                        const message = [data.message || 'Gagal mengupdate ruang', validationDetails]
                            .filter(Boolean)
                            .join('\n');
                        alert(message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal mengupdate ruang: ' + error.message);
                });
            });
        }

        // Close modal on outside click
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('fixed')) {
                const modals = ['roomDetailModal', 'roomEditModal', 'roomHapusModal'];
                modals.forEach(modalId => {
                    if (!document.getElementById(modalId).classList.contains('hidden')) {
                        closeModal(modalId);
                    }
                });
            }
        });

        // Auto-hide success message
        setTimeout(() => {
            const successMessage = document.getElementById('success-message');
            if (successMessage) {
                successMessage.style.transition = 'opacity 0.5s';
                successMessage.style.opacity = '0';
                setTimeout(() => successMessage.remove(), 500);
            }
        }, 3000);
    </script>

    <!-- Mobile Sidebar -->
    @include('components.mobile-sidebar', [
        'userRole' => 'admin',
        'userName' => session('user_data.full_name'),
        'userEmail' => session('user_data.email'),
        'pageTitle' => 'Kelola Ruang'
    ])

    <!-- WhatsApp Floating Button -->
    @include('components.whatsapp-float')
</body>
</html>
