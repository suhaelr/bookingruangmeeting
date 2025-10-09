# Cara Menghilangkan /public dari URL

## Masalah
Saat ini URL aplikasi adalah: `https://jadixpert.com/public/dashboard`
Anda ingin URL menjadi: `https://jadixpert.com/dashboard`

## Solusi

### Opsi 1: Konfigurasi Web Server (Recommended)

#### Untuk Apache (cPanel/Shared Hosting):

1. **Buat file `.htaccess` di root domain** (`/home/ypimfbgf/public_html/.htaccess`):
```apache
RewriteEngine On
RewriteCond %{REQUEST_URI} !^/public/
RewriteRule ^(.*)$ /public/$1 [L,QSA]
```

2. **Atau pindahkan semua file Laravel ke root domain:**
   - Pindahkan semua file dari `/home/ypimfbgf/public_html/public/` ke `/home/ypimfbgf/public_html/`
   - Hapus folder `public` yang kosong

#### Untuk Apache (VPS/Dedicated):

1. **Edit Virtual Host** (`/etc/apache2/sites-available/jadixpert.com.conf`):
```apache
<VirtualHost *:80>
    ServerName jadixpert.com
    ServerAlias www.jadixpert.com
    DocumentRoot "/home/ypimfbgf/public_html/public"
    
    <Directory "/home/ypimfbgf/public_html/public">
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/jadixpert.com-error.log
    CustomLog ${APACHE_LOG_DIR}/jadixpert.com-access.log combined
</VirtualHost>
```

2. **Aktifkan mod_rewrite:**
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### Untuk Nginx:

1. **Edit server block** (`/etc/nginx/sites-available/jadixpert.com`):
```nginx
server {
    listen 80;
    server_name jadixpert.com www.jadixpert.com;
    root /home/ypimfbgf/public_html/public;
    
    index index.php index.html;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

2. **Restart Nginx:**
```bash
sudo nginx -t
sudo systemctl restart nginx
```

### Opsi 2: Pindahkan File Laravel (Alternatif)

Jika Anda tidak bisa mengubah konfigurasi web server:

1. **Pindahkan semua file Laravel ke root domain:**
```bash
# Di server
cd /home/ypimfbgf/public_html
mv public/* .
mv public/.* . 2>/dev/null || true
rmdir public
```

2. **Update file `.env`:**
```env
APP_URL=https://jadixpert.com
```

3. **Update file `public/index.php`:**
```php
// Ubah path bootstrap
require __DIR__.'/../bootstrap/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
```

### Opsi 3: Menggunakan Subdomain

1. **Buat subdomain `admin.jadixpert.com`**
2. **Arahkan ke folder `/home/ypimfbgf/public_html/public`**
3. **Update `.env`:**
```env
APP_URL=https://admin.jadixpert.com
```

## Verifikasi

Setelah melakukan salah satu opsi di atas:

1. **Test URL:**
   - `https://jadixpert.com` → Halaman login
   - `https://jadixpert.com/dashboard` → Dashboard admin
   - `https://jadixpert.com/login` → Halaman login

2. **Cek error logs:**
```bash
tail -f /var/log/apache2/error.log
# atau
tail -f /var/log/nginx/error.log
```

## Troubleshooting

### Error 500 Internal Server Error:
```bash
# Set permissions
chmod -R 755 /home/ypimfbgf/public_html
chown -R www-data:www-data /home/ypimfbgf/public_html
```

### Error 404 Not Found:
- Pastikan mod_rewrite aktif
- Cek konfigurasi Virtual Host
- Pastikan file `.htaccess` ada di root

### Error Permission Denied:
```bash
# Set ownership
chown -R www-data:www-data /home/ypimfbgf/public_html
chmod -R 755 /home/ypimfbgf/public_html
```

## Rekomendasi

**Untuk cPanel/Shared Hosting:** Gunakan Opsi 1 (file .htaccess)
**Untuk VPS/Dedicated:** Gunakan Opsi 1 (Virtual Host)
**Untuk kemudahan:** Gunakan Opsi 2 (pindahkan file)

Pilih opsi yang sesuai dengan setup hosting Anda!
