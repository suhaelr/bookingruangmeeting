<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Booking & Ketersediaan Ruang Meeting</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .subtitle {
            font-size: 12px;
            color: #666;
        }
        
        .section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 12px;
            font-weight: bold;
            background-color: #f0f0f0;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }
        
        th {
            background-color: #4472C4;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        
        .booking-table th {
            font-size: 9px;
        }
        
        .room-table th {
            font-size: 9px;
        }
        
        .status-pending {
            color: #f39c12;
            font-weight: bold;
        }
        
        .status-confirmed {
            color: #27ae60;
            font-weight: bold;
        }
        
        .status-cancelled {
            color: #e74c3c;
            font-weight: bold;
        }
        
        .status-completed {
            color: #3498db;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">LAPORAN DATA BOOKING & KETERSEDIAAN RUANG MEETING</div>
        <div class="subtitle">Periode: {{ $startDate }} - {{ $endDate }}</div>
        <div class="subtitle">Dibuat pada: {{ $generatedAt }}</div>
    </div>

    <!-- Bookings Data Section -->
    <div class="section">
        <div class="section-title">DATA RIWAYAT BOOKING</div>
        
        <table class="booking-table">
            <thead>
                <tr>
                    <th style="width: 3%;">No</th>
                    <th style="width: 15%;">Judul Meeting</th>
                    <th style="width: 12%;">Nama Pemesan</th>
                    <th style="width: 12%;">Unit Kerja</th>
                    <th style="width: 15%;">Email</th>
                    <th style="width: 10%;">Ruang Meeting</th>
                    <th style="width: 12%;">Waktu Mulai</th>
                    <th style="width: 12%;">Waktu Selesai</th>
                    <th style="width: 8%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $index => $booking)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $booking->title }}</td>
                    <td>{{ $booking->user->full_name ?? $booking->user->name ?? 'N/A' }}</td>
                    <td>{{ $booking->user->unit_kerja ?? 'N/A' }}</td>
                    <td>{{ $booking->user->email }}</td>
                    <td>{{ $booking->meetingRoom->name }}</td>
                    <td>{{ $booking->start_time->format('d M Y H:i') }}</td>
                    <td>{{ $booking->end_time->format('d M Y H:i') }}</td>
                    <td style="text-align: center;">
                        <span class="status-{{ $booking->status }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 20px;">
                        Tidak ada data booking dalam periode ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Room Availability Section -->
    <div class="section">
        <div class="section-title">STATUS KETERSEDIAAN RUANG</div>
        
        <table class="room-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 20%;">Nama Ruang</th>
                    <th style="width: 20%;">Lokasi</th>
                    <th style="width: 10%;">Kapasitas</th>
                    <th style="width: 15%;">Total Booking (24h)</th>
                    <th style="width: 15%;">Status Ketersediaan</th>
                    <th style="width: 15%;">Detail Booking</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rooms as $index => $room)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $room->name }}</td>
                    <td>{{ $room->location }}</td>
                    <td style="text-align: center;">{{ $room->capacity }} orang</td>
                    <td style="text-align: center;">{{ $room->bookings->count() }}</td>
                    <td style="text-align: center;">
                        @if($room->bookings->count() > 0)
                            <span style="color: #e74c3c; font-weight: bold;">Terisi</span>
                        @else
                            <span style="color: #27ae60; font-weight: bold;">Kosong</span>
                        @endif
                    </td>
                    <td>
                        @if($room->bookings->count() > 0)
                            @foreach($room->bookings as $booking)
                                • {{ $booking->user->full_name ?? $booking->user->name }} ({{ $booking->start_time->format('H:i') }}-{{ $booking->end_time->format('H:i') }})<br>
                            @endforeach
                        @else
                            Tidak ada booking
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">
                        Tidak ada data ruang meeting
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh Sistem Pemesanan Ruang Meeting</p>
        <p>Data diambil dari database pada {{ $generatedAt }}</p>
    </div>
</body>
</html>
