# Database Setup untuk Meeting Room Booking System

File-file SQL ini dibuat berdasarkan migration files Laravel untuk sistem booking meeting room. Ada 3 versi yang tersedia sesuai dengan kebutuhan database yang berbeda.

## 📁 File yang Tersedia

### 1. `database_complete_schema.sql` - MySQL/MariaDB Complete
- **Database**: MySQL/MariaDB
- **Fitur**: Lengkap dengan semua tabel Laravel, views, stored procedures, triggers
- **Gunakan untuk**: Production environment dengan fitur lengkap
- **Tabel**: 11 tabel (users, meeting_rooms, bookings, cache, jobs, sessions, dll)

### 2. `database_simple_schema.sql` - MySQL/MariaDB Simple
- **Database**: MySQL/MariaDB
- **Fitur**: Hanya tabel utama, sederhana untuk testing
- **Gunakan untuk**: Development/testing environment
- **Tabel**: 3 tabel utama (users, meeting_rooms, bookings)

### 3. `database_postgresql_schema.sql` - PostgreSQL
- **Database**: PostgreSQL
- **Fitur**: Lengkap dengan JSONB support, functions, triggers
- **Gunakan untuk**: Production dengan PostgreSQL
- **Tabel**: 11 tabel dengan optimasi PostgreSQL

## 🚀 Cara Penggunaan

### MySQL/MariaDB

#### Complete Schema:
```bash
# Login ke MySQL
mysql -u root -p

# Buat database baru
CREATE DATABASE meeting_room_booking;
USE meeting_room_booking;

# Import schema lengkap
source database_complete_schema.sql;
```

#### Simple Schema:
```bash
# Login ke MySQL
mysql -u root -p

# Buat database baru
CREATE DATABASE meeting_room_booking_simple;
USE meeting_room_booking_simple;

# Import schema sederhana
source database_simple_schema.sql;
```

### PostgreSQL

```bash
# Login ke PostgreSQL
psql -U postgres

# Buat database baru
CREATE DATABASE meeting_room_booking;
\c meeting_room_booking;

# Import schema
\i database_postgresql_schema.sql;
```

## 📊 Struktur Database

### Tabel Utama

#### 1. **users** - Tabel Pengguna
- `id` - Primary key
- `username` - Username unik
- `name` - Nama lengkap
- `email` - Email unik
- `password` - Password terenkripsi
- `role` - Admin atau User
- `department` - Departemen
- `phone` - Nomor telepon
- `avatar` - Foto profil
- `last_login_at` - Waktu login terakhir

#### 2. **meeting_rooms** - Tabel Ruang Meeting
- `id` - Primary key
- `name` - Nama ruang
- `description` - Deskripsi ruang
- `capacity` - Kapasitas maksimal
- `location` - Lokasi ruang
- `hourly_rate` - Tarif per jam
- `amenities` - Fasilitas (JSON)
- `images` - Gambar ruang (JSON)
- `is_active` - Status aktif/tidak

#### 3. **bookings** - Tabel Pemesanan
- `id` - Primary key
- `user_id` - ID pengguna
- `meeting_room_id` - ID ruang meeting
- `title` - Judul pemesanan
- `start_time` - Waktu mulai
- `end_time` - Waktu selesai
- `status` - Status (pending/confirmed/cancelled/completed)
- `attendees_count` - Jumlah peserta
- `attendees` - Daftar peserta (JSON)
- `total_cost` - Total biaya
- `special_requirements` - Kebutuhan khusus

### Tabel Pendukung

#### Laravel System Tables:
- `cache` - Cache storage
- `cache_locks` - Cache locks
- `jobs` - Queue jobs
- `job_batches` - Job batches
- `failed_jobs` - Failed jobs
- `sessions` - User sessions
- `password_reset_tokens` - Password reset
- `admin_sessions` - Admin sessions

## 🔍 Views yang Tersedia

### 1. **active_bookings_view**
Menampilkan pemesanan aktif dengan detail user dan ruang:
```sql
SELECT * FROM active_bookings_view;
```

### 2. **room_availability_view**
Menampilkan status ketersediaan ruang:
```sql
SELECT * FROM room_availability_view;
```

## ⚙️ Stored Procedures/Functions

### MySQL (Complete Schema):
- `CheckRoomAvailability(room_id, start_time, end_time)` - Cek ketersediaan ruang
- `GetUserBookingHistory(user_id)` - Riwayat pemesanan user

### PostgreSQL:
- `check_room_availability(room_id, start_time, end_time)` - Cek ketersediaan ruang
- `get_user_booking_history(user_id)` - Riwayat pemesanan user

## 🎯 Sample Data

Semua schema sudah include sample data:
- **Admin User**: username: `admin`, email: `admin@example.com`
- **Regular User**: username: `john.doe`, email: `john.doe@example.com`
- **5 Meeting Rooms** dengan berbagai kapasitas dan fasilitas
- **3 Sample Bookings** untuk testing

## 🔧 Konfigurasi Laravel

Setelah database dibuat, update file `.env` Laravel:

```env
DB_CONNECTION=mysql  # atau pgsql untuk PostgreSQL
DB_HOST=127.0.0.1
DB_PORT=3306  # 5432 untuk PostgreSQL
DB_DATABASE=meeting_room_booking
DB_USERNAME=root
DB_PASSWORD=your_password
```

## 📝 Catatan Penting

1. **Password Default**: Semua user sample menggunakan password `password` (terenkripsi)
2. **JSON Fields**: Menggunakan JSON/JSONB untuk amenities, attendees, dll
3. **Indexes**: Sudah dioptimasi dengan indexes untuk performa
4. **Foreign Keys**: Relasi antar tabel sudah didefinisikan
5. **Triggers**: Auto-calculate total cost saat booking dibuat

## 🚨 Troubleshooting

### Error "Table already exists":
```sql
DROP DATABASE meeting_room_booking;
CREATE DATABASE meeting_room_booking;
```

### Error "Foreign key constraint":
Pastikan import dilakukan dalam urutan yang benar (tabel parent dulu)

### Error "JSON not supported":
Pastikan menggunakan MySQL 5.7+ atau PostgreSQL 9.2+

## 📞 Support

Jika ada masalah dengan setup database, periksa:
1. Versi database yang digunakan
2. Permission user database
3. Charset dan collation settings
4. Log error database
