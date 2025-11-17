<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $notification->title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            font-size: 24px;
            font-weight: 600;
            margin: 0;
        }
        .email-header p {
            font-size: 14px;
            margin-top: 8px;
            opacity: 0.9;
        }
        .notification-card {
            padding: 30px;
        }
        .notification-icon {
            text-align: center;
            font-size: 64px;
            margin-bottom: 20px;
        }
        .notification-title {
            font-size: 22px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 15px;
            text-align: center;
            padding-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
        }
        .notification-message {
            background-color: #f7fafc;
            border-left: 4px solid #007bff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-size: 16px;
            line-height: 1.8;
            color: #4a5568;
        }
        .notification-message.success {
            border-left-color: #28a745;
        }
        .notification-message.error {
            border-left-color: #dc3545;
        }
        .notification-message.warning {
            border-left-color: #ffc107;
        }
        .notification-message.info {
            border-left-color: #17a2b8;
        }
        @if($booking)
        .booking-details {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .booking-details h3 {
            color: #2d3748;
            font-size: 18px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
        }
        .detail-item {
            display: flex;
            margin-bottom: 12px;
            align-items: flex-start;
        }
        .detail-label {
            font-weight: 600;
            color: #4a5568;
            width: 140px;
            flex-shrink: 0;
        }
        .detail-value {
            color: #2d3748;
            flex: 1;
        }
        .time-badge {
            display: inline-block;
            background-color: #007bff;
            color: #ffffff;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
        }
        .time-badge.success {
            background-color: #28a745;
        }
        .time-badge.error {
            background-color: #dc3545;
        }
        .time-badge.warning {
            background-color: #ffc107;
        }
        .time-badge.info {
            background-color: #17a2b8;
        }
        @endif
        .action-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            margin: 20px 0;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        .button-container {
            text-align: center;
            margin: 25px 0;
        }
        .footer {
            background-color: #f7fafc;
            padding: 25px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            color: #718096;
            font-size: 14px;
        }
        .footer p {
            margin: 5px 0;
        }
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
        .type-badge {
            display: inline-block;
            background-color: #007bff;
            color: #ffffff;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
        }
        .type-badge.booking_confirmed,
        .type-badge.success {
            background-color: #28a745;
        }
        .type-badge.booking_cancelled,
        .type-badge.error {
            background-color: #dc3545;
        }
        .type-badge.booking_completed {
            background-color: #17a2b8;
        }
        .type-badge.room_maintenance,
        .type-badge.warning {
            background-color: #ffc107;
        }
        .type-badge.info {
            background-color: #007bff;
        }
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                border-radius: 0;
            }
            .notification-card {
                padding: 20px;
            }
            .detail-item {
                flex-direction: column;
            }
            .detail-label {
                width: 100%;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1>🏢 Sistem Pemesanan Ruang Meeting</h1>
            <p>Notifikasi Terbaru</p>
        </div>

        <!-- Notification Content -->
        <div class="notification-card">
            <!-- Icon -->
            <div class="notification-icon">
                {{ $typeIcon }}
            </div>

            <!-- Type Badge -->
            <div style="text-align: center;">
                <span class="type-badge {{ $notification->type }}">{{ ucfirst(str_replace('_', ' ', $notification->type)) }}</span>
            </div>

            <!-- Title -->
            <h2 class="notification-title">{{ $notification->title }}</h2>

            <!-- Message -->
            <div class="notification-message {{ $notification->type }}">
                {!! nl2br(e($notification->message)) !!}
            </div>

            @if($booking)
            <!-- Booking Details -->
            <div class="booking-details">
                <h3>📋 Detail Booking</h3>
                
                <div class="detail-item">
                    <div class="detail-label">Judul Meeting:</div>
                    <div class="detail-value"><strong>{{ $booking->title }}</strong></div>
                </div>
                
                @if($booking->meetingRoom)
                <div class="detail-item">
                    <div class="detail-label">Ruang Meeting:</div>
                    <div class="detail-value">{{ $booking->meetingRoom->name }} - {{ $booking->meetingRoom->location }}</div>
                </div>
                @endif
                
                <div class="detail-item">
                    <div class="detail-label">Waktu Mulai:</div>
                    <div class="detail-value">
                        <span class="time-badge {{ $notification->type }}">{{ $booking->start_time->format('d M Y, H:i') }}</span>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Waktu Selesai:</div>
                    <div class="detail-value">
                        <span class="time-badge {{ $notification->type }}">{{ $booking->end_time->format('d M Y, H:i') }}</span>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Status:</div>
                    <div class="detail-value">
                        @if($booking->status === 'pending')
                            <span style="color: #ffc107; font-weight: 600;">Menunggu Konfirmasi</span>
                        @elseif($booking->status === 'confirmed')
                            <span style="color: #28a745; font-weight: 600;">Dikonfirmasi</span>
                        @elseif($booking->status === 'cancelled')
                            <span style="color: #dc3545; font-weight: 600;">Dibatalkan</span>
                        @elseif($booking->status === 'completed')
                            <span style="color: #17a2b8; font-weight: 600;">Selesai</span>
                        @else
                            {{ ucfirst($booking->status) }}
                        @endif
                    </div>
                </div>
                
                @if($booking->unit_kerja)
                <div class="detail-item">
                    <div class="detail-label">Unit Kerja:</div>
                    <div class="detail-value">{{ $booking->unit_kerja }}</div>
                </div>
                @endif

                @if($booking->description)
                <div class="detail-item" style="flex-direction: column; margin-top: 15px;">
                    <div class="detail-label" style="width: 100%; margin-bottom: 8px;">Deskripsi Meeting:</div>
                    <div class="detail-value" style="width: 100%; white-space: pre-wrap; word-wrap: break-word;">
                        {!! nl2br(e($booking->description)) !!}
                    </div>
                </div>
                @endif
            </div>
            @endif

            @php
                // Cek apakah ini notifikasi undangan PIC
                $isPicInvitation = false;
                $invitationId = null;
                if ($booking && $notification->title === 'Undangan Meeting dari PIC') {
                    $invitation = \App\Models\MeetingInvitation::where('booking_id', $booking->id)
                        ->where('pic_id', $user->id)
                        ->first();
                    if ($invitation) {
                        $isPicInvitation = true;
                        $invitationId = $invitation->id;
                    }
                }
            @endphp

            <!-- Action Buttons -->
            <div class="button-container">
                @if($isPicInvitation && $invitationId)
                <a href="{{ route('user.confirm-attendance', $invitationId) }}" class="action-button" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); margin-right: 10px; display: inline-block;">
                    ✅ Konfirmasi Kehadiran
                </a>
                @endif
                <a href="{{ route($user->isAdmin() ? 'admin.dashboard' : 'user.dashboard') }}" class="action-button" style="{{ $isPicInvitation ? 'display: inline-block;' : '' }}">
                    Lihat Detail di Dashboard
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Email ini dikirim otomatis oleh sistem.</strong></p>
            <p>Jika Anda tidak seharusnya menerima email ini, silakan hubungi administrator.</p>
            <p style="margin-top: 15px;">
                <a href="{{ route('login') }}">Login ke Sistem</a> | 
                <a href="{{ route($user->isAdmin() ? 'admin.dashboard' : 'user.dashboard') }}">Dashboard</a>
            </p>
            <p style="margin-top: 15px; font-size: 12px;">
                &copy; {{ date('Y') }} Sistem Pemesanan Ruang Meeting. Semua hak dilindungi.
            </p>
        </div>
    </div>
</body>
</html>

