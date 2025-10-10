-- =====================================================
-- UPDATE DATABASE - SCRIPT CERDAS (CEK KOLOM DULU)
-- Database: MySQL/MariaDB
-- Versi: 2025-10-10
-- =====================================================

-- Pastikan sudah memilih database yang tepat
-- USE nama_database_anda;

-- =====================================================
-- TAMBAH KOLOM KE TABEL BOOKINGS (HANYA JIKA BELUM ADA)
-- =====================================================

-- Tambah kolom unit_kerja jika belum ada
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'bookings' 
     AND COLUMN_NAME = 'unit_kerja') = 0,
    'ALTER TABLE bookings ADD COLUMN unit_kerja VARCHAR(255) NULL COMMENT ''Unit kerja pemesan'' AFTER special_requirements',
    'SELECT ''Kolom unit_kerja sudah ada'' as status'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Tambah kolom dokumen_perizinan jika belum ada
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'bookings' 
     AND COLUMN_NAME = 'dokumen_perizinan') = 0,
    'ALTER TABLE bookings ADD COLUMN dokumen_perizinan VARCHAR(255) NULL COMMENT ''Path file dokumen perizinan PDF'' AFTER unit_kerja',
    'SELECT ''Kolom dokumen_perizinan sudah ada'' as status'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- TAMBAH KOLOM KE TABEL USERS (HANYA JIKA BELUM ADA)
-- =====================================================

-- Tambah kolom username jika belum ada
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'users' 
     AND COLUMN_NAME = 'username') = 0,
    'ALTER TABLE users ADD COLUMN username VARCHAR(255) UNIQUE AFTER id',
    'SELECT ''Kolom username sudah ada'' as status'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Tambah kolom full_name jika belum ada
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'users' 
     AND COLUMN_NAME = 'full_name') = 0,
    'ALTER TABLE users ADD COLUMN full_name VARCHAR(255) AFTER username',
    'SELECT ''Kolom full_name sudah ada'' as status'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Tambah kolom phone jika belum ada
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'users' 
     AND COLUMN_NAME = 'phone') = 0,
    'ALTER TABLE users ADD COLUMN phone VARCHAR(20) NULL AFTER full_name',
    'SELECT ''Kolom phone sudah ada'' as status'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Tambah kolom department jika belum ada
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'users' 
     AND COLUMN_NAME = 'department') = 0,
    'ALTER TABLE users ADD COLUMN department VARCHAR(100) NULL AFTER phone',
    'SELECT ''Kolom department sudah ada'' as status'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Tambah kolom role jika belum ada
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'users' 
     AND COLUMN_NAME = 'role') = 0,
    'ALTER TABLE users ADD COLUMN role ENUM(''admin'', ''user'') NOT NULL DEFAULT ''user'' AFTER department',
    'SELECT ''Kolom role sudah ada'' as status'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Tambah kolom avatar jika belum ada
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'users' 
     AND COLUMN_NAME = 'avatar') = 0,
    'ALTER TABLE users ADD COLUMN avatar VARCHAR(255) NULL AFTER role',
    'SELECT ''Kolom avatar sudah ada'' as status'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Tambah kolom last_login_at jika belum ada
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'users' 
     AND COLUMN_NAME = 'last_login_at') = 0,
    'ALTER TABLE users ADD COLUMN last_login_at TIMESTAMP NULL AFTER avatar',
    'SELECT ''Kolom last_login_at sudah ada'' as status'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Tambah kolom email_verification_token jika belum ada
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'users' 
     AND COLUMN_NAME = 'email_verification_token') = 0,
    'ALTER TABLE users ADD COLUMN email_verification_token VARCHAR(255) NULL AFTER email_verified_at',
    'SELECT ''Kolom email_verification_token sudah ada'' as status'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- UPDATE TABEL MEETING_ROOMS
-- =====================================================

-- Hapus kolom hourly_rate jika ada (karena sudah tidak digunakan)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'meeting_rooms' 
     AND COLUMN_NAME = 'hourly_rate') > 0,
    'ALTER TABLE meeting_rooms DROP COLUMN hourly_rate',
    'SELECT ''Kolom hourly_rate sudah tidak ada'' as status'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Tambah kolom amenities jika belum ada
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'meeting_rooms' 
     AND COLUMN_NAME = 'amenities') = 0,
    'ALTER TABLE meeting_rooms ADD COLUMN amenities JSON NULL COMMENT ''Array fasilitas'' AFTER capacity',
    'SELECT ''Kolom amenities sudah ada'' as status'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Tambah kolom images jika belum ada
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'meeting_rooms' 
     AND COLUMN_NAME = 'images') = 0,
    'ALTER TABLE meeting_rooms ADD COLUMN images JSON NULL COMMENT ''Array path gambar'' AFTER location',
    'SELECT ''Kolom images sudah ada'' as status'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Tambah kolom is_active jika belum ada
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'meeting_rooms' 
     AND COLUMN_NAME = 'is_active') = 0,
    'ALTER TABLE meeting_rooms ADD COLUMN is_active BOOLEAN NOT NULL DEFAULT TRUE AFTER images',
    'SELECT ''Kolom is_active sudah ada'' as status'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- TAMBAH KOLOM KE TABEL BOOKINGS (jika belum ada)
-- =====================================================

-- Tambah kolom attendees jika belum ada
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'bookings' 
     AND COLUMN_NAME = 'attendees') = 0,
    'ALTER TABLE bookings ADD COLUMN attendees JSON NULL COMMENT ''Array email peserta'' AFTER attendees_count',
    'SELECT ''Kolom attendees sudah ada'' as status'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Tambah kolom attachments jika belum ada
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'bookings' 
     AND COLUMN_NAME = 'attachments') = 0,
    'ALTER TABLE bookings ADD COLUMN attachments JSON NULL COMMENT ''Array file lampiran'' AFTER attendees',
    'SELECT ''Kolom attachments sudah ada'' as status'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Tambah kolom special_requirements jika belum ada
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'bookings' 
     AND COLUMN_NAME = 'special_requirements') = 0,
    'ALTER TABLE bookings ADD COLUMN special_requirements TEXT NULL AFTER attachments',
    'SELECT ''Kolom special_requirements sudah ada'' as status'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Tambah kolom cancelled_at jika belum ada
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'bookings' 
     AND COLUMN_NAME = 'cancelled_at') = 0,
    'ALTER TABLE bookings ADD COLUMN cancelled_at TIMESTAMP NULL AFTER total_cost',
    'SELECT ''Kolom cancelled_at sudah ada'' as status'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Tambah kolom cancellation_reason jika belum ada
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'bookings' 
     AND COLUMN_NAME = 'cancellation_reason') = 0,
    'ALTER TABLE bookings ADD COLUMN cancellation_reason TEXT NULL AFTER cancelled_at',
    'SELECT ''Kolom cancellation_reason sudah ada'' as status'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

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

✅ Mengecek apakah kolom sudah ada sebelum menambahkannya
✅ Menambahkan kolom unit_kerja dan dokumen_perizinan ke tabel bookings
✅ Menambahkan kolom yang hilang ke tabel users
✅ Menghapus kolom hourly_rate dari meeting_rooms (jika ada)
✅ Menambahkan kolom amenities, images, is_active ke meeting_rooms
✅ Memasukkan data sample yang aman dari duplicate

CARA PENGGUNAAN:
1. Pilih database yang sudah ada di phpMyAdmin
2. Jalankan script ini - akan otomatis cek kolom yang sudah ada
3. Setelah selesai, database akan siap digunakan dengan fitur terbaru

FITUR YANG DITAMBAHKAN:
- Kolom unit_kerja dan dokumen_perizinan di bookings
- Kolom username, full_name, dll di users
- Penghapusan kolom hourly_rate (pricing)
- Kolom amenities dan is_active di meeting_rooms
*/
