-- =====================================================
-- SQL Scripts for Meeting Room Booking System v2.0
-- Database Updates for New Features
-- =====================================================

-- =====================================================
-- 1. USER NOTIFICATIONS TABLE
-- =====================================================
-- Table untuk sistem notifikasi user
CREATE TABLE `user_notifications` (
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
-- 2. UPDATE BOOKINGS TABLE (if needed)
-- =====================================================
-- Pastikan kolom yang sudah ada di tabel bookings
-- Jika belum ada, tambahkan kolom berikut:

-- Kolom untuk unit kerja (jika belum ada)
ALTER TABLE `bookings` 
ADD COLUMN `unit_kerja` varchar(255) DEFAULT NULL AFTER `special_requirements`;

-- Kolom untuk dokumen perizinan (jika belum ada)
ALTER TABLE `bookings` 
ADD COLUMN `dokumen_perizinan` varchar(255) DEFAULT NULL AFTER `unit_kerja`;

-- =====================================================
-- 3. UPDATE MEETING ROOMS TABLE (if needed)
-- =====================================================
-- Pastikan kolom is_active ada di tabel meeting_rooms
-- Jika belum ada, tambahkan kolom berikut:

-- Kolom untuk status aktif ruang meeting
ALTER TABLE `meeting_rooms` 
ADD COLUMN `is_active` tinyint(1) NOT NULL DEFAULT 1 AFTER `images`;

-- =====================================================
-- 4. UPDATE USERS TABLE (if needed)
-- =====================================================
-- Pastikan kolom yang sudah ada di tabel users
-- Jika belum ada, tambahkan kolom berikut:

-- Kolom untuk Google ID (jika belum ada)
ALTER TABLE `users` 
ADD COLUMN `google_id` varchar(255) DEFAULT NULL AFTER `email_verification_token`;

-- Kolom untuk last login (jika belum ada)
ALTER TABLE `users` 
ADD COLUMN `last_login_at` timestamp NULL DEFAULT NULL AFTER `avatar`;

-- Kolom untuk email verification (jika belum ada)
ALTER TABLE `users` 
ADD COLUMN `email_verification_token` varchar(255) DEFAULT NULL AFTER `email_verified_at`;

-- =====================================================
-- 5. SAMPLE DATA FOR TESTING
-- =====================================================

-- Insert sample notification types
INSERT INTO `user_notifications` (`user_id`, `booking_id`, `type`, `title`, `message`, `is_read`, `created_at`, `updated_at`) VALUES
(1, 1, 'booking_confirmed', 'Booking Dikonfirmasi', 'Meeting "Rapat Tim" telah dikonfirmasi. Anda akan menerima email pengingat 30 menit sebelum meeting dimulai.', 0, NOW(), NOW()),
(1, 2, 'room_maintenance', 'Ruang Meeting Dinonaktifkan', 'Meeting "Presentasi Proyek" dibatalkan karena ruang meeting sedang dalam maintenance. Silakan buat booking baru untuk ruang lain.', 0, NOW(), NOW());

-- =====================================================
-- 6. INDEXES FOR PERFORMANCE
-- =====================================================

-- Index untuk pencarian notifikasi berdasarkan user dan status
CREATE INDEX `idx_user_notifications_user_read` ON `user_notifications` (`user_id`, `is_read`);

-- Index untuk pencarian notifikasi berdasarkan tipe dan tanggal
CREATE INDEX `idx_user_notifications_type_date` ON `user_notifications` (`type`, `created_at`);

-- Index untuk pencarian booking berdasarkan status dan waktu
CREATE INDEX `idx_bookings_status_time` ON `bookings` (`status`, `start_time`, `end_time`);

-- Index untuk pencarian booking berdasarkan user dan status
CREATE INDEX `idx_bookings_user_status` ON `bookings` (`user_id`, `status`);

-- =====================================================
-- 7. VIEWS FOR REPORTING
-- =====================================================

-- View untuk statistik notifikasi
CREATE VIEW `notification_stats` AS
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

-- View untuk booking dengan notifikasi
CREATE VIEW `booking_notifications` AS
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
-- 8. STORED PROCEDURES FOR AUTOMATION
-- =====================================================

-- Procedure untuk update status booking otomatis
DELIMITER //
CREATE PROCEDURE `UpdateCompletedBookings`()
BEGIN
    UPDATE bookings 
    SET status = 'completed', 
        updated_at = NOW()
    WHERE status = 'confirmed' 
    AND end_time < NOW();
    
    SELECT ROW_COUNT() as updated_count;
END //
DELIMITER ;

-- Procedure untuk cleanup notifikasi lama
DELIMITER //
CREATE PROCEDURE `CleanupOldNotifications`()
BEGIN
    DELETE FROM user_notifications 
    WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)
    AND is_read = 1;
    
    SELECT ROW_COUNT() as deleted_count;
END //
DELIMITER ;

-- =====================================================
-- 9. TRIGGERS FOR AUTOMATION
-- =====================================================

-- Trigger untuk auto-create notification saat booking dibuat
DELIMITER //
CREATE TRIGGER `booking_created_notification` 
AFTER INSERT ON `bookings`
FOR EACH ROW
BEGIN
    INSERT INTO user_notifications (user_id, booking_id, type, title, message, created_at, updated_at)
    VALUES (NEW.user_id, NEW.id, 'booking_created', 'Booking Baru Dibuat', 
            CONCAT('Booking "', NEW.title, '" telah dibuat dan menunggu konfirmasi admin.'), 
            NOW(), NOW());
END //
DELIMITER ;

-- =====================================================
-- 10. CLEANUP AND MAINTENANCE QUERIES
-- =====================================================

-- Query untuk melihat statistik notifikasi
SELECT 
    type,
    COUNT(*) as total,
    COUNT(CASE WHEN is_read = 0 THEN 1 END) as unread,
    COUNT(CASE WHEN is_read = 1 THEN 1 END) as read_count
FROM user_notifications 
GROUP BY type;

-- Query untuk melihat user dengan notifikasi terbanyak
SELECT 
    u.full_name,
    COUNT(n.id) as total_notifications,
    COUNT(CASE WHEN n.is_read = 0 THEN 1 END) as unread_count
FROM users u
LEFT JOIN user_notifications n ON u.id = n.user_id
GROUP BY u.id, u.full_name
ORDER BY total_notifications DESC;

-- Query untuk melihat booking yang perlu reminder
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
-- 11. ROLLBACK SCRIPTS (if needed)
-- =====================================================

-- Script untuk rollback jika diperlukan
/*
-- Drop triggers
DROP TRIGGER IF EXISTS `booking_created_notification`;

-- Drop procedures
DROP PROCEDURE IF EXISTS `UpdateCompletedBookings`;
DROP PROCEDURE IF EXISTS `CleanupOldNotifications`;

-- Drop views
DROP VIEW IF EXISTS `notification_stats`;
DROP VIEW IF EXISTS `booking_notifications`;

-- Drop indexes
DROP INDEX IF EXISTS `idx_user_notifications_user_read` ON `user_notifications`;
DROP INDEX IF EXISTS `idx_user_notifications_type_date` ON `user_notifications`;
DROP INDEX IF EXISTS `idx_bookings_status_time` ON `bookings`;
DROP INDEX IF EXISTS `idx_bookings_user_status` ON `bookings`;

-- Drop table
DROP TABLE IF EXISTS `user_notifications`;

-- Remove columns (be careful!)
-- ALTER TABLE `bookings` DROP COLUMN IF EXISTS `unit_kerja`;
-- ALTER TABLE `bookings` DROP COLUMN IF EXISTS `dokumen_perizinan`;
-- ALTER TABLE `meeting_rooms` DROP COLUMN IF EXISTS `is_active`;
-- ALTER TABLE `users` DROP COLUMN IF EXISTS `google_id`;
-- ALTER TABLE `users` DROP COLUMN IF EXISTS `last_login_at`;
-- ALTER TABLE `users` DROP COLUMN IF EXISTS `email_verification_token`;
*/

-- =====================================================
-- END OF SCRIPT
-- =====================================================
