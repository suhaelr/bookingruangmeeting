-- =====================================================
-- SQL Migration: Add Reminder Tracking Fields to Bookings Table
-- Date: 2025-11-05
-- Description: Menambahkan field tracking untuk email reminder yang sudah dikirim
--              Field ini digunakan untuk mencegah pengiriman email reminder duplikat
--              dan mengirim reminder berbeda di waktu berbeda (1 jam, 30 menit, 15 menit)
-- =====================================================

-- =====================================================
-- UP Migration: Tambah field reminder tracking
-- =====================================================

-- Check if reminder_1h_sent column exists, if not add it
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'bookings' 
     AND COLUMN_NAME = 'reminder_1h_sent' 
     AND TABLE_SCHEMA = DATABASE()) = 0,
    'ALTER TABLE `bookings` ADD COLUMN `reminder_1h_sent` TINYINT(1) NOT NULL DEFAULT 0 COMMENT ''Flag apakah reminder 1 jam sudah dikirim'' AFTER `reschedule_deadline_at`',
    'SELECT "Column reminder_1h_sent already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check if reminder_30m_sent column exists, if not add it
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'bookings' 
     AND COLUMN_NAME = 'reminder_30m_sent' 
     AND TABLE_SCHEMA = DATABASE()) = 0,
    'ALTER TABLE `bookings` ADD COLUMN `reminder_30m_sent` TINYINT(1) NOT NULL DEFAULT 0 COMMENT ''Flag apakah reminder 30 menit sudah dikirim'' AFTER `reminder_1h_sent`',
    'SELECT "Column reminder_30m_sent already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check if reminder_15m_sent column exists, if not add it
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'bookings' 
     AND COLUMN_NAME = 'reminder_15m_sent' 
     AND TABLE_SCHEMA = DATABASE()) = 0,
    'ALTER TABLE `bookings` ADD COLUMN `reminder_15m_sent` TINYINT(1) NOT NULL DEFAULT 0 COMMENT ''Flag apakah reminder 15 menit sudah dikirim'' AFTER `reminder_30m_sent`',
    'SELECT "Column reminder_15m_sent already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check if reminder_1h_sent_at column exists, if not add it
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'bookings' 
     AND COLUMN_NAME = 'reminder_1h_sent_at' 
     AND TABLE_SCHEMA = DATABASE()) = 0,
    'ALTER TABLE `bookings` ADD COLUMN `reminder_1h_sent_at` TIMESTAMP NULL DEFAULT NULL COMMENT ''Waktu reminder 1 jam dikirim'' AFTER `reminder_15m_sent`',
    'SELECT "Column reminder_1h_sent_at already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check if reminder_30m_sent_at column exists, if not add it
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'bookings' 
     AND COLUMN_NAME = 'reminder_30m_sent_at' 
     AND TABLE_SCHEMA = DATABASE()) = 0,
    'ALTER TABLE `bookings` ADD COLUMN `reminder_30m_sent_at` TIMESTAMP NULL DEFAULT NULL COMMENT ''Waktu reminder 30 menit dikirim'' AFTER `reminder_1h_sent_at`',
    'SELECT "Column reminder_30m_sent_at already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check if reminder_15m_sent_at column exists, if not add it
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'bookings' 
     AND COLUMN_NAME = 'reminder_15m_sent_at' 
     AND TABLE_SCHEMA = DATABASE()) = 0,
    'ALTER TABLE `bookings` ADD COLUMN `reminder_15m_sent_at` TIMESTAMP NULL DEFAULT NULL COMMENT ''Waktu reminder 15 menit dikirim'' AFTER `reminder_30m_sent_at`',
    'SELECT "Column reminder_15m_sent_at already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- Verifikasi: Cek struktur tabel setelah migration
-- =====================================================

-- Jalankan query ini untuk memverifikasi field sudah ditambahkan:
-- DESCRIBE `bookings`;

-- Atau untuk melihat semua field reminder:
SELECT 
    COLUMN_NAME, 
    DATA_TYPE, 
    IS_NULLABLE, 
    COLUMN_DEFAULT,
    COLUMN_COMMENT,
    ORDINAL_POSITION
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'bookings' 
  AND COLUMN_NAME LIKE 'reminder%'
ORDER BY ORDINAL_POSITION;

-- =====================================================
-- DOWN Migration: Hapus field reminder tracking (jika perlu rollback)
-- =====================================================

-- Jika perlu rollback, jalankan query berikut:
/*
ALTER TABLE `bookings`
DROP COLUMN IF EXISTS `reminder_1h_sent`,
DROP COLUMN IF EXISTS `reminder_30m_sent`,
DROP COLUMN IF EXISTS `reminder_15m_sent`,
DROP COLUMN IF EXISTS `reminder_1h_sent_at`,
DROP COLUMN IF EXISTS `reminder_30m_sent_at`,
DROP COLUMN IF EXISTS `reminder_15m_sent_at`;
*/

