-- =====================================================
-- MIGRATION: Create User Notifications Table
-- File: 2025_10_11_080436_create_user_notifications_table.php
-- =====================================================

-- Create user_notifications table
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
-- SAMPLE DATA FOR TESTING
-- =====================================================

-- Insert sample notifications for testing
INSERT INTO `user_notifications` (`user_id`, `booking_id`, `type`, `title`, `message`, `is_read`, `created_at`, `updated_at`) VALUES
(1, 1, 'booking_confirmed', 'Booking Dikonfirmasi', 'Meeting "Rapat Tim" telah dikonfirmasi. Anda akan menerima email pengingat 30 menit sebelum meeting dimulai.', 0, NOW(), NOW()),
(1, 2, 'room_maintenance', 'Ruang Meeting Dinonaktifkan', 'Meeting "Presentasi Proyek" dibatalkan karena ruang meeting sedang dalam maintenance. Silakan buat booking baru untuk ruang lain.', 0, NOW(), NOW()),
(2, 3, 'booking_cancelled', 'Booking Dibatalkan', 'Meeting "Review Proyek" telah dibatalkan. Alasan: Perubahan jadwal.', 1, NOW(), NOW()),
(2, 4, 'booking_completed', 'Meeting Selesai', 'Meeting "Training Tim" telah selesai. Terima kasih telah menggunakan layanan kami.', 1, NOW(), NOW());

-- =====================================================
-- USEFUL QUERIES FOR TESTING
-- =====================================================

-- Get all notifications for a user
SELECT * FROM user_notifications WHERE user_id = 1 ORDER BY created_at DESC;

-- Get unread notifications count for a user
SELECT COUNT(*) as unread_count FROM user_notifications WHERE user_id = 1 AND is_read = 0;

-- Get notifications by type
SELECT type, COUNT(*) as count FROM user_notifications GROUP BY type;

-- Mark notification as read
UPDATE user_notifications SET is_read = 1, read_at = NOW() WHERE id = 1;

-- Mark all notifications as read for a user
UPDATE user_notifications SET is_read = 1, read_at = NOW() WHERE user_id = 1 AND is_read = 0;
