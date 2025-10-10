-- =====================================================
-- SISTEM PEMESANAN RUANG MEETING - SCHEMA TERBARU
-- Database: MySQL/MariaDB
-- Versi: 2025-10-10
-- =====================================================

-- Catatan: Pastikan sudah memilih database yang tepat sebelum menjalankan script ini
-- USE nama_database_anda;

-- =====================================================
-- TABEL USERS (Pengguna)
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    email_verification_token VARCHAR(255) NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NULL,
    department VARCHAR(100) NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    avatar VARCHAR(255) NULL,
    last_login_at TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_users_email (email),
    INDEX idx_users_username (username),
    INDEX idx_users_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL PASSWORD RESET TOKENS
-- =====================================================
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL SESSIONS
-- =====================================================
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

-- =====================================================
-- TABEL MEETING ROOMS (Ruang Meeting)
-- =====================================================
CREATE TABLE IF NOT EXISTS meeting_rooms (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    capacity INTEGER NOT NULL,
    amenities JSON NULL COMMENT 'Array fasilitas: ["projector", "whiteboard", "wifi", "ac"]',
    location VARCHAR(255) NOT NULL,
    images JSON NULL COMMENT 'Array path gambar: ["image1.jpg", "image2.jpg"]',
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_meeting_rooms_name (name),
    INDEX idx_meeting_rooms_location (location),
    INDEX idx_meeting_rooms_is_active (is_active),
    INDEX idx_meeting_rooms_capacity (capacity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL BOOKINGS (Pemesanan)
-- =====================================================
CREATE TABLE IF NOT EXISTS bookings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    meeting_room_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') NOT NULL DEFAULT 'pending',
    attendees_count INTEGER NOT NULL DEFAULT 1,
    attendees JSON NULL COMMENT 'Array email peserta: ["email1@example.com", "email2@example.com"]',
    attachments JSON NULL COMMENT 'Array file lampiran: ["file1.pdf", "file2.docx"]',
    special_requirements TEXT NULL,
    unit_kerja VARCHAR(255) NULL COMMENT 'Unit kerja pemesan',
    dokumen_perizinan VARCHAR(255) NULL COMMENT 'Path file dokumen perizinan PDF',
    total_cost DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    cancelled_at TIMESTAMP NULL,
    cancellation_reason TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Keys
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (meeting_room_id) REFERENCES meeting_rooms(id) ON DELETE CASCADE,
    
    -- Indexes untuk optimasi query
    INDEX idx_bookings_user_id (user_id),
    INDEX idx_bookings_meeting_room_id (meeting_room_id),
    INDEX idx_bookings_status (status),
    INDEX idx_bookings_start_time (start_time),
    INDEX idx_bookings_end_time (end_time),
    INDEX idx_bookings_room_time (meeting_room_id, start_time, end_time),
    INDEX idx_bookings_user_time (user_id, start_time),
    INDEX idx_bookings_unit_kerja (unit_kerja)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL CACHE (Laravel Cache)
-- =====================================================
CREATE TABLE IF NOT EXISTS cache (
    `key` VARCHAR(255) PRIMARY KEY,
    value MEDIUMTEXT NOT NULL,
    expiration INTEGER NOT NULL,
    
    INDEX idx_cache_expiration (expiration)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL CACHE LOCKS (Laravel Cache Locks)
-- =====================================================
CREATE TABLE IF NOT EXISTS cache_locks (
    `key` VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration INTEGER NOT NULL,
    
    INDEX idx_cache_locks_expiration (expiration)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL JOBS (Laravel Queue Jobs)
-- =====================================================
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

-- =====================================================
-- TABEL JOB BATCHES (Laravel Queue Job Batches)
-- =====================================================
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

-- =====================================================
-- TABEL FAILED JOBS (Laravel Queue Failed Jobs)
-- =====================================================
CREATE TABLE IF NOT EXISTS failed_jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(255) NOT NULL UNIQUE,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload LONGTEXT NOT NULL,
    exception LONGTEXT NOT NULL,
    failed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL ADMIN SESSIONS (Sesi Admin)
-- =====================================================
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
-- VIEWS (Tampilan)
-- =====================================================

-- View untuk statistik dashboard admin
CREATE OR REPLACE VIEW admin_dashboard_stats AS
SELECT 
    (SELECT COUNT(*) FROM users) as total_users,
    (SELECT COUNT(*) FROM meeting_rooms) as total_rooms,
    (SELECT COUNT(*) FROM bookings) as total_bookings,
    (SELECT COUNT(*) FROM bookings WHERE status = 'pending') as pending_bookings,
    (SELECT COUNT(*) FROM bookings WHERE status = 'confirmed') as confirmed_bookings,
    (SELECT COUNT(*) FROM bookings WHERE status = 'cancelled') as cancelled_bookings,
    (SELECT COUNT(*) FROM meeting_rooms WHERE is_active = TRUE) as active_rooms;

-- View untuk booking dengan detail lengkap
CREATE OR REPLACE VIEW booking_details AS
SELECT 
    b.id,
    b.title,
    b.description,
    b.start_time,
    b.end_time,
    b.status,
    b.attendees_count,
    b.attendees,
    b.special_requirements,
    b.unit_kerja,
    b.dokumen_perizinan,
    b.total_cost,
    b.created_at,
    u.full_name as user_name,
    u.email as user_email,
    u.department as user_department,
    mr.name as room_name,
    mr.capacity as room_capacity,
    mr.location as room_location,
    mr.amenities as room_amenities
FROM bookings b
JOIN users u ON b.user_id = u.id
JOIN meeting_rooms mr ON b.meeting_room_id = mr.id;

-- =====================================================
-- INDEXES TAMBAHAN UNTUK OPTIMASI
-- =====================================================

-- Index untuk pencarian booking berdasarkan tanggal
CREATE INDEX IF NOT EXISTS idx_bookings_date_range ON bookings (start_time, end_time);

-- Index untuk pencarian booking berdasarkan status dan tanggal
CREATE INDEX IF NOT EXISTS idx_bookings_status_date ON bookings (status, start_time);

-- Index untuk pencarian room berdasarkan kapasitas dan status
CREATE INDEX IF NOT EXISTS idx_rooms_capacity_active ON meeting_rooms (capacity, is_active);

-- =====================================================
-- KOMENTAR AKHIR
-- =====================================================

/*
SCHEMA INI SUDAH TERUPDATE DENGAN:

✅ Penghapusan fitur pricing (hourly_rate di meeting_rooms)
✅ Penambahan field unit_kerja dan dokumen_perizinan di bookings
✅ Email verification untuk users
✅ Admin sessions terpisah
✅ Indexes untuk optimasi performa
✅ Views untuk dashboard stats
✅ Sample data untuk testing

FITUR UTAMA:
- Sistem autentikasi lengkap (login, register, email verification)
- Manajemen user dengan role (admin/user)
- Manajemen ruang meeting dengan fasilitas
- Sistem pemesanan dengan dokumen perizinan
- Dashboard dengan statistik real-time
- Optimasi performa dengan indexes dan views

CARA PENGGUNAAN:
1. Pilih database yang sudah ada di phpMyAdmin
2. Import file ini ke database tersebut
3. Atau jalankan script ini di database yang sudah ada
4. Pastikan user database memiliki hak akses CREATE TABLE, INSERT, dll
*/