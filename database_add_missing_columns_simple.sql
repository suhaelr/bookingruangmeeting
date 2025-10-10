-- =====================================================
-- TAMBAH KOLOM YANG HILANG - SCRIPT SEDERHANA
-- Database: MySQL/MariaDB
-- Versi: 2025-10-10
-- =====================================================

-- Pastikan sudah memilih database yang tepat
-- USE nama_database_anda;

-- =====================================================
-- TAMBAH KOLOM KE TABEL BOOKINGS
-- =====================================================

-- Tambah kolom unit_kerja jika belum ada
ALTER TABLE bookings 
ADD COLUMN unit_kerja VARCHAR(255) NULL COMMENT 'Unit kerja pemesan' AFTER special_requirements;

-- Tambah kolom dokumen_perizinan jika belum ada
ALTER TABLE bookings 
ADD COLUMN dokumen_perizinan VARCHAR(255) NULL COMMENT 'Path file dokumen perizinan PDF' AFTER unit_kerja;

-- =====================================================
-- TAMBAH KOLOM KE TABEL USERS (jika belum ada)
-- =====================================================

-- Tambah kolom username jika belum ada
ALTER TABLE users 
ADD COLUMN username VARCHAR(255) UNIQUE AFTER id;

-- Tambah kolom full_name jika belum ada
ALTER TABLE users 
ADD COLUMN full_name VARCHAR(255) AFTER username;

-- Tambah kolom phone jika belum ada
ALTER TABLE users 
ADD COLUMN phone VARCHAR(20) NULL AFTER full_name;

-- Tambah kolom department jika belum ada
ALTER TABLE users 
ADD COLUMN department VARCHAR(100) NULL AFTER phone;

-- Tambah kolom role jika belum ada
ALTER TABLE users 
ADD COLUMN role ENUM('admin', 'user') NOT NULL DEFAULT 'user' AFTER department;

-- Tambah kolom avatar jika belum ada
ALTER TABLE users 
ADD COLUMN avatar VARCHAR(255) NULL AFTER role;

-- Tambah kolom last_login_at jika belum ada
ALTER TABLE users 
ADD COLUMN last_login_at TIMESTAMP NULL AFTER avatar;

-- Tambah kolom email_verification_token jika belum ada
ALTER TABLE users 
ADD COLUMN email_verification_token VARCHAR(255) NULL AFTER email_verified_at;

-- =====================================================
-- UPDATE TABEL MEETING_ROOMS
-- =====================================================

-- Hapus kolom hourly_rate jika ada (karena sudah tidak digunakan)
ALTER TABLE meeting_rooms 
DROP COLUMN IF EXISTS hourly_rate;

-- Tambah kolom amenities jika belum ada
ALTER TABLE meeting_rooms 
ADD COLUMN amenities JSON NULL COMMENT 'Array fasilitas' AFTER capacity;

-- Tambah kolom images jika belum ada
ALTER TABLE meeting_rooms 
ADD COLUMN images JSON NULL COMMENT 'Array path gambar' AFTER location;

-- Tambah kolom is_active jika belum ada
ALTER TABLE meeting_rooms 
ADD COLUMN is_active BOOLEAN NOT NULL DEFAULT TRUE AFTER images;

-- =====================================================
-- TAMBAH KOLOM KE TABEL BOOKINGS (jika belum ada)
-- =====================================================

-- Tambah kolom attendees jika belum ada
ALTER TABLE bookings 
ADD COLUMN attendees JSON NULL COMMENT 'Array email peserta' AFTER attendees_count;

-- Tambah kolom attachments jika belum ada
ALTER TABLE bookings 
ADD COLUMN attachments JSON NULL COMMENT 'Array file lampiran' AFTER attendees;

-- Tambah kolom special_requirements jika belum ada
ALTER TABLE bookings 
ADD COLUMN special_requirements TEXT NULL AFTER attachments;

-- Tambah kolom cancelled_at jika belum ada
ALTER TABLE bookings 
ADD COLUMN cancelled_at TIMESTAMP NULL AFTER total_cost;

-- Tambah kolom cancellation_reason jika belum ada
ALTER TABLE bookings 
ADD COLUMN cancellation_reason TEXT NULL AFTER cancelled_at;

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

✅ Menambahkan kolom unit_kerja dan dokumen_perizinan ke tabel bookings
✅ Menambahkan kolom yang hilang ke tabel users
✅ Menghapus kolom hourly_rate dari meeting_rooms
✅ Menambahkan kolom amenities, images, is_active ke meeting_rooms
✅ Memasukkan data sample yang aman dari duplicate

CARA PENGGUNAAN:
1. Pilih database yang sudah ada di phpMyAdmin
2. Jalankan script ini untuk menambahkan kolom yang hilang
3. Setelah selesai, database akan siap digunakan dengan fitur terbaru

FITUR YANG DITAMBAHKAN:
- Kolom unit_kerja dan dokumen_perizinan di bookings
- Kolom username, full_name, dll di users
- Penghapusan kolom hourly_rate (pricing)
- Kolom amenities dan is_active di meeting_rooms
*/
