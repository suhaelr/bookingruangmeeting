# Instruksi Deployment ke jadixpert.com

## Konfigurasi Database
Berdasarkan informasi yang diberikan:
- **Database Name:** `ypimfbgf_lara470`
- **Database User:** `ypimfbgf_lara470`
- **Database Password:** `!np5]S2o9z`
- **Database Host:** `localhost`
- **Directory:** `/home/ypimfbgf/public_html`
- **URL:** `https://jadixpert.com`

## Langkah-langkah Deployment

### 1. Setup Environment File
Buat file `.env` di root project dengan konfigurasi berikut:

```env
APP_NAME="Admin Panel"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://jadixpert.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=ypimfbgf_lara470
DB_USERNAME=ypimfbgf_lara470
DB_PASSWORD=!np5]S2o9z

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

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="admin@jadixpert.com"
MAIL_FROM_NAME="${APP_NAME}"

VITE_APP_NAME="${APP_NAME}"
```

### 2. Jalankan Setup Commands
```bash
# Generate application key
php artisan key:generate

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install

# Build assets
npm run build

# Run migrations
php artisan migrate --force

# Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. Set Permissions
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 4. Upload ke Server
Upload semua file ke `/home/ypimfbgf/public_html` dengan struktur:
```
/home/ypimfbgf/public_html/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
│   ├── index.php
│   ├── .htaccess
│   └── build/
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env
├── artisan
├── composer.json
└── composer.lock
```

### 5. Konfigurasi Web Server
Pastikan web server (Apache/Nginx) mengarah ke folder `public` di dalam project Laravel.

**Untuk Apache, pastikan .htaccess di folder public:**
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### 6. Test Aplikasi
1. Akses `https://jadixpert.com`
2. Login dengan:
   - Username: `admin`
   - Password: `admin`
3. Pastikan dashboard berfungsi dengan baik

## Troubleshooting

### Error Database Connection
```bash
# Test koneksi database
php artisan tinker
DB::connection()->getPdo();
```

### Error 500 Internal Server Error
```bash
# Check logs
tail -f storage/logs/laravel.log

# Clear cache
php artisan config:clear
php artisan cache:clear
```

### Error Permission Denied
```bash
# Set permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## Fitur yang Tersedia
- ✅ Login admin (username: admin, password: admin)
- ✅ Dashboard dengan tabel responsif
- ✅ Auto logout jika tidak login
- ✅ Tampilan modern dan responsif
- ✅ Search functionality
- ✅ Proteksi middleware

## URL Akses
- **Login:** `https://jadixpert.com/login`
- **Dashboard:** `https://jadixpert.com/dashboard`
- **Logout:** `https://jadixpert.com/logout`
