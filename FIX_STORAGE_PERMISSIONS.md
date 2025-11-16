# Cara Memperbaiki Permission Error Storage Laravel

## Masalah
Error: `file_put_contents(/var/www/bookingruangmeeting/storage/framework/views/...): Failed to open stream: Permission denied`

## Solusi

### 1. Masuk ke Server via SSH
```bash
ssh user@your-server-ip
cd /var/www/bookingruangmeeting
```

### 2. Perbaiki Ownership (Pilih salah satu sesuai web server Anda)

**PENTING:** Cek dulu user yang menjalankan PHP-FPM atau web server:
```bash
ps aux | grep -E 'php-fpm|apache|nginx' | head -1
```

#### Untuk Apache (www-data):
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

#### Untuk Nginx dengan PHP-FPM (www-data):
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

#### Untuk Nginx dengan user lain (misalnya nginx):
```bash
sudo chown -R nginx:nginx storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

#### Jika menggunakan user lain (misalnya www-run atau _www):
```bash
# Ganti USER_GROUP dengan user yang sesuai dari hasil ps aux di atas
sudo chown -R USER_GROUP:USER_GROUP storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 3. Pastikan Semua Direktori Storage Memiliki Permission yang Benar
```bash
# Set permission untuk semua direktori storage
sudo find storage -type d -exec chmod 775 {} \;
sudo find storage -type f -exec chmod 664 {} \;

# Set permission untuk bootstrap/cache
sudo find bootstrap/cache -type d -exec chmod 775 {} \;
sudo find bootstrap/cache -type f -exec chmod 664 {} \;
```

### 4. Verifikasi Permission
```bash
ls -la storage/framework/views/
ls -la bootstrap/cache/
```

### 5. Clear Cache Laravel
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### 6. Fix Permission untuk Artisan Commands
Jika masih error saat menjalankan `php artisan optimize` atau command lain:

```bash
# Pastikan user yang menjalankan artisan memiliki akses
# Jika menjalankan sebagai user 'linux', pastikan user tersebut memiliki akses:
sudo chown -R www-data:www-data storage bootstrap/cache
# Atau jika ingin user 'linux' yang memiliki akses:
sudo chown -R linux:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Setelah itu, coba jalankan lagi:
php artisan optimize
```

### 6. Test
Coba akses aplikasi lagi di browser. Error seharusnya sudah teratasi.

## Catatan Penting

- **Jangan gunakan 777** untuk security reasons
- **775** sudah cukup untuk direktori yang perlu write access
- **664** sudah cukup untuk file
- Pastikan web server user (www-data/nginx) memiliki ownership yang benar

## Troubleshooting

Jika masih error setelah langkah di atas:

1. **Cek user web server:**
   ```bash
   ps aux | grep -E 'apache|nginx|php-fpm' | head -1
   ```

2. **Cek permission saat ini:**
   ```bash
   ls -la storage/
   ls -la storage/framework/
   ls -la storage/framework/views/
   ```

3. **Jika menggunakan SELinux (CentOS/RHEL):**
   ```bash
   sudo chcon -R -t httpd_sys_rw_content_t storage/
   sudo chcon -R -t httpd_sys_rw_content_t bootstrap/cache/
   ```

