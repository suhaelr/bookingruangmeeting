# Database Files Summary - Meeting Room Booking System

## 📁 File yang Telah Dibuat

### 1. **Database Schema Files**

#### `database_complete_schema.sql` - MySQL/MariaDB Complete
- **Ukuran**: ~15KB
- **Tabel**: 11 tabel lengkap
- **Fitur**: Views, Stored Procedures, Triggers, Indexes
- **Gunakan untuk**: Production environment
- **Kompatibilitas**: MySQL 5.7+, MariaDB 10.2+

#### `database_simple_schema.sql` - MySQL/MariaDB Simple  
- **Ukuran**: ~8KB
- **Tabel**: 3 tabel utama
- **Fitur**: Views sederhana, Sample data
- **Gunakan untuk**: Development/Testing
- **Kompatibilitas**: MySQL 5.6+, MariaDB 10.1+

#### `database_sqlite_schema.sql` - SQLite
- **Ukuran**: ~12KB
- **Tabel**: 11 tabel lengkap
- **Fitur**: Views, Triggers, JSON support
- **Gunakan untuk**: Development, Mobile apps
- **Kompatibilitas**: SQLite 3.8+

#### `database_postgresql_schema.sql` - PostgreSQL
- **Ukuran**: ~18KB
- **Tabel**: 11 tabel lengkap
- **Fitur**: Functions, Triggers, JSONB, Views
- **Gunakan untuk**: Production dengan PostgreSQL
- **Kompatibilitas**: PostgreSQL 9.2+

### 2. **Setup Scripts**

#### `setup_database.bat` - Windows Batch Script
- **Platform**: Windows
- **Fitur**: Interactive setup untuk semua database
- **Cara pakai**: Double-click atau `setup_database.bat`

#### `setup_database.sh` - Linux/Mac Shell Script
- **Platform**: Linux, macOS, WSL
- **Fitur**: Interactive setup untuk semua database
- **Cara pakai**: `./setup_database.sh` atau `bash setup_database.sh`

### 3. **Documentation**

#### `DATABASE_SETUP_README.md` - Setup Guide
- **Ukuran**: ~8KB
- **Isi**: Panduan lengkap setup database
- **Fitur**: Troubleshooting, konfigurasi Laravel

#### `DATABASE_FILES_SUMMARY.md` - File ini
- **Isi**: Ringkasan semua file yang dibuat

## 🗂️ Struktur Database

### Tabel Utama (Core Tables)
1. **users** - Manajemen pengguna
2. **meeting_rooms** - Data ruang meeting
3. **bookings** - Pemesanan ruang

### Tabel Sistem Laravel
4. **cache** - Cache storage
5. **cache_locks** - Cache locks
6. **jobs** - Queue jobs
7. **job_batches** - Job batches
8. **failed_jobs** - Failed jobs
9. **sessions** - User sessions
10. **password_reset_tokens** - Password reset
11. **admin_sessions** - Admin sessions

## 📊 Sample Data

### Users
- **Admin**: `admin@example.com` / `password`
- **User**: `john.doe@example.com` / `password`

### Meeting Rooms (5 ruang)
- Conference Room A (20 orang, Rp 150k/jam)
- Meeting Room B (8 orang, Rp 75k/jam)
- Executive Boardroom (12 orang, Rp 250k/jam)
- Training Room (50 orang, Rp 200k/jam)
- Small Meeting Room (4 orang, Rp 50k/jam)

### Bookings (3 sample)
- Weekly Team Meeting (confirmed)
- Client Presentation (pending)
- Board Meeting (confirmed)

## 🔧 Konfigurasi Laravel

Setelah database dibuat, update `.env`:

```env
# MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=meeting_room_booking
DB_USERNAME=root
DB_PASSWORD=your_password

# PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=meeting_room_booking
DB_USERNAME=postgres
DB_PASSWORD=your_password

# SQLite
DB_CONNECTION=sqlite
DB_DATABASE=database.sqlite
```

## 🚀 Quick Start

### Windows
```cmd
setup_database.bat
```

### Linux/Mac
```bash
./setup_database.sh
```

### Manual Setup
```bash
# MySQL Complete
mysql -u root -p < database_complete_schema.sql

# MySQL Simple
mysql -u root -p < database_simple_schema.sql

# SQLite
sqlite3 database.sqlite < database_sqlite_schema.sql

# PostgreSQL
psql -U postgres -f database_postgresql_schema.sql
```

## 📋 Checklist Setup

- [ ] Pilih jenis database (MySQL/PostgreSQL/SQLite)
- [ ] Jalankan script setup atau import manual
- [ ] Verifikasi tabel terbuat dengan benar
- [ ] Update konfigurasi Laravel (.env)
- [ ] Test koneksi database
- [ ] Jalankan `php artisan migrate:status` untuk verifikasi

## 🔍 Verifikasi Setup

### MySQL
```sql
SHOW TABLES;
SELECT COUNT(*) FROM users;
SELECT COUNT(*) FROM meeting_rooms;
SELECT COUNT(*) FROM bookings;
```

### PostgreSQL
```sql
\dt
SELECT COUNT(*) FROM users;
SELECT COUNT(*) FROM meeting_rooms;
SELECT COUNT(*) FROM bookings;
```

### SQLite
```sql
.tables
SELECT COUNT(*) FROM users;
SELECT COUNT(*) FROM meeting_rooms;
SELECT COUNT(*) FROM bookings;
```

## 📞 Support

Jika ada masalah:
1. Baca `DATABASE_SETUP_README.md`
2. Periksa log error database
3. Pastikan versi database sesuai
4. Cek permission user database

## 📈 Next Steps

Setelah database setup:
1. Update `.env` Laravel
2. Jalankan `php artisan config:cache`
3. Test aplikasi dengan `php artisan serve`
4. Login dengan `admin@example.com` / `password`
