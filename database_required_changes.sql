-- =====================================================
-- PERUBAHAN YANG DIPERLUKAN UNTUK DATABASE
-- Database: MySQL/MariaDB
-- Versi: 2025-10-10
-- =====================================================

-- Pastikan sudah memilih database yang tepat
-- USE nama_database_anda;

-- =====================================================
-- 1. TABEL BOOKINGS - TAMBAH KOLOM YANG HILANG
-- =====================================================

-- Tambah kolom unit_kerja (WAJIB untuk fitur baru)
ALTER TABLE bookings 
ADD COLUMN IF NOT EXISTS unit_kerja VARCHAR(255) NULL COMMENT 'Unit kerja pemesan' AFTER special_requirements;

-- Tambah kolom dokumen_perizinan (WAJIB untuk fitur baru)
ALTER TABLE bookings 
ADD COLUMN IF NOT EXISTS dokumen_perizinan VARCHAR(255) NULL COMMENT 'Path file dokumen perizinan PDF' AFTER unit_kerja;

-- Tambah kolom attendees jika belum ada
ALTER TABLE bookings 
ADD COLUMN IF NOT EXISTS attendees JSON NULL COMMENT 'Array email peserta' AFTER attendees_count;

-- Tambah kolom attachments jika belum ada
ALTER TABLE bookings 
ADD COLUMN IF NOT EXISTS attachments JSON NULL COMMENT 'Array file lampiran' AFTER attendees;

-- Tambah kolom special_requirements jika belum ada
ALTER TABLE bookings 
ADD COLUMN IF NOT EXISTS special_requirements TEXT NULL AFTER attachments;

-- Tambah kolom cancelled_at jika belum ada
ALTER TABLE bookings 
ADD COLUMN IF NOT EXISTS cancelled_at TIMESTAMP NULL AFTER total_cost;

-- Tambah kolom cancellation_reason jika belum ada
ALTER TABLE bookings 
ADD COLUMN IF NOT EXISTS cancellation_reason TEXT NULL AFTER cancelled_at;

-- =====================================================
-- 2. TABEL USERS - TAMBAH KOLOM YANG HILANG
-- =====================================================

-- Tambah kolom username (WAJIB untuk fitur baru)
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS username VARCHAR(255) UNIQUE AFTER id;

-- Tambah kolom full_name (WAJIB untuk fitur baru)
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS full_name VARCHAR(255) AFTER username;

-- Tambah kolom phone jika belum ada
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS phone VARCHAR(20) NULL AFTER full_name;

-- Tambah kolom department jika belum ada
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS department VARCHAR(100) NULL AFTER phone;

-- Tambah kolom role (WAJIB untuk fitur baru)
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS role ENUM('admin', 'user') NOT NULL DEFAULT 'user' AFTER department;

-- Tambah kolom avatar jika belum ada
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS avatar VARCHAR(255) NULL AFTER role;

-- Tambah kolom last_login_at jika belum ada
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS last_login_at TIMESTAMP NULL AFTER avatar;

-- Tambah kolom email_verification_token jika belum ada
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS email_verification_token VARCHAR(255) NULL AFTER email_verified_at;

-- =====================================================
-- 3. TABEL MEETING_ROOMS - HAPUS KOLOM PRICING
-- =====================================================

-- Hapus kolom hourly_rate (karena sudah tidak digunakan)
ALTER TABLE meeting_rooms 
DROP COLUMN IF EXISTS hourly_rate;

-- Tambah kolom amenities jika belum ada
ALTER TABLE meeting_rooms 
ADD COLUMN IF NOT EXISTS amenities JSON NULL COMMENT 'Array fasilitas' AFTER capacity;

-- Tambah kolom images jika belum ada
ALTER TABLE meeting_rooms 
ADD COLUMN IF NOT EXISTS images JSON NULL COMMENT 'Array path gambar' AFTER location;

-- Tambah kolom is_active jika belum ada
ALTER TABLE meeting_rooms 
ADD COLUMN IF NOT EXISTS is_active BOOLEAN NOT NULL DEFAULT TRUE AFTER images;

-- =====================================================
-- 4. TABEL LARAVEL YANG DIPERLUKAN
-- =====================================================

-- Buat tabel password_reset_tokens jika belum ada
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Buat tabel sessions jika belum ada
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INTEGER NOT NULL,
    
    INDEX idx_sessions_user_id (user_id),
    INDEX idx_sessions_last_activity (last_activity),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Buat tabel cache jika belum ada
CREATE TABLE IF NOT EXISTS cache (
    `key` VARCHAR(255) PRIMARY KEY,
    value MEDIUMTEXT NOT NULL,
    expiration INTEGER NOT NULL,
    
    INDEX idx_cache_expiration (expiration)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Buat tabel cache_locks jika belum ada
CREATE TABLE IF NOT EXISTS cache_locks (
    `key` VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration INTEGER NOT NULL,
    
    INDEX idx_cache_locks_expiration (expiration)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Buat tabel jobs jika belum ada
CREATE TABLE IF NOT EXISTS jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload LONGTEXT NOT NULL,
    attempts TINYINT UNSIGNED NOT NULL,
    reserved_at INTEGER UNSIGNED NULL,
    available_at INTEGER UNSIGNED NOT NULL,
    created_at INTEGER UNSIGNED NOT NULL,
    
    INDEX idx_jobs_queue (queue)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Buat tabel job_batches jika belum ada
CREATE TABLE IF NOT EXISTS job_batches (
    id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    total_jobs INTEGER NOT NULL,
    pending_jobs INTEGER NOT NULL,
    failed_jobs INTEGER NOT NULL,
    failed_job_ids LONGTEXT NOT NULL,
    options MEDIUMTEXT NULL,
    cancelled_at INTEGER NULL,
    created_at INTEGER NOT NULL,
    finished_at INTEGER NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Buat tabel failed_jobs jika belum ada
CREATE TABLE IF NOT EXISTS failed_jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(255) NOT NULL UNIQUE,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload LONGTEXT NOT NULL,
    exception LONGTEXT NOT NULL,
    failed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Buat tabel admin_sessions jika belum ada
CREATE TABLE IF NOT EXISTS admin_sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INTEGER NOT NULL,
    
    INDEX idx_admin_sessions_user_id (user_id),
    INDEX idx_admin_sessions_last_activity (last_activity),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 5. INDEXES UNTUK OPTIMASI
-- =====================================================

-- Indexes untuk users
CREATE INDEX IF NOT EXISTS idx_users_email ON users (email);
CREATE INDEX IF NOT EXISTS idx_users_username ON users (username);
CREATE INDEX IF NOT EXISTS idx_users_role ON users (role);

-- Indexes untuk meeting_rooms
CREATE INDEX IF NOT EXISTS idx_meeting_rooms_name ON meeting_rooms (name);
CREATE INDEX IF NOT EXISTS idx_meeting_rooms_location ON meeting_rooms (location);
CREATE INDEX IF NOT EXISTS idx_meeting_rooms_is_active ON meeting_rooms (is_active);
CREATE INDEX IF NOT EXISTS idx_meeting_rooms_capacity ON meeting_rooms (capacity);

-- Indexes untuk bookings
CREATE INDEX IF NOT EXISTS idx_bookings_user_id ON bookings (user_id);
CREATE INDEX IF NOT EXISTS idx_bookings_meeting_room_id ON bookings (meeting_room_id);
CREATE INDEX IF NOT EXISTS idx_bookings_status ON bookings (status);
CREATE INDEX IF NOT EXISTS idx_bookings_start_time ON bookings (start_time);
CREATE INDEX IF NOT EXISTS idx_bookings_end_time ON bookings (end_time);
CREATE INDEX IF NOT EXISTS idx_bookings_room_time ON bookings (meeting_room_id, start_time, end_time);
CREATE INDEX IF NOT EXISTS idx_bookings_user_time ON bookings (user_id, start_time);
CREATE INDEX IF NOT EXISTS idx_bookings_unit_kerja ON bookings (unit_kerja);

-- =====================================================
-- KOMENTAR AKHIR
-- =====================================================

/*
TABEL YANG DIPERLUKAN:

✅ TABEL YANG SUDAH ADA (perlu diubah):
   - bookings (tambah kolom unit_kerja, dokumen_perizinan)
   - users (tambah kolom username, full_name, role, dll)
   - meeting_rooms (hapus hourly_rate, tambah amenities, is_active)

✅ TABEL LARAVEL YANG DIPERLUKAN:
   - password_reset_tokens
   - sessions
   - cache
   - cache_locks
   - jobs
   - job_batches
   - failed_jobs
   - admin_sessions

CARA PENGGUNAAN:
1. Pilih database yang sudah ada di phpMyAdmin
2. Jalankan script ini untuk menambahkan kolom yang hilang
3. Setelah selesai, database akan siap digunakan dengan fitur terbaru

FITUR YANG DITAMBAHKAN:
- Kolom unit_kerja dan dokumen_perizinan di bookings
- Kolom username, full_name, role di users
- Penghapusan kolom hourly_rate (pricing)
- Kolom amenities dan is_active di meeting_rooms
- Tabel Laravel yang diperlukan untuk aplikasi
*/
