# Email Configuration Instructions

## Konfigurasi Email untuk Production

### Kredensial Email:
- **Username:** admin@pusdatinbgn.web.id
- **Password:** superadmin123
- **Incoming Server:** mail.pusdatinbgn.web.id
- **Outgoing Server:** mail.pusdatinbgn.web.id
- **IMAP Port:** 993
- **POP3 Port:** 995
- **SMTP Port:** 465
- **Encryption:** SSL/TLS

### Langkah Update di Production:

#### 1. Update .env file
```bash
# SSH ke production server
ssh user@pusdatinbgn.web.id
cd /path/to/your/laravel/app

# Backup .env file
cp .env .env.backup

# Edit .env file
nano .env
```

#### 2. Tambahkan/Update konfigurasi email di .env:
```env
# Email Configuration
MAIL_MAILER=smtp
MAIL_HOST=mail.pusdatinbgn.web.id
MAIL_PORT=465
MAIL_USERNAME=admin@pusdatinbgn.web.id
MAIL_PASSWORD=superadmin123
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=admin@pusdatinbgn.web.id
MAIL_FROM_NAME="Meeting Room Booking System"

# SSL/TLS Settings (MAIL_SCHEME removed - not supported by Laravel)
MAIL_VERIFY_PEER=false
MAIL_VERIFY_PEER_NAME=false
MAIL_ALLOW_SELF_SIGNED=true
```

#### 3. Clear cache dan restart:
```bash
# Clear cache
php artisan config:clear
php artisan cache:clear

# Rebuild cache
php artisan config:cache

# Restart web server
sudo systemctl restart apache2
```

#### 4. Test email configuration:
```bash
# Test email (optional)
php artisan tinker
# Di dalam tinker:
Mail::raw('Test email', function($message) {
    $message->to('test@example.com')->subject('Test Email');
});
```

### Verifikasi:
1. Pastikan .env file sudah diupdate
2. Pastikan cache sudah di-clear
3. Test kirim email dari aplikasi
4. Cek log email di storage/logs/laravel.log

### Troubleshooting:
- Jika email tidak terkirim, cek log di storage/logs/laravel.log
- Pastikan port 465 tidak diblokir firewall
- Pastikan SSL certificate valid
- Pastikan kredensial email benar
- **IMPORTANT:** Jangan gunakan MAIL_SCHEME=ssl (tidak didukung Laravel)
- Gunakan MAIL_ENCRYPTION=ssl untuk SSL encryption

### Security Notes:
- Password email disimpan di .env file
- File .env tidak boleh di-commit ke Git
- Pastikan .env file memiliki permission yang tepat (600)
