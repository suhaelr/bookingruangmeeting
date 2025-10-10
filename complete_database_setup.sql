-- =====================================================
-- COMPLETE DATABASE SETUP FOR MEETING ROOM BOOKING SYSTEM
-- Database: dsvbpgpt_lara812
-- =====================================================

-- Set database
USE dsvbpgpt_lara812;

-- =====================================================
-- 1. CREATE MISSING TABLES (if not exist)
-- =====================================================

-- Create users table if not exists
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
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_username_unique` (`username`),
    UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. UPDATE EXISTING TABLES STRUCTURE
-- =====================================================

-- Update meeting_rooms table structure
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
ADD COLUMN IF NOT EXISTS `updated_at` timestamp NULL DEFAULT NULL AFTER `created_at`,
ADD PRIMARY KEY (`id`);

-- Remove hourly_rate column if exists (pricing removed)
ALTER TABLE `meeting_rooms` DROP COLUMN IF EXISTS `hourly_rate`;

-- Update bookings table structure
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
ADD COLUMN IF NOT EXISTS `updated_at` timestamp NULL DEFAULT NULL AFTER `created_at`,
ADD PRIMARY KEY (`id`),
ADD KEY `bookings_user_id_foreign` (`user_id`),
ADD KEY `bookings_meeting_room_id_foreign` (`meeting_room_id`),
ADD KEY `bookings_start_time_index` (`start_time`),
ADD KEY `bookings_status_index` (`status`);

-- =====================================================
-- 3. ADD FOREIGN KEY CONSTRAINTS
-- =====================================================

-- Add foreign key constraints for bookings
ALTER TABLE `bookings` 
ADD CONSTRAINT `bookings_user_id_foreign` 
FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `bookings` 
ADD CONSTRAINT `bookings_meeting_room_id_foreign` 
FOREIGN KEY (`meeting_room_id`) REFERENCES `meeting_rooms` (`id`) ON DELETE CASCADE;

-- =====================================================
-- 4. INSERT SAMPLE DATA
-- =====================================================

-- Insert admin user
INSERT IGNORE INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@pusdatinbgn.web.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin', 1, NOW(), NOW());

-- Insert sample users
INSERT IGNORE INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `phone`, `department`, `role`, `is_active`, `created_at`, `updated_at`) VALUES
(2, 'suhaelr', 'suhaelr@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Suhael Rahman', '08123456789', 'IT Department', 'user', 1, NOW(), NOW()),
(3, 'john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', '08123456788', 'HR Department', 'user', 1, NOW(), NOW()),
(4, 'jane_smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane Smith', '08123456787', 'Finance Department', 'user', 1, NOW(), NOW());

-- Insert sample meeting rooms
INSERT IGNORE INTO `meeting_rooms` (`id`, `name`, `description`, `capacity`, `amenities`, `location`, `images`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Conference Room A', 'Large conference room with modern facilities', 20, '["projector", "whiteboard", "wifi", "ac", "sound_system"]', 'Floor 1, Building A', '["room1.jpg", "room1_2.jpg"]', 1, NOW(), NOW()),
(2, 'Meeting Room B', 'Medium-sized meeting room for small groups', 8, '["whiteboard", "wifi", "ac"]', 'Floor 2, Building A', '["room2.jpg"]', 1, NOW(), NOW()),
(3, 'Executive Boardroom', 'Premium boardroom for executive meetings', 12, '["projector", "whiteboard", "wifi", "ac", "video_conference", "catering"]', 'Floor 3, Building A', '["room3.jpg", "room3_2.jpg"]', 1, NOW(), NOW()),
(4, 'Training Room', 'Large training room with presentation facilities', 30, '["projector", "whiteboard", "wifi", "ac", "sound_system", "microphone"]', 'Floor 1, Building B', '["room4.jpg"]', 1, NOW(), NOW()),
(5, 'Small Meeting Room', 'Intimate meeting room for 2-4 people', 4, '["wifi", "ac"]', 'Floor 2, Building B', '["room5.jpg"]', 1, NOW(), NOW());

-- Insert sample bookings
INSERT IGNORE INTO `bookings` (`id`, `user_id`, `meeting_room_id`, `title`, `description`, `start_time`, `end_time`, `status`, `attendees_count`, `attendees`, `special_requirements`, `unit_kerja`, `dokumen_perizinan`, `total_cost`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'Weekly Team Meeting', 'Regular weekly team meeting', DATE_ADD(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 2 HOUR, 'confirmed', 15, '["team1@example.com", "team2@example.com"]', 'Need projector and whiteboard', 'IT Department', 'dokumen_perizinan/sample1.pdf', 0.00, NOW(), NOW()),
(2, 3, 2, 'Client Presentation', 'Presentation for new client', DATE_ADD(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 2 DAY) + INTERVAL 1 HOUR, 'pending', 6, '["client@example.com"]', 'Need video conference setup', 'HR Department', 'dokumen_perizinan/sample2.pdf', 0.00, NOW(), NOW()),
(3, 4, 3, 'Board Meeting', 'Monthly board meeting', DATE_ADD(NOW(), INTERVAL 3 DAY), DATE_ADD(NOW(), INTERVAL 3 DAY) + INTERVAL 3 HOUR, 'confirmed', 10, '["board1@example.com", "board2@example.com"]', 'Catering required', 'Finance Department', 'dokumen_perizinan/sample3.pdf', 0.00, NOW(), NOW());

-- =====================================================
-- 5. CREATE INDEXES FOR PERFORMANCE
-- =====================================================

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS `idx_meeting_rooms_is_active` ON `meeting_rooms` (`is_active`);
CREATE INDEX IF NOT EXISTS `idx_meeting_rooms_capacity` ON `meeting_rooms` (`capacity`);
CREATE INDEX IF NOT EXISTS `idx_bookings_user_status` ON `bookings` (`user_id`, `status`);
CREATE INDEX IF NOT EXISTS `idx_bookings_room_time` ON `bookings` (`meeting_room_id`, `start_time`, `end_time`);
CREATE INDEX IF NOT EXISTS `idx_bookings_created_at` ON `bookings` (`created_at`);
CREATE INDEX IF NOT EXISTS `idx_users_email` ON `users` (`email`);
CREATE INDEX IF NOT EXISTS `idx_users_username` ON `users` (`username`);
CREATE INDEX IF NOT EXISTS `idx_users_role` ON `users` (`role`);

-- =====================================================
-- 6. VERIFY DATABASE STRUCTURE
-- =====================================================

-- Show table structure
SHOW TABLES;

-- Show meeting_rooms structure
DESCRIBE meeting_rooms;

-- Show bookings structure
DESCRIBE bookings;

-- Show users structure
DESCRIBE users;

-- =====================================================
-- 7. VERIFY DATA
-- =====================================================

-- Count records in each table
SELECT 'meeting_rooms' as table_name, COUNT(*) as record_count FROM meeting_rooms
UNION ALL
SELECT 'bookings' as table_name, COUNT(*) as record_count FROM bookings
UNION ALL
SELECT 'users' as table_name, COUNT(*) as record_count FROM users;

-- Show sample data
SELECT 'Meeting Rooms:' as info;
SELECT id, name, capacity, location, is_active FROM meeting_rooms LIMIT 5;

SELECT 'Users:' as info;
SELECT id, username, email, full_name, role FROM users LIMIT 5;

SELECT 'Bookings:' as info;
SELECT id, user_id, meeting_room_id, title, status, start_time FROM bookings LIMIT 5;

-- =====================================================
-- COMPLETE DATABASE SETUP FINISHED
-- =====================================================

SELECT 'Database setup completed successfully!' as status;
