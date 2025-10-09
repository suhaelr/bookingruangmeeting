<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang</title>
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
            background: #3182ce;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
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
        <h1>Selamat Datang di Meeting Room Booking System!</h1>
        <p>Halo {{ $user->full_name }}, akun Anda telah berhasil dibuat.</p>
    </div>
    
    <div class="content">
        <h2>Detail Akun Anda:</h2>
        <ul>
            <li><strong>Username:</strong> {{ $user->username }}</li>
            <li><strong>Email:</strong> {{ $user->email }}</li>
            <li><strong>Departemen:</strong> {{ $user->department ?? 'Tidak ditentukan' }}</li>
        </ul>
        
        <p>Anda sekarang dapat menggunakan sistem untuk:</p>
        <ul>
            <li>✅ Melihat ruang meeting yang tersedia</li>
            <li>✅ Membuat booking ruang meeting</li>
            <li>✅ Mengelola booking Anda</li>
            <li>✅ Menerima notifikasi booking</li>
        </ul>
        
        <div style="text-align: center;">
            <a href="{{ $loginUrl }}" class="button">Login ke Sistem</a>
        </div>
        
        <p><strong>Tips:</strong></p>
        <ul>
            <li>Pastikan untuk login dengan username dan password yang Anda buat</li>
            <li>Jika lupa password, gunakan fitur "Lupa Password" di halaman login</li>
            <li>Hubungi admin jika mengalami kendala</li>
        </ul>
    </div>
    
    <div class="footer">
        <p>Email ini dikirim secara otomatis dari Meeting Room Booking System</p>
        <p>© {{ date('Y') }} Jadixpert.com - All rights reserved</p>
    </div>
</body>
</html>
