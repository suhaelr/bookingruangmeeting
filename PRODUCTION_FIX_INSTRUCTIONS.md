# Instruksi Perbaikan Database Production

## Masalah
Error: `SQLSTATE[42S22]: Column not found: 1054 Unknown column 'last_login_at' in 'SET'`

## Penyebab
Kolom `last_login_at` dan kolom lainnya tidak ada di database production.

## Solusi

### Opsi 1: SQL Sederhana (Rekomendasi)
Jalankan file `fix_users_table_production.sql` di database:

```bash
# Masuk ke MySQL
mysql -u username -p database_name

# Jalankan SQL
source fix_users_table_production.sql
```

### Opsi 2: SQL dengan Error Handling
Jalankan file `fix_users_table_safe.sql` di database:

```bash
# Masuk ke MySQL
mysql -u username -p database_name

# Jalankan SQL
source fix_users_table_safe.sql
```

### Opsi 3: Manual Laravel Migration
```bash
# SSH ke production server
ssh user@pusdatinbgn.web.id
cd /path/to/your/laravel/app

# Pull perubahan terbaru
git pull origin master

# Jalankan migration
php artisan migrate

# Verifikasi
php artisan tinker --execute="\DB::select('DESCRIBE users');"
```

## SQL Manual (Jika file tidak tersedia)

```sql
-- Jalankan satu per satu
ALTER TABLE users ADD COLUMN last_login_at TIMESTAMP NULL;
ALTER TABLE users ADD COLUMN avatar VARCHAR(255) NULL;
ALTER TABLE users ADD COLUMN username VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE users ADD COLUMN full_name VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE users ADD COLUMN phone VARCHAR(255) NULL;
ALTER TABLE users ADD COLUMN department VARCHAR(255) NULL;
ALTER TABLE users ADD COLUMN role ENUM('admin','user') NOT NULL DEFAULT 'user';
ALTER TABLE users ADD COLUMN email_verification_token VARCHAR(255) NULL;
```

## Verifikasi
Setelah menjalankan SQL, pastikan:

1. ✅ Kolom `last_login_at` ada di tabel `users`
2. ✅ Login berfungsi tanpa error
3. ✅ Password reset berfungsi
4. ✅ Last login terupdate saat login

## Troubleshooting

### Jika ada error "Duplicate column name"
- Abaikan error tersebut, kolom sudah ada
- Lanjutkan ke kolom berikutnya

### Jika ada error "Table doesn't exist"
- Pastikan nama database dan tabel benar
- Jalankan `SHOW TABLES;` untuk melihat tabel yang ada

### Jika ada error permission
- Pastikan user MySQL memiliki permission ALTER
- Gunakan user dengan privilege yang cukup
