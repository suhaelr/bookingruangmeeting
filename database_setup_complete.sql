-- =============================================
-- LARAVEL MEETING ROOM BOOKING SYSTEM
-- Complete Database Setup with Sample Data
-- =============================================

-- 1. CREATE MEETING_ROOMS TABLE
-- =============================================
CREATE TABLE `meeting_rooms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `capacity` int(11) NOT NULL,
  `amenities` json DEFAULT NULL,
  `location` varchar(255) NOT NULL,
  `hourly_rate` decimal(8,2) NOT NULL DEFAULT '0.00',
  `images` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. CREATE BOOKINGS TABLE
-- =============================================
CREATE TABLE `bookings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `meeting_room_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `attendees_count` int(11) NOT NULL DEFAULT '1',
  `attendees` json DEFAULT NULL,
  `attachments` json DEFAULT NULL,
  `special_requirements` text,
  `total_cost` decimal(8,2) NOT NULL DEFAULT '0.00',
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancellation_reason` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bookings_meeting_room_id_start_time_end_time_index` (`meeting_room_id`,`start_time`,`end_time`),
  KEY `bookings_user_id_start_time_index` (`user_id`,`start_time`),
  KEY `bookings_status_index` (`status`),
  KEY `bookings_user_id_foreign` (`user_id`),
  KEY `bookings_meeting_room_id_foreign` (`meeting_room_id`),
  CONSTRAINT `bookings_meeting_room_id_foreign` FOREIGN KEY (`meeting_room_id`) REFERENCES `meeting_rooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. UPDATE USERS TABLE (add missing columns)
-- =============================================
ALTER TABLE `users` 
ADD COLUMN `username` varchar(255) UNIQUE AFTER `id`,
ADD COLUMN `full_name` varchar(255) AFTER `username`,
ADD COLUMN `phone` varchar(255) NULL AFTER `full_name`,
ADD COLUMN `department` varchar(255) NULL AFTER `phone`,
ADD COLUMN `role` enum('admin','user') NOT NULL DEFAULT 'user' AFTER `department`,
ADD COLUMN `avatar` varchar(255) NULL AFTER `role`,
ADD COLUMN `last_login_at` timestamp NULL AFTER `avatar`;

-- 4. INSERT SAMPLE DATA - MEETING ROOMS
-- =============================================
INSERT INTO `meeting_rooms` (`name`, `description`, `capacity`, `amenities`, `location`, `hourly_rate`, `images`, `is_active`, `created_at`, `updated_at`) VALUES
('Ruang Rapat Utama', 'Ruang rapat besar dengan fasilitas lengkap untuk meeting penting', 20, '["projector", "whiteboard", "wifi", "ac", "sound_system"]', 'Lantai 3 - Gedung A', 150000.00, '["room1.jpg", "room1_2.jpg"]', 1, NOW(), NOW()),
('Ruang Meeting Kecil', 'Ruang meeting untuk 4-6 orang, cocok untuk diskusi tim', 6, '["projector", "whiteboard", "wifi", "ac"]', 'Lantai 2 - Gedung A', 75000.00, '["room2.jpg"]', 1, NOW(), NOW()),
('Ruang Konferensi', 'Ruang konferensi dengan kapasitas besar untuk presentasi', 50, '["projector", "whiteboard", "wifi", "ac", "sound_system", "video_conference"]', 'Lantai 1 - Gedung B', 300000.00, '["room3.jpg", "room3_2.jpg"]', 1, NOW(), NOW()),
('Ruang Diskusi', 'Ruang santai untuk brainstorming dan diskusi informal', 8, '["whiteboard", "wifi", "ac", "coffee_machine"]', 'Lantai 4 - Gedung A', 50000.00, '["room4.jpg"]', 1, NOW(), NOW()),
('Ruang VIP', 'Ruang meeting eksklusif untuk eksekutif', 12, '["projector", "whiteboard", "wifi", "ac", "sound_system", "video_conference", "coffee_machine"]', 'Lantai 5 - Gedung A', 200000.00, '["room5.jpg"]', 1, NOW(), NOW()),
('Ruang Training', 'Ruang khusus untuk pelatihan dan workshop', 30, '["projector", "whiteboard", "wifi", "ac", "sound_system", "flipchart"]', 'Lantai 2 - Gedung B', 120000.00, '["room6.jpg"]', 1, NOW(), NOW());

-- 5. INSERT SAMPLE DATA - USERS (Admin & Regular Users)
-- =============================================
INSERT INTO `users` (`name`, `email`, `password`, `username`, `full_name`, `phone`, `department`, `role`, `avatar`, `last_login_at`, `email_verified_at`, `created_at`, `updated_at`) VALUES
('Admin System', 'admin@company.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj4Z4.8K.2', 'admin', 'Administrator System', '081234567890', 'IT', 'admin', 'admin-avatar.jpg', NOW(), NOW(), NOW(), NOW()),
('John Doe', 'john.doe@company.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj4Z4.8K.2', 'johndoe', 'John Doe', '081234567891', 'Marketing', 'user', 'john-avatar.jpg', NOW(), NOW(), NOW(), NOW()),
('Jane Smith', 'jane.smith@company.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj4Z4.8K.2', 'janesmith', 'Jane Smith', '081234567892', 'HR', 'user', 'jane-avatar.jpg', NOW(), NOW(), NOW(), NOW()),
('Mike Johnson', 'mike.johnson@company.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj4Z4.8K.2', 'mikejohnson', 'Mike Johnson', '081234567893', 'Finance', 'user', 'mike-avatar.jpg', NOW(), NOW(), NOW(), NOW()),
('Sarah Wilson', 'sarah.wilson@company.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj4Z4.8K.2', 'sarahwilson', 'Sarah Wilson', '081234567894', 'Operations', 'user', 'sarah-avatar.jpg', NOW(), NOW(), NOW(), NOW());

-- 6. INSERT SAMPLE DATA - BOOKINGS
-- =============================================
INSERT INTO `bookings` (`user_id`, `meeting_room_id`, `title`, `description`, `start_time`, `end_time`, `status`, `attendees_count`, `attendees`, `attachments`, `special_requirements`, `total_cost`, `created_at`, `updated_at`) VALUES
(2, 1, 'Weekly Team Meeting', 'Meeting rutin tim marketing untuk review progress', '2024-10-15 09:00:00', '2024-10-15 10:00:00', 'confirmed', 8, '["john.doe@company.com", "jane.smith@company.com", "mike.johnson@company.com"]', '["agenda.pdf", "presentation.pptx"]', 'Perlu projector dan whiteboard', 150000.00, NOW(), NOW()),
(3, 2, 'Client Presentation', 'Presentasi proposal kepada klien baru', '2024-10-16 14:00:00', '2024-10-16 15:30:00', 'pending', 4, '["jane.smith@company.com", "client@external.com"]', '["proposal.pdf"]', 'Perlu video conference setup', 112500.00, NOW(), NOW()),
(4, 3, 'Company All Hands', 'Meeting seluruh karyawan untuk update perusahaan', '2024-10-17 10:00:00', '2024-10-17 12:00:00', 'confirmed', 45, '["all@company.com"]', '["company_update.pdf"]', 'Perlu sound system yang baik', 600000.00, NOW(), NOW()),
(5, 4, 'Brainstorming Session', 'Sesi brainstorming untuk project baru', '2024-10-18 13:00:00', '2024-10-18 14:30:00', 'confirmed', 6, '["sarah.wilson@company.com", "team@company.com"]', NULL, 'Perlu whiteboard dan coffee', 75000.00, NOW(), NOW()),
(2, 5, 'Executive Meeting', 'Meeting eksekutif untuk strategic planning', '2024-10-19 09:00:00', '2024-10-19 11:00:00', 'pending', 10, '["executives@company.com"]', '["strategy_doc.pdf"]', 'Perlu privacy dan video conference', 400000.00, NOW(), NOW()),
(3, 6, 'Training Session', 'Pelatihan software baru untuk tim', '2024-10-20 09:00:00', '2024-10-20 17:00:00', 'confirmed', 25, '["training@company.com"]', '["training_materials.zip"]', 'Full day training dengan break', 960000.00, NOW(), NOW()),
(4, 1, 'Project Review', 'Review progress project Q4', '2024-10-21 10:00:00', '2024-10-21 11:30:00', 'confirmed', 12, '["project_team@company.com"]', '["project_report.pdf"]', 'Perlu projector untuk presentasi', 225000.00, NOW(), NOW()),
(5, 2, 'Budget Planning', 'Perencanaan budget tahun depan', '2024-10-22 14:00:00', '2024-10-22 16:00:00', 'pending', 5, '["finance@company.com"]', '["budget_template.xlsx"]', 'Perlu privacy untuk data sensitif', 150000.00, NOW(), NOW());

-- 7. INSERT ADDITIONAL SAMPLE BOOKINGS (Past & Future)
-- =============================================
INSERT INTO `bookings` (`user_id`, `meeting_room_id`, `title`, `description`, `start_time`, `end_time`, `status`, `attendees_count`, `attendees`, `attachments`, `special_requirements`, `total_cost`, `created_at`, `updated_at`) VALUES
(2, 3, 'Monthly Sales Review', 'Review penjualan bulan September', '2024-09-30 10:00:00', '2024-09-30 12:00:00', 'completed', 15, '["sales_team@company.com"]', '["sales_report.pdf"]', 'Perlu projector untuk presentasi data', 600000.00, '2024-09-25 08:00:00', '2024-09-30 12:00:00'),
(3, 1, 'HR Training', 'Pelatihan HR untuk karyawan baru', '2024-10-01 09:00:00', '2024-10-01 17:00:00', 'completed', 20, '["hr_team@company.com"]', '["hr_handbook.pdf"]', 'Full day training', 1200000.00, '2024-09-28 10:00:00', '2024-10-01 17:00:00'),
(4, 4, 'Product Development', 'Diskusi pengembangan produk baru', '2024-10-25 13:00:00', '2024-10-25 15:00:00', 'confirmed', 8, '["dev_team@company.com"]', '["product_specs.pdf"]', 'Perlu whiteboard untuk brainstorming', 100000.00, NOW(), NOW()),
(5, 5, 'Board Meeting', 'Rapat dewan direksi', '2024-10-28 09:00:00', '2024-10-28 12:00:00', 'pending', 8, '["board@company.com"]', '["board_package.pdf"]', 'Meeting tertutup, perlu privacy', 600000.00, NOW(), NOW());

-- =============================================
-- END OF DATABASE SETUP
-- =============================================

-- NOTES:
-- 1. Password untuk semua user adalah: 'password' (sudah di-hash)
-- 2. Admin login: username='admin', password='password'
-- 3. Sample data mencakup 6 meeting rooms dengan berbagai fasilitas
-- 4. Sample bookings dengan status berbeda (pending, confirmed, completed)
-- 5. Data mencakup booking masa lalu, sekarang, dan masa depan
-- 6. Semua foreign key constraints sudah di-set dengan benar
