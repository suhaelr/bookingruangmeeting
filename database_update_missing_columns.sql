-- =====================================================
-- UPDATE DATABASE - TAMBAH KOLOM YANG HILANG
-- Database: MySQL/MariaDB
-- Versi: 2025-10-10
-- =====================================================

-- Pastikan sudah memilih database yang tepat
-- USE nama_database_anda;

-- =====================================================
-- UPDATE TABEL USERS - Tambah kolom yang hilang
-- =====================================================

-- Tambah kolom username jika belum ada
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS username VARCHAR(255) UNIQUE AFTER id;

-- Tambah kolom full_name jika belum ada
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS full_name VARCHAR(255) AFTER username;

-- Tambah kolom phone jika belum ada
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS phone VARCHAR(20) NULL AFTER full_name;

-- Tambah kolom department jika belum ada
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS department VARCHAR(100) NULL AFTER phone;

-- Tambah kolom role jika belum ada
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
-- UPDATE TABEL MEETING_ROOMS - Hapus kolom pricing
-- =====================================================

-- Hapus kolom hourly_rate jika ada (karena sudah tidak digunakan)
ALTER TABLE meeting_rooms 
DROP COLUMN IF EXISTS hourly_rate;

-- Tambah kolom amenities jika belum ada
ALTER TABLE meeting_rooms 
ADD COLUMN IF NOT EXISTS amenities JSON NULL COMMENT 'Array fasilitas: ["projector", "whiteboard", "wifi", "ac"]' AFTER capacity;

-- Tambah kolom images jika belum ada
ALTER TABLE meeting_rooms 
ADD COLUMN IF NOT EXISTS images JSON NULL COMMENT 'Array path gambar: ["image1.jpg", "image2.jpg"]' AFTER location;

-- Tambah kolom is_active jika belum ada
ALTER TABLE meeting_rooms 
ADD COLUMN IF NOT EXISTS is_active BOOLEAN NOT NULL DEFAULT TRUE AFTER images;

-- =====================================================
-- UPDATE TABEL BOOKINGS - Tambah kolom baru
-- =====================================================

-- Tambah kolom unit_kerja jika belum ada
ALTER TABLE bookings 
ADD COLUMN IF NOT EXISTS unit_kerja VARCHAR(255) NULL COMMENT 'Unit kerja pemesan' AFTER special_requirements;

-- Tambah kolom dokumen_perizinan jika belum ada
ALTER TABLE bookings 
ADD COLUMN IF NOT EXISTS dokumen_perizinan VARCHAR(255) NULL COMMENT 'Path file dokumen perizinan PDF' AFTER unit_kerja;

-- Tambah kolom attendees jika belum ada
ALTER TABLE bookings 
ADD COLUMN IF NOT EXISTS attendees JSON NULL COMMENT 'Array email peserta: ["email1@example.com", "email2@example.com"]' AFTER attendees_count;

-- Tambah kolom attachments jika belum ada
ALTER TABLE bookings 
ADD COLUMN IF NOT EXISTS attachments JSON NULL COMMENT 'Array file lampiran: ["file1.pdf", "file2.docx"]' AFTER attendees;

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
-- TAMBAH TABEL YANG HILANG
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
-- TAMBAH INDEXES UNTUK OPTIMASI
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
-- DATA SAMPLE (Data Contoh) - AMAN DARI DUPLICATE
-- =====================================================

-- Insert admin user (jika belum ada)
INSERT IGNORE INTO users (id, username, name, full_name, email, password, role, department) VALUES
(1, 'admin', 'Administrator', 'Super Administrator', 'admin@bgn.co.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'IT');

-- Insert sample users (jika belum ada)
INSERT IGNORE INTO users (id, username, name, full_name, email, password, role, department) VALUES
(2, 'john.doe', 'John Doe', 'John Doe', 'john.doe@bgn.co.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'HR'),
(3, 'jane.smith', 'Jane Smith', 'Jane Smith', 'jane.smith@bgn.co.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'Finance'),
(4, 'bob.wilson', 'Bob Wilson', 'Bob Wilson', 'bob.wilson@bgn.co.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'Marketing');

-- Insert sample meeting rooms (jika belum ada)
INSERT IGNORE INTO meeting_rooms (id, name, description, capacity, amenities, location, is_active) VALUES
(1, 'Ruang Rapat Utama', 'Ruang rapat utama dengan kapasitas besar untuk meeting penting', 20, '["projector", "whiteboard", "wifi", "ac", "sound_system"]', 'Lantai 1 - Gedung A', TRUE),
(2, 'Ruang Meeting Kecil', 'Ruang meeting kecil untuk diskusi tim', 8, '["whiteboard", "wifi", "ac"]', 'Lantai 2 - Gedung A', TRUE),
(3, 'Ruang Konferensi', 'Ruang konferensi dengan fasilitas lengkap', 30, '["projector", "whiteboard", "wifi", "ac", "sound_system", "video_conference"]', 'Lantai 3 - Gedung B', TRUE),
(4, 'Ruang Diskusi', 'Ruang diskusi informal', 6, '["wifi", "ac"]', 'Lantai 1 - Gedung B', TRUE);

-- Insert sample bookings (jika belum ada)
INSERT IGNORE INTO bookings (id, user_id, meeting_room_id, title, description, start_time, end_time, status, attendees_count, attendees, special_requirements, unit_kerja, total_cost) VALUES
(1, 2, 1, 'Rapat Tim HR', 'Rapat bulanan tim HR', '2025-10-15 09:00:00', '2025-10-15 11:00:00', 'confirmed', 5, '["john.doe@bgn.co.id", "jane.smith@bgn.co.id"]', 'Perlu flipchart', 'HR', 0.00),
(2, 3, 2, 'Review Budget', 'Review budget Q4', '2025-10-16 14:00:00', '2025-10-16 16:00:00', 'pending', 3, '["jane.smith@bgn.co.id", "bob.wilson@bgn.co.id"]', 'Perlu kalkulator', 'Finance', 0.00),
(3, 4, 3, 'Presentasi Marketing', 'Presentasi strategi marketing 2025', '2025-10-17 10:00:00', '2025-10-17 12:00:00', 'confirmed', 15, '["bob.wilson@bgn.co.id"]', 'Perlu laptop dan proyektor', 'Marketing', 0.00);

-- =====================================================
-- KOMENTAR AKHIR
-- =====================================================

/*
SCRIPT INI AKAN:

✅ Menambahkan kolom yang hilang ke tabel yang sudah ada
✅ Menghapus kolom pricing yang tidak digunakan
✅ Membuat tabel Laravel yang hilang
✅ Menambahkan indexes untuk optimasi
✅ Memasukkan data sample yang aman dari duplicate

CARA PENGGUNAAN:
1. Pilih database yang sudah ada di phpMyAdmin
2. Jalankan script ini untuk update schema
3. Setelah selesai, database akan siap digunakan dengan fitur terbaru

FITUR YANG DITAMBAHKAN:
- Kolom unit_kerja dan dokumen_perizinan di bookings
- Kolom username, full_name, dll di users
- Penghapusan kolom hourly_rate (pricing)
- Tabel Laravel yang diperlukan
- Indexes untuk performa optimal
*/
