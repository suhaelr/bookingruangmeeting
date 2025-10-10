# Production Migration Fix

## Masalah
Error migrasi di production:
```
2024_01_01_000000_create_admin_sessions_table ............ 3.25ms FAIL
SQLSTATE[42S01]: Base table or view already exists: 1050 Table 'admin_sessions' already exists
```

## Root Cause
Tabel `admin_sessions` sudah ada di production, tetapi migrasi mencoba membuatnya lagi.

## Solusi

### Option 1: Mark Migration as Completed (Recommended)
```bash
# SSH ke production server
ssh user@pusdatinbgn.web.id
cd /path/to/your/laravel/app

# Masuk ke MySQL
mysql -u dsvbpgpt_lara812 -p dsvbpgpt_lara812

# Mark migration as completed
INSERT INTO migrations (migration, batch) VALUES ('2024_01_01_000000_create_admin_sessions_table', 1);

# Keluar dari MySQL
exit

# Jalankan migrasi lagi
php artisan migrate
```

### Option 2: Drop and Recreate Table
```bash
# SSH ke production server
ssh user@pusdatinbgn.web.id
cd /path/to/your/laravel/app

# Masuk ke MySQL
mysql -u dsvbpgpt_lara812 -p dsvbpgpt_lara812

# Drop table
DROP TABLE IF EXISTS admin_sessions;

# Keluar dari MySQL
exit

# Jalankan migrasi
php artisan migrate
```

### Option 3: Skip Problematic Migration
```bash
# SSH ke production server
ssh user@pusdatinbgn.web.id
cd /path/to/your/laravel/app

# Jalankan migrasi individual
php artisan migrate --path=database/migrations/2024_01_01_000002_create_meeting_rooms_table.php
php artisan migrate --path=database/migrations/2024_01_01_000003_create_bookings_table.php
php artisan migrate --path=database/migrations/2025_10_09_151949_add_columns_to_users_table.php
php artisan migrate --path=database/migrations/2025_10_09_173254_add_email_verification_to_users_table.php
php artisan migrate --path=database/migrations/2025_10_10_051815_remove_hourly_rate_from_meeting_rooms_table.php
php artisan migrate --path=database/migrations/2025_10_10_052238_add_unit_kerja_and_dokumen_perizinan_to_bookings_table.php
php artisan migrate --path=database/migrations/2025_10_10_061751_update_admin_sessions_table_structure.php
```

## Verifikasi

### Cek Tabel yang Ada
```bash
mysql -u dsvbpgpt_lara812 -p dsvbpgpt_lara812 -e "SHOW TABLES;"
```

### Cek Status Migrasi
```bash
php artisan migrate:status
```

### Cek Struktur Tabel admin_sessions
```bash
mysql -u dsvbpgpt_lara812 -p dsvbpgpt_lara812 -e "DESCRIBE admin_sessions;"
```

## Expected Results
- ✅ Semua migrasi berhasil dijalankan
- ✅ Tabel `admin_sessions` ada dengan struktur yang benar
- ✅ Tidak ada error migrasi

## Troubleshooting

### Jika Masih Error:
1. Cek apakah tabel `admin_sessions` sudah ada
2. Cek struktur tabel apakah sudah benar
3. Cek apakah migrasi sudah tercatat di tabel `migrations`

### Jika Tabel Tidak Ada:
1. Jalankan migrasi individual
2. Atau drop dan recreate tabel

## Status
❌ **Migration error di production**
🚨 **Perlu perbaikan segera**

## Next Steps
1. **Pilih salah satu solusi di atas**
2. **Jalankan perbaikan**
3. **Verifikasi hasil**
4. **Lanjutkan dengan deploy room update fix**
