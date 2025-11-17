# Panduan Memperbaiki Error Database Connection

## Error yang Terjadi
```
SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost' (using password: NO)
```

Error ini terjadi karena password database tidak dikonfigurasi dengan benar di file `.env`.

## Solusi

### 1. Akses Server Production
```bash
ssh user@server
cd /var/www/bookingruangmeeting
```

### 2. Periksa File .env
```bash
# Lihat konfigurasi database saat ini
cat .env | grep DB_
```

### 3. Edit File .env
```bash
nano .env
```

### 4. Pastikan Konfigurasi Database Benar
Pastikan bagian database di `.env` seperti ini:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=root
DB_PASSWORD=password_database_anda
```

**PENTING:**
- Pastikan `DB_PASSWORD` **tidak kosong**
- Jika password kosong, isi dengan password MySQL yang benar
- Jika tidak ada password untuk user root, buat password atau gunakan user lain

### 5. Jika Password Kosong (Tidak Aman)
Jika MySQL root tidak memiliki password, Anda bisa:

**Opsi A: Set Password untuk Root**
```bash
# Login ke MySQL
mysql -u root

# Set password
ALTER USER 'root'@'localhost' IDENTIFIED BY 'password_baru';
FLUSH PRIVILEGES;
EXIT;
```

Kemudian update `.env`:
```env
DB_PASSWORD=password_baru
```

**Opsi B: Buat User Database Khusus (Lebih Aman)**
```bash
# Login ke MySQL sebagai root
mysql -u root

# Buat user baru
CREATE USER 'meet_user'@'localhost' IDENTIFIED BY 'password_kuat';
GRANT ALL PRIVILEGES ON nama_database.* TO 'meet_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Kemudian update `.env`:
```env
DB_USERNAME=meet_user
DB_PASSWORD=password_kuat
```

### 6. Clear Cache Konfigurasi Laravel
Setelah mengedit `.env`, **WAJIB** clear cache:

```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

### 7. Test Koneksi Database
```bash
php artisan tinker
```

Di dalam tinker, coba:
```php
DB::connection()->getPdo();
```

Jika berhasil, akan muncul: `PDO Object`
Jika error, periksa kembali konfigurasi.

### 8. Verifikasi
Coba akses website lagi. Error seharusnya sudah hilang.

## Troubleshooting

### Jika Masih Error Setelah Perbaikan

1. **Pastikan MySQL Service Running**
```bash
sudo systemctl status mysql
# atau
sudo service mysql status
```

2. **Cek User MySQL Memiliki Akses**
```bash
mysql -u root -p
```

Kemudian:
```sql
SELECT user, host FROM mysql.user WHERE user='root';
SHOW GRANTS FOR 'root'@'localhost';
```

3. **Test Koneksi Manual**
```bash
mysql -u root -p nama_database
```

Jika bisa login, berarti kredensial benar.

4. **Cek File Permission .env**
```bash
ls -la .env
```

Pastikan file readable:
```bash
chmod 644 .env
```

5. **Cek Log Laravel untuk Detail Error**
```bash
tail -n 50 storage/logs/laravel.log
```

## Catatan Keamanan

⚠️ **JANGAN** commit file `.env` ke git!
- File `.env` sudah ada di `.gitignore`
- Jangan pernah share password database
- Gunakan user database khusus untuk aplikasi (bukan root)
- Gunakan password yang kuat

## Contoh Konfigurasi .env yang Benar

```env
APP_NAME="Meeting Room Booking"
APP_ENV=production
APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
APP_DEBUG=false
APP_URL=https://meet.bgn.go.id

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=booking_ruang_meeting
DB_USERNAME=meet_user
DB_PASSWORD=password_yang_kuat_dan_aman
```

