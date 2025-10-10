# Complete Migration Guide for Production

## Status Saat Ini
✅ `admin_sessions` migration sudah teratasi
❌ Masih ada 6 migrasi yang pending

## Migrasi yang Pending
1. `2024_01_01_000002_create_meeting_rooms_table` - Pending
2. `2024_01_01_000003_create_bookings_table` - Pending  
3. `2025_10_09_151949_add_columns_to_users_table` - Pending
4. `2025_10_09_173254_add_email_verification_to_users_table` - Pending
5. `2025_10_10_051815_remove_hourly_rate_from_meeting_rooms_table` - Pending
6. `2025_10_10_052238_add_unit_kerja_and_dokumen_perizinan_to_bookings_table` - Pending
7. `2025_10_10_061751_update_admin_sessions_table_structure` - Pending

## Langkah Menjalankan Migrasi

### Option 1: Jalankan Semua Migrasi Sekaligus
```bash
# SSH ke production server
ssh user@pusdatinbgn.web.id
cd /path/to/your/laravel/app

# Jalankan semua migrasi
php artisan migrate
```

### Option 2: Jalankan Migrasi Individual (Jika ada error)
```bash
# SSH ke production server
ssh user@pusdatinbgn.web.id
cd /path/to/your/laravel/app

# Jalankan migrasi satu per satu
php artisan migrate --path=database/migrations/2024_01_01_000002_create_meeting_rooms_table.php
php artisan migrate --path=database/migrations/2024_01_01_000003_create_bookings_table.php
php artisan migrate --path=database/migrations/2025_10_09_151949_add_columns_to_users_table.php
php artisan migrate --path=database/migrations/2025_10_09_173254_add_email_verification_to_users_table.php
php artisan migrate --path=database/migrations/2025_10_10_051815_remove_hourly_rate_from_meeting_rooms_table.php
php artisan migrate --path=database/migrations/2025_10_10_052238_add_unit_kerja_and_dokumen_perizinan_to_bookings_table.php
php artisan migrate --path=database/migrations/2025_10_10_061751_update_admin_sessions_table_structure.php
```

### Option 3: Gunakan Script Otomatis
```bash
# SSH ke production server
ssh user@pusdatinbgn.web.id
cd /path/to/your/laravel/app

# Pull perubahan terbaru
git pull origin master

# Jalankan script
chmod +x run_pending_migrations.sh
./run_pending_migrations.sh
```

## Verifikasi Migrasi

### Cek Status Migrasi
```bash
php artisan migrate:status
```

### Cek Tabel yang Ada
```bash
mysql -u dsvbpgpt_lara812 -p dsvbpgpt_lara812 -e "SHOW TABLES;"
```

### Expected Tables
- `users`
- `cache`
- `cache_locks`
- `failed_jobs`
- `job_batches`
- `jobs`
- `migrations`
- `password_reset_tokens`
- `sessions`
- `admin_sessions`
- `meeting_rooms`
- `bookings`

## Troubleshooting

### Jika Ada Error "Table already exists":
```bash
# Cek apakah tabel sudah ada
mysql -u dsvbpgpt_lara812 -p dsvbpgpt_lara812 -e "SHOW TABLES LIKE 'table_name';"

# Jika sudah ada, mark migration as completed
mysql -u dsvbpgpt_lara812 -p dsvbpgpt_lara812 -e "INSERT IGNORE INTO migrations (migration, batch) VALUES ('migration_name', 1);"
```

### Jika Ada Error "Column already exists":
```bash
# Cek struktur tabel
mysql -u dsvbpgpt_lara812 -p dsvbpgpt_lara812 -e "DESCRIBE table_name;"

# Jika kolom sudah ada, mark migration as completed
mysql -u dsvbpgpt_lara812 -p dsvbpgpt_lara812 -e "INSERT IGNORE INTO migrations (migration, batch) VALUES ('migration_name', 1);"
```

## Setelah Migrasi Selesai

### 1. Deploy Room Update Fix
```bash
# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart web server
sudo systemctl restart apache2
```

### 2. Test Functionality
1. Buka `https://pusdatinbgn.web.id/admin/rooms`
2. Edit ruang dan ubah status
3. Cek apakah error 422 sudah teratasi

## Status
✅ `admin_sessions` migration fixed
❌ **6 migrasi masih pending**
🚨 **Perlu dijalankan sebelum deploy room update fix**

## Next Steps
1. **Jalankan migrasi yang pending**
2. **Verifikasi semua tabel ada**
3. **Deploy room update fix**
4. **Test functionality**
