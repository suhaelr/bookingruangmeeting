@extends('layouts.admin')

@section('title', 'Kelola Ruang - Meeting Room Booking')

@php
    $pageTitle = 'Kelola Ruang';
@endphp

@push('head')
<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<link href="{{ asset('css/dropdown-fix.css') }}" rel="stylesheet">
@endpush

@push('styles')
<style>
    /* Fix dropdown styling */
    select {
        background-color: #ffffff !important;
        color: #000000 !important;
        border: 1px solid #e5e7eb !important;
    }
    
    select option {
        background-color: #ffffff !important;
        color: #000000 !important;
        padding: 8px 12px !important;
    }
    
    select option:hover {
        background-color: #f3f4f6 !important;
        color: #000000 !important;
    }
    
    select option:checked {
        background-color: #3b82f6 !important;
        color: #ffffff !important;
    }
</style>
@endpush

@section('main-content')
    <!-- Header -->
    <div class="glass-effect rounded-2xl p-6 mb-8 shadow-2xl">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-black mb-2">Kelola Ruang Meeting</h2>
                <p class="text-black">Lihat dan kelola semua ruang meeting</p>
            </div>
            <div class="flex space-x-4">
                <button id="export-btn" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors duration-300 flex items-center">
                    <i class="fas fa-download mr-2"></i>Export
                </button>
            </div>
        </div>
        <div class="mt-4">
            <a href="{{ route('admin.rooms.create') }}" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-300 inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>Tambah Ruang
            </a>
        </div>
    </div>

    <!-- Ruang Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($rooms as $room)
        <div class="glass-effect rounded-2xl p-6 shadow-2xl hover:shadow-3xl transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-black">{{ $room->name }}</h3>
                <span class="px-2 py-1 rounded-full text-xs font-medium
                    {{ $room->is_active ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                    {{ $room->is_active ? 'Aktif' : 'Tidak Aktif' }}
                </span>
            </div>
            
            <p class="text-black text-sm mb-4">{{ $room->description }}</p>
            
            <div class="space-y-2 mb-4">
                <div class="flex items-center justify-between">
                    <span class="text-black text-sm">Kapasitas:</span>
                    <span class="text-black font-medium">{{ $room->capacity ? $room->capacity . ' kursi' : 'Tidak ditentukan' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-black text-sm">Lokasi:</span>
                    <span class="text-black font-medium">{{ $room->location }}</span>
                </div>
            </div>
            
            @if($room->getAmenitiesList())
            <div class="mb-4">
                <p class="text-black text-sm mb-2">Fasilitas:</p>
                <div class="flex flex-wrap gap-1">
                    @foreach($room->getAmenitiesList() as $amenity)
                    <span class="px-2 py-1 bg-blue-500/20 text-blue-700 text-xs rounded">
                        {{ ucfirst(str_replace('_', ' ', $amenity)) }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif
            
            <!-- Today's Bookings -->
            @php
                $todayBookings = \App\Models\Booking::where('meeting_room_id', $room->id)
                    ->whereDate('start_time', today())
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->with('user')
                    ->orderBy('start_time')
                    ->get();
            @endphp
            
            @if($todayBookings->count() > 0)
            <div class="mb-4">
                <p class="text-black text-sm mb-2">Pemesanan Hari Ini:</p>
                <div class="space-y-2">
                    @foreach($todayBookings as $booking)
                    <div class="bg-gray-100 rounded-lg p-3">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-black font-medium text-sm">{{ $booking->user->full_name }}</p>
                                <p class="text-black text-xs">{{ $booking->title }}</p>
                                @if($booking->unit_kerja)
                                    <p class="text-blue-600 text-xs mt-1">
                                        <i class="fas fa-building mr-1"></i>{{ $booking->unit_kerja }}
                                    </p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-black text-sm font-medium">
                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                </p>
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($booking->status === 'confirmed') bg-green-500/20 text-green-700
                                    @elseif($booking->status === 'pending') bg-yellow-500/20 text-yellow-700
                                    @else bg-gray-500/20 text-gray-700 @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            
            <div class="flex items-center justify-between pt-4 border-t border-gray-300">
                <div class="text-black text-sm">
                    <i class="fas fa-calendar mr-1"></i>
                    {{ $room->bookings_count ?? 0 }} pemesanan total
                    @if($todayBookings->count() > 0)
                        <span class="text-green-600">({{ $todayBookings->count() }} hari ini)</span>
                    @endif
                </div>
                <div class="flex space-x-2">
                    <button onclick="viewRoom({{ $room->id }})" class="text-blue-600 hover:text-blue-800 transition-colors" title="Lihat Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button onclick="editRoom({{ $room->id }})" class="text-yellow-600 hover:text-yellow-800 transition-colors" title="Edit Ruang">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteRoom({{ $room->id }})" class="text-red-600 hover:text-red-800 transition-colors" title="Hapus Ruang">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <i class="fas fa-door-open text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-xl font-bold text-black mb-2">Tidak Ada Ruang</h3>
            <p class="text-black">Belum ada ruang meeting dalam sistem.</p>
        </div>
        @endforelse
    </div>
    
    @if($rooms->count() > 0)
    <!-- Pagination -->
    <div class="flex justify-between items-center mt-8">
        <div class="text-black text-sm">
            Menampilkan {{ $rooms->firstItem() }} sampai {{ $rooms->lastItem() }} dari {{ $rooms->total() }} ruang
        </div>
        <div class="flex space-x-2">
            @if($rooms->previousPageUrl())
            <a href="{{ $rooms->previousPageUrl() }}" class="px-3 py-2 bg-gray-200 text-black rounded-lg hover:bg-gray-300 transition-colors">
                <i class="fas fa-chevron-left"></i>
            </a>
            @endif
            
            @for($i = 1; $i <= $rooms->lastPage(); $i++)
            <a href="{{ $rooms->url($i) }}" 
               class="px-3 py-2 rounded-lg transition-colors {{ $rooms->currentPage() == $i ? 'bg-blue-500 text-white font-semibold' : 'bg-gray-200 text-black hover:bg-gray-300' }}">
                {{ $i }}
            </a>
            @endfor
            
            @if($rooms->nextPageUrl())
            <a href="{{ $rooms->nextPageUrl() }}" class="px-3 py-2 bg-gray-200 text-black rounded-lg hover:bg-gray-300 transition-colors">
                <i class="fas fa-chevron-right"></i>
            </a>
            @endif
        </div>
    </div>
    @endif
@endsection

@push('modals')
    <!-- Room Detail Modal -->
    <div id="roomDetailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-black">Detail Ruang</h3>
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
                    <h3 class="text-2xl font-bold text-black">Edit Ruang</h3>
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
                        <h3 class="text-lg font-bold text-black">Hapus Ruang</h3>
                        <p class="text-black">Tindakan ini tidak dapat dibatalkan</p>
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
@endpush

@push('scripts')
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
                            <h4 class="text-lg font-semibold text-black mb-4">Informasi Dasar</h4>
                            <div class="space-y-3">
                                <div>
                                    <label class="text-sm font-medium text-black">Nama Ruang</label>
                                    <p class="text-black">${room.name}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-black">Deskripsi</label>
                                    <p class="text-black">${room.description}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-black">Kapasitas</label>
                                    <p class="text-black">${room.capacity} kursi</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-black">Lokasi</label>
                                    <p class="text-black">${room.location}</p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-black mb-4">Status</h4>
                            <div class="space-y-3">
                                <div>
                                    <label class="text-sm font-medium text-black">Status</label>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium ${room.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                        ${room.is_active ? 'Aktif' : 'Tidak Aktif'}
                                    </span>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-black">Total Pemesanan</label>
                                    <p class="text-black">${room.bookings_count || 0} pemesanan</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    ${room.amenities && room.amenities.length > 0 ? `
                    <div>
                        <h4 class="text-lg font-semibold text-black mb-4">Fasilitas</h4>
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

    // Export functionality
    document.getElementById('export-btn').addEventListener('click', function() {
        const rooms = @json($rooms->items());
        
        const data = rooms.map(room => ({
            'ID': room.id,
            'Nama': room.name,
            'Deskripsi': room.description || '',
            'Kapasitas': room.capacity || 0,
            'Lokasi': room.location || '',
            'Status': room.is_active ? 'Aktif' : 'Tidak Aktif',
            'Fasilitas': room.amenities ? room.amenities.join(', ') : ''
        }));

        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.json_to_sheet(data);

        ws['!cols'] = [
            { wch: 10 },
            { wch: 25 },
            { wch: 40 },
            { wch: 12 },
            { wch: 20 },
            { wch: 15 },
            { wch: 30 }
        ];

        XLSX.utils.book_append_sheet(wb, ws, 'Data Ruang');

        const filename = `rooms-export-${new Date().toISOString().split('T')[0]}.xlsx`;
        XLSX.writeFile(wb, filename);
    });

    // Edit form submission
    const editForm = document.getElementById('roomEditForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const roomId = currentRoomId;
            
            const formData = {
                name: this.querySelector('input[name="name"]').value,
                capacity: this.querySelector('input[name="capacity"]').value,
                description: this.querySelector('textarea[name="description"]').value,
                location: this.querySelector('input[name="location"]').value,
                amenities: this.querySelector('input[name="amenities"]').value
            };

            const isActiveField = this.querySelector('select[name="is_active"]');
            if (isActiveField) {
                formData.is_active = isActiveField.value === '1' ? '1' : '0';
            }
            
            fetch(`/admin/rooms/${roomId}`, {
                method: 'PUT',
                body: JSON.stringify(formData),
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(async response => {
                let data = null;
                try {
                    data = await response.json();
                } catch (jsonError) {
                    console.error('JSON parsing error:', jsonError);
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
</script>
@endpush
