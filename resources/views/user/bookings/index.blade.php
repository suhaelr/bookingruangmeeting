@extends('layouts.user')

@section('title', 'Pemesanan Saya - Sistem Pemesanan Ruang Meeting')

@php
    $pageTitle = 'Pemesanan Saya';
@endphp

@push('head')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
@endpush

@push('styles')
<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
        /* Ensure buttons are clickable */
        button, .btn, [role="button"], a {
            cursor: pointer !important;
            pointer-events: auto !important;
            user-select: none;
        }
        
        /* Ensure form elements are interactive */
        select, input, textarea {
            pointer-events: auto !important;
        }
        
        /* Fix dropdown styling */
        select {
            background-color: #ffffff !important;
            color: #000000 !important;
            border: 1px solid #d1d5db !important;
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
            background-color: #6366f1 !important;
            color: #ffffff !important;
        }
        
        /* Select2 styling for status filter */
        .select2-container--default .select2-selection--single {
            height: 48px;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            padding: 0;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 48px;
            padding-left: 16px;
            color: #000000;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px;
            right: 10px;
        }
        
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #3b82f6;
            color: white;
        }
        
        .select2-dropdown {
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
        }
        
        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #e5e7eb;
        }
        
        .select2-container--default .select2-results__option[aria-selected=true]:hover {
            background-color: #3b82f6;
            color: white;
        }
        
        /* Fix button hover states */
        button:hover, .btn:hover, a:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        /* Ensure proper z-index for overlays */
        .glass-effect {
            position: relative;
            z-index: 1;
        }
        
        /* Fix booking card interactions */
        .booking-item {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        /* Mobile status badge fix */
        @media (max-width: 768px) {
            .booking-item {
                margin-bottom: 1rem;
            }
            
            .booking-item .flex.items-start.justify-between {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .booking-item .flex-shrink-0 {
                margin-left: 0;
                margin-top: 0.5rem;
                align-self: flex-start;
            }
            
            .booking-item .flex.items-center.space-x-4 {
                flex-wrap: wrap;
                gap: 0.5rem;
            }
        }
        
        /* Flatpickr styling - Tailwind CSS design */
        .flatpickr-calendar {
            z-index: 9999 !important;
            background-color: #ffffff !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 0.75rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
            font-family: inherit !important;
            padding: 0.75rem !important;
            width: auto;
            height: auto;
        }

        .flatpickr-calendar.open {
            height: auto;
            max-height: unset;
        }
        
        /* Month header */
        .flatpickr-months .flatpickr-month {
            height: 50px;
        }
        .flatpickr-months .flatpickr-prev-month{
            transform: translate(15px, 15px);
        }
        .flatpickr-months .flatpickr-next-month {
            transform: translate(-15px, 15px);
        }
        .flatpickr-month {
            background-color: #ffffff !important;
            border-bottom: 1px solid #e5e7eb !important;
            margin-bottom: 0.5rem !important;
        }

        .flatpickr-calendar.hasTime .flatpickr-time {
            height: 65px;
        }
        
        .flatpickr-current-month {
            font-size: 1rem !important;
            font-weight: 600 !important;
            color: #111827 !important;
            padding: 0.5rem 0 !important;
        }
        
        .flatpickr-prev-month,
        .flatpickr-next-month {
            color: #6b7280 !important;
            fill: #6b7280 !important;
            padding: 0.5rem !important;
            border-radius: 0.375rem !important;
            transition: all 0.2s !important;
        }
        
        .flatpickr-prev-month:hover,
        .flatpickr-next-month:hover {
            background-color: #f3f4f6 !important;
            color: #111827 !important;
            fill: #111827 !important;
        }
        
        /* Weekdays */
        .flatpickr-weekdays {
            background-color: #ffffff !important;
            border-bottom: 1px solid #e5e7eb !important;
            padding: 0.5rem 0 !important;
            margin-bottom: 0.5rem !important;
        }
        
        .flatpickr-weekday {
            color: #6b7280 !important;
            font-size: 0.75rem !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
        }
        
        /* Days */
        .flatpickr-days {
            background-color: #ffffff !important;
        }
        
        .flatpickr-day {
            color: #374151 !important;
            border-radius: 0.375rem !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            height: 2.5rem !important;
            line-height: 2.5rem !important;
            transition: all 0.15s ease-in-out !important;
        }
        
        .flatpickr-day:hover {
            background-color: #f3f4f6 !important;
            color: #111827 !important;
            border-color: transparent !important;
        }
        
        .flatpickr-day.selected,
        .flatpickr-day.startRange,
        .flatpickr-day.endRange {
            background-color: #3b82f6 !important;
            color: #ffffff !important;
            border-color: #3b82f6 !important;
            font-weight: 600 !important;
        }
        
        .flatpickr-day.selected:hover,
        .flatpickr-day.startRange:hover,
        .flatpickr-day.endRange:hover {
            background-color: #2563eb !important;
            border-color: #2563eb !important;
        }
        
        .flatpickr-day.today {
            border-color: #3b82f6 !important;
            font-weight: 600 !important;
        }
        
        .flatpickr-day.today:hover {
            background-color: #dbeafe !important;
            color: #1e40af !important;
        }
        
        .flatpickr-day.flatpickr-disabled,
        .flatpickr-day.prevMonthDay,
        .flatpickr-day.nextMonthDay {
            color: #d1d5db !important;
            background-color: transparent !important;
        }
        
        .flatpickr-day.flatpickr-disabled:hover {
            background-color: transparent !important;
        }
        
        /* Time picker */
        .flatpickr-time {
            background-color: #ffffff !important;
            border-top: 1px solid #e5e7eb !important;
            padding: 0.75rem 0.5rem !important;
            margin-top: 0.5rem !important;
            line-height: unset;
            max-height: unset;
        }
        
        .flatpickr-time .flatpickr-time-separator {
            color: #6b7280 !important;
            margin: 0 0.25rem !important;
            transform: translate(2px, 20px);
        }
        
        .flatpickr-time input {
            color: #111827 !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            background-color: #f9fafb !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 0.375rem !important;
            padding: 0.5rem 0.75rem !important;
            transition: all 0.15s ease-in-out !important;
        }
        
        .flatpickr-time input:hover {
            background-color: #f3f4f6 !important;
            border-color: #d1d5db !important;
        }
        
        .flatpickr-time input:focus {
            outline: none !important;
            background-color: #ffffff !important;
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
        }
        
        .flatpickr-time .flatpickr-am-pm {
            color: #111827 !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            background-color: #f9fafb !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 0.375rem !important;
            padding: 0.5rem 0.75rem !important;
            margin-left: 0.5rem !important;
        }
        
        .flatpickr-time .flatpickr-am-pm:hover {
            background-color: #f3f4f6 !important;
            border-color: #d1d5db !important;
        }
        
        /* Arrow */
        .flatpickr-calendar.arrowTop:before {
            border-bottom-color: #e5e7eb !important;
        }
        
        .flatpickr-calendar.arrowTop:after {
            border-bottom-color: #ffffff !important;
        }
        
        .flatpickr-calendar.arrowBottom:before {
            border-top-color: #e5e7eb !important;
        }
        
        .flatpickr-calendar.arrowBottom:after {
            border-top-color: #ffffff !important;
        }
    </style>
@endpush

@section('main-content')
    <!-- Header -->
    <div class="border border-[#071e48] bg-[#071e48] rounded-2xl p-6 mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white mb-2">Pemesanan Saya</h2>
                    <p class="text-white">Kelola dan pantau pemesanan ruang meeting Anda</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-4">
                    <a href="{{ route('user.bookings.create') }}" 
                        class="border border-white hover:bg-blue-600 hover:border-blue-600 text-white px-6 py-3 rounded-lg transition-colors duration-300 flex items-center">
                        <i data-feather="plus" class="mr-2" style="width: 18px; height: 18px;"></i>
                        Pesan Ruang Meeting
                    </a>
                    <button id="export-btn" class="px-4 py-2 border border-white hover:bg-green-600 hover:border-green-600 text-white rounded-lg transition-colors duration-300 flex items-center">
                        <i data-feather="download" class="mr-2" style="width: 18px; height: 18px;"></i>Export
                    </button>
                </div>
            </div>
        </div>

    <!-- Bookings List -->
    <div class="rounded-2xl p-6 border border-gray-200">
            <div class="flex justify-end mb-5">
                <div style="width: 200px;">
                    <select id="status-filter" class="px-4 py-2 rounded-lg border border-gray-200 bg-white text-black">
                        <option value="">Semua Status</option>
                        <option value="pending">Menunggu</option>
                        <option value="confirmed">Dikonfirmasi</option>
                        <option value="cancelled">Dibatalkan</option>
                        <option value="completed">Selesai</option>
                    </select>
                </div>
            </div>
            @if($bookings->count() > 0)
                <!-- Empty state for filtered results (hidden by default) -->
                <div id="filtered-empty-state" class="hidden text-center py-12">
                    <i data-feather="search" class="text-gray-300 mb-4 w-[64px] h-[64px] mx-auto"></i>
                    <h3 class="text-xl font-bold text-black mb-2">Tidak Ada Pemesanan Ditemukan</h3>
                    <p class="text-black mb-6">Tidak ada pemesanan yang sesuai dengan filter yang dipilih.</p>
                    <button onclick="document.getElementById('status-filter').value = ''; $(document.getElementById('status-filter')).trigger('change');" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors duration-300 inline-flex items-center">
                        <i data-feather="x" class="mr-2" style="width: 18px; height: 18px;"></i>
                        Hapus Filter
                    </button>
                </div>
                
                <div id="bookings-list" class="space-y-4">
                    @foreach($bookings as $booking)
                    <div class="booking-item bg-gray-50 rounded-lg p-6 hover:border-blue-800 hover:bg-blue-50 transition-colors border border-gray-200" 
                         data-status="{{ $booking->status }}">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                            <div class="flex-1">
                                <div class="mb-4">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-bold text-black mb-1">{{ $booking->title }}</h3>
                                            <p class="text-black text-sm mb-2">{{ $booking->meetingRoom->name }} • {{ $booking->meetingRoom->location }}</p>
                                        </div>
                                        <div class="ml-4 flex-shrink-0">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                                @if($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($booking->status === 'confirmed') bg-green-100 text-green-800
                                                @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                                                @elseif($booking->status === 'completed') bg-blue-100 text-blue-800
                                                @else bg-gray-100 text-black @endif">
                                                @if($booking->status === 'pending') Menunggu
                                                @elseif($booking->status === 'confirmed') Dikonfirmasi
                                                @elseif($booking->status === 'cancelled') Dibatalkan
                                                @elseif($booking->status === 'completed') Selesai
                                                @else {{ ucfirst($booking->status) }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4 text-sm text-black">
                                        <span><i data-feather="calendar" class="mr-1 inline" style="width: 16px; height: 16px;"></i>{{ $booking->start_time->format('d M Y') }}</span>
                                        <span><i data-feather="clock" class="mr-1 inline" style="width: 16px; height: 16px;"></i>{{ $booking->start_time->format('H:i') }} - {{ $booking->end_time->format('H:i') }}</span>
                                        <span><i data-feather="users" class="mr-1 inline" style="width: 16px; height: 16px;"></i>{{ $booking->attendees_count }} peserta</span>
                                    </div>
                                </div>
                                
                                @if($booking->description)
                                <p class="text-black text-sm mb-3">{{ $booking->description }}</p>
                                @endif
                                
                                @if($booking->special_requirements)
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-3">
                                    <p class="text-yellow-800 text-sm">
                                        <i data-feather="alert-triangle" class="mr-1 inline" style="width: 16px; height: 16px;"></i>
                                        <strong>Kebutuhan Khusus:</strong> {{ $booking->special_requirements }}
                                    </p>
                                </div>
                                @endif

                                @if(isset($booking->preempt_status) && $booking->preempt_status === 'pending')
                                <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-3">
                                    <p class="text-red-800 text-sm mb-3">
                                        <i data-feather="alert-circle" class="mr-2 inline" style="width: 16px; height: 16px;"></i>
                                        <strong>Permintaan Didahulukan:</strong> Booking ini sedang menunggu tanggapan Anda.
                                    </p>
                                    <div class="flex flex-wrap gap-2">
                                        <button onclick="respondPreempt({{ $booking->id }}, 'accept_cancel')" class="px-3 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors">
                                            <i data-feather="check" class="mr-1 inline" style="width: 16px; height: 16px;"></i>Terima & Batalkan
                                        </button>
                                        <button onclick="respondPreempt({{ $booking->id }}, 'reject')" class="px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                                            <i data-feather="x" class="mr-1 inline" style="width: 16px; height: 16px;"></i>Tolak
                                        </button>
                                    </div>
                                </div>
                                @endif
                                
                                @if($booking->attendees && count($booking->attendees) > 0)
                                <div class="mb-3">
                                    <p class="text-black text-sm mb-1">Peserta:</p>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($booking->attendees as $attendee)
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">
                                            {{ $attendee }}
                                        </span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                                
                                @if($booking->status === 'cancelled' && $booking->cancellation_reason)
                                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                    <p class="text-red-800 text-sm">
                                        <i data-feather="x-circle" class="mr-1 inline" style="width: 16px; height: 16px;"></i>
                                        <strong>Alasan Pembatalan:</strong> {{ $booking->cancellation_reason }}
                                    </p>
                                </div>
                                @endif
                            </div>
                            
                            <div class="flex items-center space-x-2 mt-4 lg:mt-0 lg:ml-6">
                                @if($booking->canBeCancelled())
                                <button onclick="cancelBooking({{ $booking->id }})" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors duration-300 flex items-center">
                                    <i data-feather="x" class="mr-1" style="width: 18px; height: 18px;"></i>Batal
                                </button>
                                @endif
                                
                                <button onclick="viewBooking({{ $booking->id }})" class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg transition-colors duration-300 flex items-center">
                                    <i data-feather="eye" class="mr-1" style="width: 18px; height: 18px;"></i>Lihat
                                </button>
                                
                                @if($booking->status !== 'confirmed')
                                <button onclick="editBooking({{ $booking->id }})" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-300 flex items-center">
                                    <i data-feather="edit" class="mr-1" style="width: 18px; height: 18px;"></i>Edit
                                </button>
                                @else
                                <button disabled class="px-4 py-2 bg-gray-400 text-gray-200 rounded-lg cursor-not-allowed flex items-center" title="Tidak dapat diedit karena sudah dikonfirmasi admin">
                                    <i data-feather="lock" class="mr-1" style="width: 18px; height: 18px;"></i>Edit
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div id="bookings-pagination" class="flex justify-between items-center mt-8">
                    <div class="text-black text-sm">
                        Menampilkan {{ $bookings->firstItem() }} sampai {{ $bookings->lastItem() }} dari {{ $bookings->total() }} pemesanan
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="flex space-x-2">
                        @if($bookings->previousPageUrl())
                        <a href="{{ $bookings->previousPageUrl() }}" class="px-3 py-2 bg-gray-100 text-black rounded-lg hover:bg-gray-200 transition-colors">
                            <i data-feather="chevron-left" style="width: 18px; height: 18px;"></i>
                        </a>
                        @endif
                        
                        @for($i = 1; $i <= $bookings->lastPage(); $i++)
                        <a href="{{ $bookings->url($i) }}" 
                           class="px-3 py-2 rounded-lg transition-colors {{ $bookings->currentPage() == $i ? 'bg-white text-indigo-600 font-semibold border border-indigo-100' : 'bg-gray-100 text-black hover:bg-gray-200' }}">
                            {{ $i }}
                        </a>
                        @endfor
                        
                        @if($bookings->nextPageUrl())
                        <a href="{{ $bookings->nextPageUrl() }}" class="px-3 py-2 bg-gray-100 text-black rounded-lg hover:bg-gray-200 transition-colors">
                            <i data-feather="chevron-right" style="width: 18px; height: 18px;"></i>
                        </a>
                        @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <i data-feather="calendar" class="text-gray-300 mb-4 w-[64px] h-[64px] mx-auto"></i>
                    <h3 class="text-xl font-bold text-black mb-2">Tidak Ada Pemesanan</h3>
                    <p class="text-black mb-6">Anda belum membuat pemesanan ruang meeting.</p>
                    <a href="{{ route('user.bookings.create') }}" 
                       class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors duration-300 inline-flex items-center">
                        <i data-feather="plus" class="mr-2" style="width: 18px; height: 18px;"></i>
                        Buat Pemesanan Pertama
                    </a>
                </div>
            @endif
    </div>
@endsection

@push('modals')
    <!-- Booking Detail Modal -->
    <div id="bookingDetailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-full w-[700px] max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-black">Detail Pemesanan</h3>
                    <button onclick="closeModal('bookingDetailModal')" class="text-gray-500 hover:text-gray-700">
                        <i data-feather="x" style="width: 20px; height: 20px;"></i>
                    </button>
                </div>
                <div id="bookingDetailContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Edit Modal -->
    <div id="bookingEditModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-black">Edit Pemesanan</h3>
                    <button onclick="closeModal('bookingEditModal')" class="text-gray-500 hover:text-gray-700">
                        <i data-feather="x" style="width: 20px; height: 20px;"></i>
                    </button>
                </div>
                <form id="bookingEditForm">
                    @csrf
                    <div id="bookingEditContent">
                        <!-- Content will be loaded here -->
                    </div>
                    <div class="flex justify-end space-x-4 mt-6">
                        <button type="button" onclick="closeModal('bookingEditModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                            Perbarui Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Booking Batal Modal -->
    <div id="bookingBatalModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-full w-[450px]">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                        <i data-feather="alert-triangle" class="text-red-600" style="width: 24px; height: 24px;"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-black">Batal Booking</h3>
                        <p class="text-black">Tindakan ini tidak dapat dibatalkan</p>
                    </div>
                </div>
                <p class="text-black mb-6">Apakah Anda yakin ingin membatalkan pemesanan ini?</p>
                <div class="flex justify-end space-x-4">
                    <button onclick="closeModal('bookingBatalModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Batal
                    </button>
                    <button onclick="confirmBatalBooking()" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                        Batal Booking
                    </button>
                </div>
            </div>
        </div>
    </div>

@endpush

@push('scripts')
<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
        // Serialize bookings for client-side use (relations eager loaded in controller)
        const BOOKINGS = @json($bookings->items());
        let currentBookingId = null;

        // Modal functions
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Booking actions
        function viewBooking(bookingId) {
            currentBookingId = bookingId;
            const booking = BOOKINGS.find(b => b.id == bookingId);
            
            if (booking) {
                document.getElementById('bookingDetailContent').innerHTML = `
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xl font-bold text-gray-800">${booking.title}</h4>
                            <span class="px-3 py-1 rounded-full text-sm font-medium ${getStatusColor(booking.status)}">
                                ${booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ruang</label>
                                <p class="text-gray-900">${booking.meeting_room.name}</p>
                                <p class="text-gray-600 text-sm">${booking.meeting_room.location}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal & Waktu</label>
                                <p class="text-gray-900">${new Date(booking.start_time).toLocaleDateString()}</p>
                                <p class="text-gray-600 text-sm">${new Date(booking.start_time).toLocaleTimeString()} - ${new Date(booking.end_time).toLocaleTimeString()}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Durasi</label>
                                <p class="text-gray-900">${calculateDuration(booking.start_time, booking.end_time)} jam</p>
                            </div>
                        </div>
                        
                        ${booking.description ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                            <p class="text-gray-900">${booking.description}</p>
                        </div>
                        ` : ''}
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Visibility Deskripsi</label>
                            <p class="text-gray-900">
                                ${booking.description_visibility === 'public' ? 
                                    '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-globe mr-1"></i>Publik - Semua PIC dapat melihat</span>' :
                                    '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"><i class="fas fa-lock mr-1"></i>Terbatas - Hanya PIC yang diundang</span>'
                                }
                            </p>
                        </div>
                        
                        ${booking.invitations && booking.invitations.length > 0 ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">PIC yang Diundang</label>
                            <div class="space-y-2">
                                ${booking.invitations.map(invitation => `
                                    <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-medium mr-3">
                                                ${invitation.pic.full_name.charAt(0).toUpperCase()}
                                            </div>
                                            <div>
                                                <p class="text-gray-900 font-medium">${invitation.pic.full_name}</p>
                                                <p class="text-gray-600 text-sm">${invitation.pic.unit_kerja || 'Tidak ada unit kerja'}</p>
                                            </div>
                                        </div>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium ${getInvitationStatusColor(invitation.status)}">
                                            ${getInvitationStatusText(invitation.status)}
                                        </span>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                        ` : `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">PIC yang Diundang</label>
                            <p class="text-gray-500 italic">Tidak ada PIC yang diundang</p>
                        </div>
                        `}
                        
                        ${booking.special_requirements ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kebutuhan Khusus</label>
                            <p class="text-gray-900">${booking.special_requirements}</p>
                        </div>
                        ` : ''}
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Peserta</label>
                            <p class="text-gray-900">${booking.attendees_count} orang</p>
                            ${booking.attendees && booking.attendees.length > 0 ? `
                                <div class="mt-2">
                                    ${booking.attendees.map(email => `<span class="inline-block bg-gray-100 text-gray-800 px-2 py-1 rounded text-sm mr-2 mb-1">${email}</span>`).join('')}
                                </div>
                            ` : ''}
                        </div>
                    </div>
                `;
                openModal('bookingDetailModal');
            }
        }

        function editBooking(bookingId) {
            currentBookingId = bookingId;
            const booking = BOOKINGS.find(b => b.id == bookingId);
            const ALL_PICS = @json($allPics ?? []);
            
            if (booking) {
                // Build invited pics checklist
                const invitedIds = (booking.invitations || []).map(inv => inv.pic_id);
                const picsHtml = ALL_PICS
                    .filter(p => p.id !== booking.user_id)
                    .map(p => `
                        <label class="flex items-center text-gray-700 mb-2 cursor-pointer">
                            <input type="checkbox" name="invited_pics[]" value="${p.id}" class="mr-3" ${invitedIds.includes(p.id) ? 'checked' : ''}>
                            <div>
                                <div class="font-medium">${p.full_name}</div>
                                <div class="text-sm text-gray-500">${p.unit_kerja || ''}</div>
                            </div>
                        </label>
                    `).join('');

                document.getElementById('bookingEditContent').innerHTML = `
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Judul</label>
                            <input type="text" name="title" value="${booking.title}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Visibility Deskripsi</label>
                            <div class="space-y-2">
                                <label class="flex items-center"><input type="radio" name="description_visibility" value="invited_pics_only" class="mr-2" ${booking.description_visibility === 'invited_pics_only' ? 'checked' : ''}>Hanya PIC yang diundang dapat melihat deskripsi</label>
                                <label class="flex items-center"><input type="radio" name="description_visibility" value="public" class="mr-2" ${booking.description_visibility === 'public' ? 'checked' : ''}>Semua PIC dapat melihat deskripsi</label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih PIC yang akan diundang</label>
                            <div class="max-h-48 overflow-y-auto border rounded-lg p-3">
                                ${picsHtml || '<div class="text-sm text-gray-500">Tidak ada PIC tersedia</div>'}
                            </div>
                        </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                                <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">${booking.description || ''}</textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Mulai Waktu</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <i data-feather="calendar" style="width: 18px; height: 18px;" class="text-gray-500"></i>
                                    </div>
                                    <input type="text" id="edit_start_time" name="start_time" value="${formatTanggalWaktuLocal(booking.start_time)}" 
                                           class="block w-full ps-10 pe-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                           required onchange="checkTimeConflict()">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Selesai Waktu</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <i data-feather="calendar" style="width: 18px; height: 18px;" class="text-gray-500"></i>
                                    </div>
                                    <input type="text" id="edit_end_time" name="end_time" value="${formatTanggalWaktuLocal(booking.end_time)}" 
                                           class="block w-full ps-10 pe-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                           required onchange="checkTimeConflict()">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Conflict Warning -->
                        <div id="time-conflict-warning" class="hidden mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-triangle text-red-500 mr-3 mt-1"></i>
                                <div>
                                    <h4 class="text-red-800 font-medium">Konflik Jadwal Terdeteksi!</h4>
                                    <p id="conflict-message" class="text-red-700 text-sm mt-1"></p>
                                </div>
                            </div>
                        </div>
                        <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kebutuhan Khusus</label>
                            <textarea name="special_requirements" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">${booking.special_requirements || ''}</textarea>
                        </div>
                    </div>
                `;
                openModal('bookingEditModal');
                
                // Initialize Flatpickr for edit form after modal opens
                setTimeout(function() {
                    // Re-initialize feather icons for the calendar icons
                    if (typeof feather !== 'undefined') {
                        feather.replace();
                    }
                    
                    // Helper function to convert datetime-local format to Date object
                    function parseDateTimeLocal(dateStr) {
                        if (!dateStr) return null;
                        // Convert from "YYYY-MM-DDTHH:mm" to Date object
                        return new Date(dateStr);
                    }
                    
                    // Initialize Flatpickr for start time
                    const startTimeInput = document.getElementById('edit_start_time');
                    const endTimeInput = document.getElementById('edit_end_time');
                    
                    if (startTimeInput && typeof flatpickr !== 'undefined') {
                        // Destroy existing instance if any
                        if (startTimeInput._flatpickr) {
                            startTimeInput._flatpickr.destroy();
                        }
                        
                        const startDateValue = formatTanggalWaktuLocal(booking.start_time);
                        const startDate = parseDateTimeLocal(startDateValue);
                        
                        flatpickr(startTimeInput, {
                            enableTime: true,
                            dateFormat: "Y-m-d H:i",
                            time_24hr: true,
                            defaultDate: startDate || null,
                            onChange: function(selectedDates, dateStr, instance) {
                                checkTimeConflict();
                            }
                        });
                    }
                    
                    if (endTimeInput && typeof flatpickr !== 'undefined') {
                        // Destroy existing instance if any
                        if (endTimeInput._flatpickr) {
                            endTimeInput._flatpickr.destroy();
                        }
                        
                        const endDateValue = formatTanggalWaktuLocal(booking.end_time);
                        const endDate = parseDateTimeLocal(endDateValue);
                        
                        flatpickr(endTimeInput, {
                            enableTime: true,
                            dateFormat: "Y-m-d H:i",
                            time_24hr: true,
                            defaultDate: endDate || null,
                            onChange: function(selectedDates, dateStr, instance) {
                                checkTimeConflict();
                            }
                        });
                    }
                }, 100);
            }
        }

        function cancelBooking(bookingId) {
            currentBookingId = bookingId;
            openModal('bookingBatalModal');
        }

        function confirmBatalBooking() {
            if (currentBookingId) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ route('user.bookings.cancel', ['id' => '__ID__']) }}`.replace('__ID__', currentBookingId);
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                form.appendChild(csrfToken);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function respondPreempt(bookingId, action) {
            fetch(`/user/bookings/${bookingId}/preempt-respond`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ action })
            })
            .then(res => res.json())
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message || 'Berhasil memproses tanggapan.'
                });
                if (data.success) location.reload();
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Gagal memproses tanggapan.'
                });
            });
        }

        // Request preempt from edit form
        window.requestPreemptFromEdit = function(bookingId) {
            const reason = prompt('Masukkan alasan mengapa Anda perlu didahulukan (opsional):');
            if (reason === null) return; // User cancelled
            
            fetch(`/user/bookings/${bookingId}/preempt-request`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    reason: reason || null
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message || 'Permintaan didahulukan berhasil dikirim!'
                    });
                    // Tutup modal edit setelah preempt dikirim
                    closeModal('bookingEditModal');
                    // Reload untuk refresh data
                    location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Gagal mengirim permintaan didahulukan.'
                    });
                }
            })
            .catch(error => {
                console.error('Error requesting preempt:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat mengirim permintaan.'
                });
            });
        };

        // removed proposeTimesPreempt: only 'Terima & Batalkan' is supported

        function getStatusColor(status) {
            const colors = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'confirmed': 'bg-green-100 text-green-800',
                'cancelled': 'bg-red-100 text-red-800',
                'completed': 'bg-blue-100 text-blue-800'
            };
            return colors[status] || 'bg-gray-100 text-black';
        }

        function getInvitationStatusColor(status) {
            const colors = {
                'invited': 'bg-yellow-100 text-yellow-800',
                'accepted': 'bg-green-100 text-green-800',
                'declined': 'bg-red-100 text-red-800'
            };
            return colors[status] || 'bg-gray-100 text-gray-800';
        }

        function getInvitationStatusText(status) {
            const texts = {
                'invited': 'Diundang',
                'accepted': 'Diterima',
                'declined': 'Ditolak'
            };
            return texts[status] || 'Tidak diketahui';
        }

        function calculateDuration(startWaktu, endWaktu) {
            const start = new Date(startWaktu);
            const end = new Date(endWaktu);
            const diffMs = end - start;
            const diffHours = diffMs / (1000 * 60 * 60);
            return diffHours.toFixed(1);
        }

        function formatTanggalWaktuLocal(dateWaktu) {
            const date = new Date(dateWaktu);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        }
        
        // Time conflict checking
        window.checkTimeConflict = function() {
            const startTime = document.querySelector('input[name="start_time"]').value;
            const endTime = document.querySelector('input[name="end_time"]').value;
            const warningDiv = document.getElementById('time-conflict-warning');
            const conflictMessage = document.getElementById('conflict-message');
            
            if (!startTime || !endTime) {
                warningDiv.classList.add('hidden');
                return;
            }
            
            // Check if end time is after start time
            if (new Date(endTime) <= new Date(startTime)) {
                warningDiv.classList.remove('hidden');
                conflictMessage.textContent = 'Waktu selesai harus setelah waktu mulai.';
                return;
            }
            
            // Check for conflicts with other bookings
            fetch('{{ route("user.check-availability") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    room_id: getCurrentBookingRoomId(),
                    start_time: startTime,
                    end_time: endTime,
                    exclude_booking_id: currentBookingId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.available) {
                    warningDiv.classList.add('hidden');
                } else {
                    warningDiv.classList.remove('hidden');
                    // Show conflicts with "Minta Didahulukan" buttons if available
                    if (data.conflicts && data.conflicts.length > 0) {
                        let conflictsHtml = '<div class="mt-3 space-y-2">';
                        conflictsHtml += '<p class="text-sm font-medium text-red-700 mb-2">Pilih booking yang ingin Anda minta didahulukan:</p>';
                        data.conflicts.forEach(conflict => {
                            conflictsHtml += `
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                    <div class="mb-2">
                                        <h5 class="font-medium text-gray-800">${conflict.title}</h5>
                                        <p class="text-xs text-gray-600">oleh ${conflict.user}</p>
                                        <p class="text-xs text-gray-500">${conflict.start_time} - ${conflict.end_time}</p>
                                    </div>
                                    <button type="button" onclick="requestPreemptFromEdit(${conflict.id})" 
                                            class="w-full px-3 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-sm transition-colors duration-300 flex items-center justify-center">
                                        <i class="fas fa-handshake mr-2"></i>
                                        Minta Didahulukan
                                    </button>
                                </div>
                            `;
                        });
                        conflictsHtml += '</div>';
                        conflictMessage.innerHTML = data.message.replace(/\n/g, '<br>') + conflictsHtml;
                    } else {
                        conflictMessage.innerHTML = data.message.replace(/\n/g, '<br>');
                    }
                }
            })
            .catch(error => {
                console.error('Error checking time conflict:', error);
            });
        };
        
        function getCurrentBookingRoomId() {
            // Get room ID from the current booking data
            const booking = BOOKINGS.find(b => b.id == currentBookingId);
            return booking ? booking.meeting_room.id : null;
        }

        // Initialize Select2 for status filter
        function initStatusFilterSelect2() {
            if (typeof $ === 'undefined') {
                console.error('jQuery tidak dimuat.');
                return;
            }

            $('#status-filter').select2({
                theme: 'bootstrap-5',
                placeholder: 'Semua Status',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "Tidak ada hasil";
                    },
                    searching: function() {
                        return "Mencari...";
                    }
                }
            });
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Select2 for status filter
            initStatusFilterSelect2();
            
            // Status filter change event
            $(document).on('change', '#status-filter', function() {
                const selectedStatus = $(this).val();
                const bookingItems = document.querySelectorAll('.booking-item');
                const emptyState = document.getElementById('filtered-empty-state');
                const bookingsList = document.getElementById('bookings-list');
                const pagination = document.getElementById('bookings-pagination');
                let visibleCount = 0;
                
                bookingItems.forEach(item => {
                    if (selectedStatus === '' || selectedStatus === null || item.dataset.status === selectedStatus) {
                        item.style.display = 'block';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                // Show/hide empty state and pagination based on visible items
                if (visibleCount === 0 && (selectedStatus !== '' && selectedStatus !== null)) {
                    emptyState.classList.remove('hidden');
                    if (bookingsList) bookingsList.classList.add('hidden');
                    if (pagination) pagination.classList.add('hidden');
                } else {
                    emptyState.classList.add('hidden');
                    if (bookingsList) bookingsList.classList.remove('hidden');
                    if (pagination) pagination.classList.remove('hidden');
                }
            });

            // Export functionality
            const exportBtn = document.getElementById('export-btn');
            if (exportBtn) {
                exportBtn.addEventListener('click', function() {
                    const bookings = BOOKINGS;
                    
                    // Prepare data for Excel
                    const data = bookings.map(booking => ({
                        'Judul': booking.title,
                        'Ruang': booking.meeting_room?.name || '',
                        'Mulai Waktu': booking.start_time || '',
                        'Selesai Waktu': booking.end_time || '',
                        'Status': booking.status || '',
                        'Biaya': booking.total_cost || 0
                    }));

                    // Create workbook
                    const wb = XLSX.utils.book_new();
                    const ws = XLSX.utils.json_to_sheet(data);

                    // Set column widths
                    ws['!cols'] = [
                        { wch: 30 }, // Judul
                        { wch: 20 }, // Ruang
                        { wch: 20 }, // Mulai Waktu
                        { wch: 20 }, // Selesai Waktu
                        { wch: 15 }, // Status
                        { wch: 15 }  // Biaya
                    ];

                    // Add worksheet to workbook
                    XLSX.utils.book_append_sheet(wb, ws, 'Pemesanan Saya');

                    // Generate Excel file
                    const filename = `my-bookings-${new Date().toISOString().split('T')[0]}.xlsx`;
                    XLSX.writeFile(wb, filename);
                });
            }

            // Edit form submission
            const editForm = document.getElementById('bookingEditForm');
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Collect form data manually
                    const formData = {
                        title: document.querySelector('input[name="title"]').value,
                        description: document.querySelector('textarea[name="description"]').value,
                        description_visibility: (document.querySelector('input[name="description_visibility"]:checked') || {}).value,
                        start_time: document.querySelector('input[name="start_time"]').value,
                        end_time: document.querySelector('input[name="end_time"]').value,
                        special_requirements: document.querySelector('textarea[name="special_requirements"]').value
                    };
                    const invited = Array.from(document.querySelectorAll('input[name="invited_pics[]"]:checked')).map(i => parseInt(i.value));
                    formData.invited_pics = invited;
                    
                    const bookingId = currentBookingId;
                    
                    console.log('Sending form data:', formData);
                    
                    fetch(`{{ route('user.bookings.update', ['id' => '__ID__']) }}`.replace('__ID__', bookingId), {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(formData)
                    })
                    .then(response => {
                        if (response.status === 403) {
                            return response.json().then(data => {
                                throw new Error(data.message || 'Booking tidak dapat diedit karena sudah dikonfirmasi admin.');
                            });
                        }
                        if (response.status === 422) {
                            return response.json().then(data => {
                                throw new Error(data.message || 'Validation error occurred.');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Booking berhasil diperbarui!'
                            });
                            closeModal('bookingEditModal');
                            location.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: data.message || 'Gagal memperbarui pemesanan'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message || 'Gagal memperbarui pemesanan'
                        });
                    });
                });
            }

            // Close modal on outside click
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('fixed')) {
                    const modals = ['bookingDetailModal', 'bookingEditModal', 'bookingBatalModal'];
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
            const successMessage = document.getElementById('success-message');
            if (successMessage) {
                successMessage.style.transition = 'opacity 0.5s';
                successMessage.style.opacity = '0';
                setTimeout(() => successMessage.remove(), 500);
            }
        }, 1000);
</script>
@endpush
