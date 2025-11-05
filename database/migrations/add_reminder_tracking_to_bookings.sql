-- Migration: Add Reminder Tracking Fields to Bookings Table
-- Date: 2025-11-05
-- Description: Menambahkan field tracking untuk email reminder yang sudah dikirim
--              Field ini digunakan untuk mencegah pengiriman email reminder duplikat
--              dan mengirim reminder berbeda di waktu berbeda (1 jam, 30 menit, 15 menit)

-- ============================================
-- UP Migration: Tambah field reminder tracking
-- ============================================

ALTER TABLE `bookings`
ADD COLUMN `reminder_1h_sent` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Flag apakah reminder 1 jam sudah dikirim' AFTER `reschedule_deadline_at`,
ADD COLUMN `reminder_30m_sent` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Flag apakah reminder 30 menit sudah dikirim' AFTER `reminder_1h_sent`,
ADD COLUMN `reminder_15m_sent` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Flag apakah reminder 15 menit sudah dikirim' AFTER `reminder_30m_sent`,
ADD COLUMN `reminder_1h_sent_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Waktu reminder 1 jam dikirim' AFTER `reminder_15m_sent`,
ADD COLUMN `reminder_30m_sent_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Waktu reminder 30 menit dikirim' AFTER `reminder_1h_sent_at`,
ADD COLUMN `reminder_15m_sent_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Waktu reminder 15 menit dikirim' AFTER `reminder_30m_sent_at`;

-- ============================================
-- DOWN Migration: Hapus field reminder tracking
-- ============================================

-- Jika perlu rollback, jalankan query berikut:
-- ALTER TABLE `bookings`
-- DROP COLUMN `reminder_1h_sent`,
-- DROP COLUMN `reminder_30m_sent`,
-- DROP COLUMN `reminder_15m_sent`,
-- DROP COLUMN `reminder_1h_sent_at`,
-- DROP COLUMN `reminder_30m_sent_at`,
-- DROP COLUMN `reminder_15m_sent_at`;

-- ============================================
-- Verifikasi: Cek struktur tabel setelah migration
-- ============================================

-- Jalankan query ini untuk memverifikasi field sudah ditambahkan:
-- DESCRIBE `bookings`;

-- Atau untuk melihat semua field reminder:
-- SELECT 
--     COLUMN_NAME, 
--     DATA_TYPE, 
--     IS_NULLABLE, 
--     COLUMN_DEFAULT,
--     COLUMN_COMMENT
-- FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_SCHEMA = DATABASE() 
--   AND TABLE_NAME = 'bookings' 
--   AND COLUMN_NAME LIKE 'reminder%'
-- ORDER BY ORDINAL_POSITION;

