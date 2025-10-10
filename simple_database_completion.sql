-- =====================================================
-- SIMPLE DATABASE COMPLETION SCRIPT
-- Database: dsvbpgpt_lara812
-- =====================================================

USE dsvbpgpt_lara812;

-- =====================================================
-- 1. CREATE USERS TABLE (if not exists)
-- =====================================================

CREATE TABLE IF NOT EXISTS `users` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `username` varchar(255) NOT NULL UNIQUE,
    `email` varchar(255) NOT NULL UNIQUE,
    `email_verified_at` timestamp NULL DEFAULT NULL,
    `password` varchar(255) NOT NULL,
    `full_name` varchar(255) NOT NULL,
    `phone` varchar(20) DEFAULT NULL,
    `department` varchar(100) DEFAULT NULL,
    `role` enum('admin','user') NOT NULL DEFAULT 'user',
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `remember_token` varchar(100) DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. UPDATE MEETING_ROOMS TABLE
-- =====================================================

-- Remove hourly_rate column (pricing removed)
ALTER TABLE `meeting_rooms` DROP COLUMN IF EXISTS `hourly_rate`;

-- Add missing columns to meeting_rooms
ALTER TABLE `meeting_rooms` 
ADD COLUMN IF NOT EXISTS `id` bigint unsigned NOT NULL AUTO_INCREMENT FIRST,
ADD COLUMN IF NOT EXISTS `name` varchar(255) NOT NULL AFTER `id`,
ADD COLUMN IF NOT EXISTS `description` text AFTER `name`,
ADD COLUMN IF NOT EXISTS `capacity` int NOT NULL AFTER `description`,
ADD COLUMN IF NOT EXISTS `amenities` json DEFAULT NULL AFTER `capacity`,
ADD COLUMN IF NOT EXISTS `location` varchar(255) NOT NULL AFTER `amenities`,
ADD COLUMN IF NOT EXISTS `images` json DEFAULT NULL AFTER `location`,
ADD COLUMN IF NOT EXISTS `is_active` tinyint(1) NOT NULL DEFAULT 1 AFTER `images`,
ADD COLUMN IF NOT EXISTS `created_at` timestamp NULL DEFAULT NULL AFTER `is_active`,
ADD COLUMN IF NOT EXISTS `updated_at` timestamp NULL DEFAULT NULL AFTER `created_at`;

-- Set primary key
ALTER TABLE `meeting_rooms` ADD PRIMARY KEY IF NOT EXISTS (`id`);

-- =====================================================
-- 3. UPDATE BOOKINGS TABLE
-- =====================================================

-- Add missing columns to bookings
ALTER TABLE `bookings` 
ADD COLUMN IF NOT EXISTS `id` bigint unsigned NOT NULL AUTO_INCREMENT FIRST,
ADD COLUMN IF NOT EXISTS `user_id` bigint unsigned NOT NULL AFTER `id`,
ADD COLUMN IF NOT EXISTS `meeting_room_id` bigint unsigned NOT NULL AFTER `user_id`,
ADD COLUMN IF NOT EXISTS `title` varchar(255) NOT NULL AFTER `meeting_room_id`,
ADD COLUMN IF NOT EXISTS `description` text AFTER `title`,
ADD COLUMN IF NOT EXISTS `start_time` datetime NOT NULL AFTER `description`,
ADD COLUMN IF NOT EXISTS `end_time` datetime NOT NULL AFTER `start_time`,
ADD COLUMN IF NOT EXISTS `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending' AFTER `end_time`,
ADD COLUMN IF NOT EXISTS `attendees_count` int NOT NULL AFTER `status`,
ADD COLUMN IF NOT EXISTS `attendees` json DEFAULT NULL AFTER `attendees_count`,
ADD COLUMN IF NOT EXISTS `attachments` json DEFAULT NULL AFTER `attendees`,
ADD COLUMN IF NOT EXISTS `special_requirements` text AFTER `attachments`,
ADD COLUMN IF NOT EXISTS `unit_kerja` varchar(255) NOT NULL AFTER `special_requirements`,
ADD COLUMN IF NOT EXISTS `dokumen_perizinan` varchar(255) DEFAULT NULL AFTER `unit_kerja`,
ADD COLUMN IF NOT EXISTS `total_cost` decimal(12,2) NOT NULL DEFAULT 0.00 AFTER `dokumen_perizinan`,
ADD COLUMN IF NOT EXISTS `cancelled_at` timestamp NULL DEFAULT NULL AFTER `total_cost`,
ADD COLUMN IF NOT EXISTS `cancellation_reason` text AFTER `cancelled_at`,
ADD COLUMN IF NOT EXISTS `created_at` timestamp NULL DEFAULT NULL AFTER `cancellation_reason`,
ADD COLUMN IF NOT EXISTS `updated_at` timestamp NULL DEFAULT NULL AFTER `created_at`;

-- Set primary key
ALTER TABLE `bookings` ADD PRIMARY KEY IF NOT EXISTS (`id`);

-- =====================================================
-- 4. ADD FOREIGN KEYS
-- =====================================================

-- Add foreign key for bookings -> users
ALTER TABLE `bookings` 
ADD CONSTRAINT `bookings_user_id_foreign` 
FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

-- Add foreign key for bookings -> meeting_rooms
ALTER TABLE `bookings` 
ADD CONSTRAINT `bookings_meeting_room_id_foreign` 
FOREIGN KEY (`meeting_room_id`) REFERENCES `meeting_rooms` (`id`) ON DELETE CASCADE;

-- =====================================================
-- 5. INSERT SAMPLE DATA
-- =====================================================

-- Insert admin user
INSERT IGNORE INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@pusdatinbgn.web.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin', 1, NOW(), NOW());

-- Insert sample user
INSERT IGNORE INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `phone`, `department`, `role`, `is_active`, `created_at`, `updated_at`) VALUES
(2, 'suhaelr', 'suhaelr@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Suhael Rahman', '08123456789', 'IT Department', 'user', 1, NOW(), NOW());

-- Insert sample meeting rooms
INSERT IGNORE INTO `meeting_rooms` (`id`, `name`, `description`, `capacity`, `amenities`, `location`, `images`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Conference Room A', 'Large conference room with modern facilities', 20, '["projector", "whiteboard", "wifi", "ac", "sound_system"]', 'Floor 1, Building A', '["room1.jpg", "room1_2.jpg"]', 1, NOW(), NOW()),
(2, 'Meeting Room B', 'Medium-sized meeting room for small groups', 8, '["whiteboard", "wifi", "ac"]', 'Floor 2, Building A', '["room2.jpg"]', 1, NOW(), NOW()),
(3, 'Executive Boardroom', 'Premium boardroom for executive meetings', 12, '["projector", "whiteboard", "wifi", "ac", "video_conference", "catering"]', 'Floor 3, Building A', '["room3.jpg", "room3_2.jpg"]', 1, NOW(), NOW()),
(4, 'Training Room', 'Large training room with presentation facilities', 30, '["projector", "whiteboard", "wifi", "ac", "sound_system", "microphone"]', 'Floor 1, Building B', '["room4.jpg"]', 1, NOW(), NOW()),
(5, 'Small Meeting Room', 'Intimate meeting room for 2-4 people', 4, '["wifi", "ac"]', 'Floor 2, Building B', '["room5.jpg"]', 1, NOW(), NOW());

-- =====================================================
-- 6. CREATE INDEXES
-- =====================================================

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS `idx_meeting_rooms_is_active` ON `meeting_rooms` (`is_active`);
CREATE INDEX IF NOT EXISTS `idx_bookings_user_status` ON `bookings` (`user_id`, `status`);
CREATE INDEX IF NOT EXISTS `idx_bookings_room_time` ON `bookings` (`meeting_room_id`, `start_time`, `end_time`);

-- =====================================================
-- 7. VERIFY STRUCTURE
-- =====================================================

-- Show all tables
SHOW TABLES;

-- Show table structures
DESCRIBE meeting_rooms;
DESCRIBE bookings;
DESCRIBE users;

-- Count records
SELECT 'meeting_rooms' as table_name, COUNT(*) as count FROM meeting_rooms
UNION ALL
SELECT 'bookings' as table_name, COUNT(*) as count FROM bookings
UNION ALL
SELECT 'users' as table_name, COUNT(*) as count FROM users;

-- =====================================================
-- COMPLETED
-- =====================================================

SELECT 'Database completion finished!' as status;
