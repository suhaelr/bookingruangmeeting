<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengingat Meeting</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #007bff;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }
        .reminder-notice {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
        }
        .reminder-notice h2 {
            color: #856404;
            margin: 0 0 10px 0;
            font-size: 18px;
        }
        .booking-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .booking-details h3 {
            color: #495057;
            margin-top: 0;
            margin-bottom: 15px;
        }
        .detail-row {
            display: flex;
            margin-bottom: 10px;
            align-items: center;
        }
        .detail-label {
            font-weight: bold;
            width: 120px;
            color: #495057;
        }
        .detail-value {
            flex: 1;
            color: #6c757d;
        }
        .time-highlight {
            background-color: #007bff;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏢 Meeting Room Booking System</h1>
        </div>

        <div class="reminder-notice">
            <h2>⏰ Pengingat Meeting</h2>
            <p><strong>Meeting Anda akan dimulai dalam 1 jam!</strong></p>
        </div>

        <div class="booking-details">
            <h3>📋 Detail Meeting</h3>
            
            <div class="detail-row">
                <div class="detail-label">Judul:</div>
                <div class="detail-value"><strong>{{ $booking->title }}</strong></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Ruang:</div>
                <div class="detail-value">{{ $meetingRoom->name }} ({{ $meetingRoom->location }})</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Waktu Mulai:</div>
                <div class="detail-value">
                    <span class="time-highlight">{{ $booking->start_time->format('d M Y, H:i') }}</span>
                </div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Waktu Selesai:</div>
                <div class="detail-value">
                    <span class="time-highlight">{{ $booking->end_time->format('d M Y, H:i') }}</span>
                </div>
            </div>
            
            @if($booking->description)
            <div class="detail-row">
                <div class="detail-label">Deskripsi:</div>
                <div class="detail-value">{{ $booking->description }}</div>
            </div>
            @endif
            
            <div class="detail-row">
                <div class="detail-label">Jumlah Peserta:</div>
                <div class="detail-value">{{ $booking->attendees_count }} orang</div>
            </div>
            
            @if($booking->attendees && count($booking->attendees) > 0)
            <div class="detail-row">
                <div class="detail-label">Peserta:</div>
                <div class="detail-value">{{ implode(', ', $booking->attendees) }}</div>
            </div>
            @endif
        </div>

        <div style="text-align: center; margin: 20px 0;">
            <p><strong>💡 Tips:</strong></p>
            <ul style="text-align: left; display: inline-block;">
                <li>Pastikan Anda sudah berada di ruang meeting 10-15 menit sebelum waktu dimulai</li>
                <li>Periksa peralatan yang dibutuhkan (proyektor, whiteboard, dll.)</li>
                <li>Hubungi admin jika ada masalah dengan ruang meeting</li>
                <li>Persiapkan materi presentasi dan dokumen yang diperlukan</li>
            </ul>
        </div>

        <div class="footer">
            <p>Email ini dikirim otomatis oleh sistem Meeting Room Booking.</p>
            <p>Jika Anda tidak seharusnya menerima email ini, silakan hubungi administrator.</p>
            <p>&copy; {{ date('Y') }} Meeting Room Booking System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
