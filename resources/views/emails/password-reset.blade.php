<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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
            background: #dc2626;
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
        <h1>Reset Password</h1>
        <p>Halo {{ $user->full_name }}, Anda meminta reset password untuk akun Anda.</p>
    </div>
    
    <div class="content">
        <p>Kami menerima permintaan untuk mereset password akun Anda dengan email <strong>{{ $user->email }}</strong>.</p>
        
        <div class="warning">
            <strong>⚠️ Penting:</strong> Link ini hanya berlaku selama 1 jam dan hanya bisa digunakan sekali.
        </div>
        
        <p>Untuk mereset password Anda, klik tombol di bawah ini:</p>
        
        <div style="text-align: center;">
            <a href="{{ $resetUrl }}" class="button">Reset Password Sekarang</a>
        </div>
        
        <p>Atau copy dan paste link berikut ke browser Anda:</p>
        <p style="word-break: break-all; background: #e5e7eb; padding: 10px; border-radius: 5px; font-family: monospace;">
            {{ $resetUrl }}
        </p>
        
        <div class="warning">
            <strong>🔒 Keamanan:</strong>
            <ul>
                <li>Jika Anda tidak meminta reset password, abaikan email ini</li>
                <li>Jangan bagikan link ini dengan siapapun</li>
                <li>Link akan otomatis expired setelah 1 jam</li>
            </ul>
        </div>
        
        <p>Jika tombol tidak berfungsi, silakan hubungi administrator sistem.</p>
    </div>
    
    <div class="footer">
        <p>Email ini dikirim secara otomatis dari Meeting Room Booking System</p>
        <p>© {{ date('Y') }} Jadixpert.com - All rights reserved</p>
    </div>
</body>
</html>
