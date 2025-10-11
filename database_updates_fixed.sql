-- =====================================================
-- SQL Scripts for Meeting Room Booking System v2.0
-- Database Updates for New Features (FIXED VERSION)
-- =====================================================

-- =====================================================
-- 1. USER NOTIFICATIONS TABLE
-- =====================================================
-- Table untuk sistem notifikasi user (jika belum ada)
CREATE TABLE IF NOT EXISTS `user_notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `booking_id` bigint(20) unsigned DEFAULT NULL,
  `type` varchar(255) NOT NULL COMMENT 'booking_confirmed, booking_cancelled, booking_completed, room_maintenance',
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_notifications_user_id_foreign` (`user_id`),
  KEY `user_notifications_booking_id_foreign` (`booking_id`),
  KEY `user_notifications_user_id_is_read_index` (`user_id`, `is_read`),
  KEY `user_notifications_type_created_at_index` (`type`, `created_at`),
  CONSTRAINT `user_notifications_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. CHECK AND ADD COLUMNS TO BOOKINGS TABLE
-- =====================================================

-- Check if unit_kerja column exists, if not add it
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'bookings' 
     AND COLUMN_NAME = 'unit_kerja' 
     AND TABLE_SCHEMA = DATABASE()) = 0,
    'ALTER TABLE `bookings` ADD COLUMN `unit_kerja` varchar(255) DEFAULT NULL AFTER `special_requirements`',
    'SELECT "Column unit_kerja already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check if dokumen_perizinan column exists, if not add it
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'bookings' 
     AND COLUMN_NAME = 'dokumen_perizinan' 
     AND TABLE_SCHEMA = DATABASE()) = 0,
    'ALTER TABLE `bookings` ADD COLUMN `dokumen_perizinan` varchar(255) DEFAULT NULL AFTER `unit_kerja`',
    'SELECT "Column dokumen_perizinan already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- 3. CHECK AND ADD COLUMNS TO MEETING_ROOMS TABLE
-- =====================================================

-- Check if is_active column exists, if not add it
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'meeting_rooms' 
     AND COLUMN_NAME = 'is_active' 
     AND TABLE_SCHEMA = DATABASE()) = 0,
    'ALTER TABLE `meeting_rooms` ADD COLUMN `is_active` tinyint(1) NOT NULL DEFAULT 1 AFTER `images`',
    'SELECT "Column is_active already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- 4. CHECK AND ADD COLUMNS TO USERS TABLE
-- =====================================================

-- Check if google_id column exists, if not add it
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'users' 
     AND COLUMN_NAME = 'google_id' 
     AND TABLE_SCHEMA = DATABASE()) = 0,
    'ALTER TABLE `users` ADD COLUMN `google_id` varchar(255) DEFAULT NULL AFTER `email_verification_token`',
    'SELECT "Column google_id already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check if last_login_at column exists, if not add it
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'users' 
     AND COLUMN_NAME = 'last_login_at' 
     AND TABLE_SCHEMA = DATABASE()) = 0,
    'ALTER TABLE `users` ADD COLUMN `last_login_at` timestamp NULL DEFAULT NULL AFTER `avatar`',
    'SELECT "Column last_login_at already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check if email_verification_token column exists, if not add it
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'users' 
     AND COLUMN_NAME = 'email_verification_token' 
     AND TABLE_SCHEMA = DATABASE()) = 0,
    'ALTER TABLE `users` ADD COLUMN `email_verification_token` varchar(255) DEFAULT NULL AFTER `email_verified_at`',
    'SELECT "Column email_verification_token already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- 5. ADD INDEXES FOR PERFORMANCE (if not exists)
-- =====================================================

-- Add index for user notifications user_id and is_read
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_NAME = 'user_notifications' 
     AND INDEX_NAME = 'idx_user_notifications_user_read' 
     AND TABLE_SCHEMA = DATABASE()) = 0,
    'CREATE INDEX `idx_user_notifications_user_read` ON `user_notifications` (`user_id`, `is_read`)',
    'SELECT "Index idx_user_notifications_user_read already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index for user notifications type and created_at
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_NAME = 'user_notifications' 
     AND INDEX_NAME = 'idx_user_notifications_type_date' 
     AND TABLE_SCHEMA = DATABASE()) = 0,
    'CREATE INDEX `idx_user_notifications_type_date` ON `user_notifications` (`type`, `created_at`)',
    'SELECT "Index idx_user_notifications_type_date already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index for bookings status and time
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_NAME = 'bookings' 
     AND INDEX_NAME = 'idx_bookings_status_time' 
     AND TABLE_SCHEMA = DATABASE()) = 0,
    'CREATE INDEX `idx_bookings_status_time` ON `bookings` (`status`, `start_time`, `end_time`)',
    'SELECT "Index idx_bookings_status_time already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index for bookings user_id and status
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_NAME = 'bookings' 
     AND INDEX_NAME = 'idx_bookings_user_status' 
     AND TABLE_SCHEMA = DATABASE()) = 0,
    'CREATE INDEX `idx_bookings_user_status` ON `bookings` (`user_id`, `status`)',
    'SELECT "Index idx_bookings_user_status already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- 6. CREATE VIEWS FOR REPORTING (if not exists)
-- =====================================================

-- Create notification stats view
CREATE OR REPLACE VIEW `notification_stats` AS
SELECT 
    u.id as user_id,
    u.full_name,
    COUNT(n.id) as total_notifications,
    COUNT(CASE WHEN n.is_read = 0 THEN 1 END) as unread_notifications,
    COUNT(CASE WHEN n.type = 'booking_confirmed' THEN 1 END) as confirmed_notifications,
    COUNT(CASE WHEN n.type = 'booking_cancelled' THEN 1 END) as cancelled_notifications,
    COUNT(CASE WHEN n.type = 'room_maintenance' THEN 1 END) as maintenance_notifications
FROM users u
LEFT JOIN user_notifications n ON u.id = n.user_id
GROUP BY u.id, u.full_name;

-- Create booking notifications view
CREATE OR REPLACE VIEW `booking_notifications` AS
SELECT 
    b.id as booking_id,
    b.title,
    b.status,
    b.start_time,
    b.end_time,
    u.full_name as user_name,
    mr.name as room_name,
    n.type as notification_type,
    n.title as notification_title,
    n.message as notification_message,
    n.is_read,
    n.created_at as notification_created
FROM bookings b
LEFT JOIN users u ON b.user_id = u.id
LEFT JOIN meeting_rooms mr ON b.meeting_room_id = mr.id
LEFT JOIN user_notifications n ON b.id = n.booking_id
ORDER BY b.created_at DESC;

-- =====================================================
-- 7. SAMPLE DATA FOR TESTING (optional)
-- =====================================================

-- Insert sample notifications for testing (only if table is empty)
INSERT IGNORE INTO `user_notifications` (`user_id`, `booking_id`, `type`, `title`, `message`, `is_read`, `created_at`, `updated_at`) VALUES
(1, 1, 'booking_confirmed', 'Booking Dikonfirmasi', 'Meeting "Rapat Tim" telah dikonfirmasi. Anda akan menerima email pengingat 30 menit sebelum meeting dimulai.', 0, NOW(), NOW()),
(1, 2, 'room_maintenance', 'Ruang Meeting Dinonaktifkan', 'Meeting "Presentasi Proyek" dibatalkan karena ruang meeting sedang dalam maintenance. Silakan buat booking baru untuk ruang lain.', 0, NOW(), NOW()),
(2, 3, 'booking_cancelled', 'Booking Dibatalkan', 'Meeting "Review Proyek" telah dibatalkan. Alasan: Perubahan jadwal.', 1, NOW(), NOW()),
(2, 4, 'booking_completed', 'Meeting Selesai', 'Meeting "Training Tim" telah selesai. Terima kasih telah menggunakan layanan kami.', 1, NOW(), NOW());

-- =====================================================
-- 8. USEFUL QUERIES FOR TESTING
-- =====================================================

-- Check if all tables and columns exist
SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME IN ('users', 'bookings', 'meeting_rooms', 'user_notifications')
ORDER BY TABLE_NAME, ORDINAL_POSITION;

-- Check notification statistics
SELECT 
    type,
    COUNT(*) as total,
    COUNT(CASE WHEN is_read = 0 THEN 1 END) as unread,
    COUNT(CASE WHEN is_read = 1 THEN 1 END) as read_count
FROM user_notifications 
GROUP BY type;

-- Check user notification counts
SELECT 
    u.full_name,
    COUNT(n.id) as total_notifications,
    COUNT(CASE WHEN n.is_read = 0 THEN 1 END) as unread_count
FROM users u
LEFT JOIN user_notifications n ON u.id = n.user_id
GROUP BY u.id, u.full_name
ORDER BY total_notifications DESC;

-- Check bookings that need reminders (for email system)
SELECT 
    b.id,
    b.title,
    b.start_time,
    u.full_name,
    u.email,
    mr.name as room_name
FROM bookings b
JOIN users u ON b.user_id = u.id
JOIN meeting_rooms mr ON b.meeting_room_id = mr.id
WHERE b.status = 'confirmed'
AND b.start_time BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 30 MINUTE)
AND b.start_time > NOW();

-- =====================================================
-- 9. VERIFICATION QUERIES
-- =====================================================

-- Verify user_notifications table structure
DESCRIBE user_notifications;

-- Verify all indexes exist
SHOW INDEX FROM user_notifications;
SHOW INDEX FROM bookings;
SHOW INDEX FROM users;
SHOW INDEX FROM meeting_rooms;

-- Verify views exist
SHOW FULL TABLES WHERE Table_type = 'VIEW';

-- =====================================================
-- END OF SCRIPT
-- =====================================================
