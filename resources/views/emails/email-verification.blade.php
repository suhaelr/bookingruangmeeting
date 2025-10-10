<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .button {
            display: inline-block;
            background: #10b981;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .warning {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            color: #92400e;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Verifikasi Email Anda</h1>
        <p>Halo {{ $user->full_name }}, terima kasih telah mendaftar!</p>
    </div>
    
    <div class="content">
        <p>Untuk mengaktifkan akun Anda, silakan verifikasi alamat email <strong>{{ $user->email }}</strong> dengan mengklik tombol di bawah ini:</p>
        
        <div style="text-align: center;">
            <a href="{{ $verificationUrl }}" class="button">Verifikasi Email Sekarang</a>
        </div>
        
        <p>Atau copy dan paste link berikut ke browser Anda:</p>
        <p style="word-break: break-all; background: #e5e7eb; padding: 10px; border-radius: 5px; font-family: monospace;">
            {{ $verificationUrl }}
        </p>
        
        <div class="warning">
            <strong>⚠️ Penting:</strong>
            <ul>
                <li>Link verifikasi hanya berlaku selama 24 jam</li>
                <li>Jika tidak diverifikasi, akun akan dihapus otomatis</li>
                <li>Jangan bagikan link ini dengan siapapun</li>
            </ul>
        </div>
        
        <p>Setelah email diverifikasi, Anda dapat:</p>
        <ul>
            <li>✅ Login ke sistem</li>
            <li>✅ Membuat booking ruang meeting</li>
            <li>✅ Mengelola booking Anda</li>
            <li>✅ Menerima notifikasi</li>
        </ul>
        
        <p>Jika Anda tidak mendaftar di sistem ini, abaikan email ini.</p>
    </div>
    
    <div class="footer">
        <p>Email ini dikirim secara otomatis dari Meeting Room Booking System</p>
        <p>© {{ date('Y') }} Jadixpert.com - Semua hak dilindungi</p>
    </div>
</body>
</html>
