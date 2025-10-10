# Instruksi Deployment untuk Production

## Konfigurasi yang Telah Diperbaiki

Konflik antara server dan lokal telah diperbaiki dengan konfigurasi berikut:

### File yang Diperbaiki:
1. `config/app.php` - URL default diubah ke `https://pusdatinbgn.web.id`
2. `env_production_template.txt` - Template konfigurasi production
3. `deploy_production_fix.sh` - Script deployment otomatis

### Konfigurasi Production yang Benar:

```env
APP_NAME="Admin Panel"
APP_ENV=production
APP_KEY=base64:RJLU1a7rGvaKGEBrz6ZDvPXsvKfkkn0/NlilHzs>
APP_DEBUG=true
APP_URL=https://pusdatinbgn.web.id

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dsvbpgpt_lara812
DB_USERNAME=dsvbpgpt_lara812
DB_PASSWORD=superadmin123

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail Configuration for pusdatinbgn.web.id
MAIL_MAILER=smtp
MAIL_HOST=mail.pusdatinbgn.web.id
MAIL_PORT=465
MAIL_USERNAME=admin@pusdatinbgn.web.id
MAIL_PASSWORD=superadmin123
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=admin@pusdatinbgn.web.id
MAIL_FROM_NAME="${APP_NAME}"

VITE_APP_NAME="${APP_NAME}"
```

## Langkah-langkah Deployment:

### 1. Di Server Production:
```bash
# Pull perubahan terbaru dari GitHub
git pull origin master

# Copy template environment ke .env
cp env_production_template.txt .env

# Clear cache konfigurasi
php artisan config:cache

# Clear cache route
php artisan route:cache

# Clear cache view
php artisan view:cache

# Restart web server (jika menggunakan Apache/Nginx)
sudo systemctl restart apache2
# atau
sudo systemctl restart nginx
```

### 2. Verifikasi Konfigurasi:
```bash
# Cek konfigurasi aplikasi
php artisan config:show app

# Test koneksi database
php artisan migrate:status

# Test email (opsional)
php artisan tinker
# Di tinker: Mail::raw('Test email', function($msg) { $msg->to('test@example.com')->subject('Test'); });
```

### 3. Troubleshooting:

Jika masih ada masalah dengan konfigurasi:

1. **URL tidak sesuai**: Pastikan file `.env` memiliki `APP_URL=https://pusdatinbgn.web.id`
2. **Database error**: Pastikan kredensial database di `.env` sesuai dengan server
3. **Cache issue**: Jalankan `php artisan config:clear` dan `php artisan cache:clear`

### 4. File Backup:
- File `config/app.php.backup` berisi konfigurasi sebelumnya
- File `env_production_template.txt` berisi template konfigurasi production

## Status:
✅ Konflik konfigurasi telah diperbaiki
✅ Perubahan telah di-commit dan di-push ke GitHub
✅ Template konfigurasi production tersedia
✅ Script deployment otomatis tersedia

## Catatan:
- Pastikan file `.env` di server production menggunakan konfigurasi yang benar
- URL aplikasi sekarang menggunakan `https://pusdatinbgn.web.id`
- Database menggunakan `dsvbpgpt_lara812`
- Semua konfigurasi telah disesuaikan untuk environment production