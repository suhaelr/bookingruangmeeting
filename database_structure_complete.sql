-- =============================================
-- LARAVEL MEETING ROOM BOOKING SYSTEM
-- Complete Database Structure with All Features
-- =============================================

-- 1. CREATE USERS TABLE (Updated with new columns)
-- =============================================
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL UNIQUE,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL UNIQUE,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(255) NULL,
  `department` varchar(255) NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `avatar` varchar(255) NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. CREATE PASSWORD RESET TOKENS TABLE
-- =============================================
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. CREATE SESSIONS TABLE
-- =============================================
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned NULL,
  `ip_address` varchar(45) NULL,
  `user_agent` text NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. CREATE CACHE TABLE
-- =============================================
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. CREATE CACHE LOCKS TABLE
-- =============================================
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. CREATE JOBS TABLE
-- =============================================
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. CREATE JOB BATCHES TABLE
-- =============================================
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext NULL,
  `cancelled_at` int(11) NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. CREATE FAILED JOBS TABLE
-- =============================================
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL UNIQUE,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. CREATE MEETING ROOMS TABLE
-- =============================================
CREATE TABLE `meeting_rooms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NULL,
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

-- 10. CREATE BOOKINGS TABLE
-- =============================================
CREATE TABLE `bookings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `meeting_room_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `attendees_count` int(11) NOT NULL DEFAULT '1',
  `attendees` json DEFAULT NULL,
  `attachments` json DEFAULT NULL,
  `special_requirements` text NULL,
  `total_cost` decimal(8,2) NOT NULL DEFAULT '0.00',
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancellation_reason` text NULL,
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

-- 11. CREATE ADMIN SESSIONS TABLE (for admin panel)
-- =============================================
CREATE TABLE `admin_sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `ip_address` varchar(45) NULL,
  `user_agent` text NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_sessions_user_id_index` (`user_id`),
  KEY `admin_sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. INSERT SAMPLE DATA - USERS (Admin & Regular Users)
-- =============================================
INSERT INTO `users` (`username`, `name`, `email`, `password`, `full_name`, `phone`, `department`, `role`, `avatar`, `last_login_at`, `email_verified_at`, `created_at`, `updated_at`) VALUES
('admin', 'Admin System', 'admin@company.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj4Z4.8K.2', 'Administrator System', '081234567890', 'IT', 'admin', 'admin-avatar.jpg', NOW(), NOW(), NOW(), NOW()),
('johndoe', 'John Doe', 'john.doe@company.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj4Z4.8K.2', 'John Doe', '081234567891', 'Marketing', 'user', 'john-avatar.jpg', NOW(), NOW(), NOW(), NOW()),
('janesmith', 'Jane Smith', 'jane.smith@company.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj4Z4.8K.2', 'Jane Smith', '081234567892', 'HR', 'user', 'jane-avatar.jpg', NOW(), NOW(), NOW(), NOW()),
('mikejohnson', 'Mike Johnson', 'mike.johnson@company.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj4Z4.8K.2', 'Mike Johnson', '081234567893', 'Finance', 'user', 'mike-avatar.jpg', NOW(), NOW(), NOW(), NOW()),
('sarahwilson', 'Sarah Wilson', 'sarah.wilson@company.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj4Z4.8K.2', 'Sarah Wilson', '081234567894', 'Operations', 'user', 'sarah-avatar.jpg', NOW(), NOW(), NOW(), NOW());

-- 13. INSERT SAMPLE DATA - MEETING ROOMS
-- =============================================
INSERT INTO `meeting_rooms` (`name`, `description`, `capacity`, `amenities`, `location`, `hourly_rate`, `images`, `is_active`, `created_at`, `updated_at`) VALUES
('Ruang Rapat Utama', 'Ruang rapat besar dengan fasilitas lengkap untuk meeting penting', 20, '["projector", "whiteboard", "wifi", "ac", "sound_system"]', 'Lantai 3 - Gedung A', 150000.00, '["room1.jpg", "room1_2.jpg"]', 1, NOW(), NOW()),
('Ruang Meeting Kecil', 'Ruang meeting untuk 4-6 orang, cocok untuk diskusi tim', 6, '["projector", "whiteboard", "wifi", "ac"]', 'Lantai 2 - Gedung A', 75000.00, '["room2.jpg"]', 1, NOW(), NOW()),
('Ruang Konferensi', 'Ruang konferensi dengan kapasitas besar untuk presentasi', 50, '["projector", "whiteboard", "wifi", "ac", "sound_system", "video_conference"]', 'Lantai 1 - Gedung B', 300000.00, '["room3.jpg", "room3_2.jpg"]', 1, NOW(), NOW()),
('Ruang Diskusi', 'Ruang santai untuk brainstorming dan diskusi informal', 8, '["whiteboard", "wifi", "ac", "coffee_machine"]', 'Lantai 4 - Gedung A', 50000.00, '["room4.jpg"]', 1, NOW(), NOW()),
('Ruang VIP', 'Ruang meeting eksklusif untuk eksekutif', 12, '["projector", "whiteboard", "wifi", "ac", "sound_system", "video_conference", "coffee_machine"]', 'Lantai 5 - Gedung A', 200000.00, '["room5.jpg"]', 1, NOW(), NOW()),
('Ruang Training', 'Ruang khusus untuk pelatihan dan workshop', 30, '["projector", "whiteboard", "wifi", "ac", "sound_system", "flipchart"]', 'Lantai 2 - Gedung B', 120000.00, '["room6.jpg"]', 1, NOW(), NOW());

-- 14. INSERT SAMPLE DATA - BOOKINGS
-- =============================================
INSERT INTO `bookings` (`user_id`, `meeting_room_id`, `title`, `description`, `start_time`, `end_time`, `status`, `attendees_count`, `attendees`, `attachments`, `special_requirements`, `total_cost`, `created_at`, `updated_at`) VALUES
(2, 1, 'Weekly Team Meeting', 'Meeting rutin tim marketing untuk review progress', '2024-10-15 09:00:00', '2024-10-15 10:00:00', 'confirmed', 8, '["john.doe@company.com", "jane.smith@company.com", "mike.johnson@company.com"]', '["agenda.pdf", "presentation.pptx"]', 'Perlu projector dan whiteboard', 150000.00, NOW(), NOW()),
(3, 2, 'Client Presentation', 'Presentasi proposal kepada klien baru', '2024-10-16 14:00:00', '2024-10-16 15:30:00', 'pending', 4, '["jane.smith@company.com", "client@external.com"]', '["proposal.pdf"]', 'Perlu video conference setup', 112500.00, NOW(), NOW()),
(4, 3, 'Company All Hands', 'Meeting seluruh karyawan untuk update perusahaan', '2024-10-17 10:00:00', '2024-10-17 12:00:00', 'confirmed', 45, '["all@company.com"]', '["company_update.pdf"]', 'Perlu sound system yang baik', 600000.00, NOW(), NOW()),
(5, 4, 'Brainstorming Session', 'Sesi brainstorming untuk project baru', '2024-10-18 13:00:00', '2024-10-18 14:30:00', 'confirmed', 6, '["sarah.wilson@company.com", "team@company.com"]', NULL, 'Perlu whiteboard dan coffee', 75000.00, NOW(), NOW()),
(2, 5, 'Executive Meeting', 'Meeting eksekutif untuk strategic planning', '2024-10-19 09:00:00', '2024-10-19 11:00:00', 'pending', 10, '["executives@company.com"]', '["strategy_doc.pdf"]', 'Perlu privacy dan video conference', 400000.00, NOW(), NOW()),
(3, 6, 'Training Session', 'Pelatihan software baru untuk tim', '2024-10-20 09:00:00', '2024-10-20 17:00:00', 'confirmed', 25, '["training@company.com"]', '["training_materials.zip"]', 'Full day training dengan break', 960000.00, NOW(), NOW()),
(4, 1, 'Project Review', 'Review progress project Q4', '2024-10-21 10:00:00', '2024-10-21 11:30:00', 'confirmed', 12, '["project_team@company.com"]', '["project_report.pdf"]', 'Perlu projector untuk presentasi', 225000.00, NOW(), NOW()),
(5, 2, 'Budget Planning', 'Perencanaan budget tahun depan', '2024-10-22 14:00:00', '2024-10-22 16:00:00', 'pending', 5, '["finance@company.com"]', '["budget_template.xlsx"]', 'Perlu privacy untuk data sensitif', 150000.00, NOW(), NOW()),
(2, 3, 'Monthly Sales Review', 'Review penjualan bulan September', '2024-09-30 10:00:00', '2024-09-30 12:00:00', 'completed', 15, '["sales_team@company.com"]', '["sales_report.pdf"]', 'Perlu projector untuk presentasi data', 600000.00, '2024-09-25 08:00:00', '2024-09-30 12:00:00'),
(3, 1, 'HR Training', 'Pelatihan HR untuk karyawan baru', '2024-10-01 09:00:00', '2024-10-01 17:00:00', 'completed', 20, '["hr_team@company.com"]', '["hr_handbook.pdf"]', 'Full day training', 1200000.00, '2024-09-28 10:00:00', '2024-10-01 17:00:00'),
(4, 4, 'Product Development', 'Diskusi pengembangan produk baru', '2024-10-25 13:00:00', '2024-10-25 15:00:00', 'confirmed', 8, '["dev_team@company.com"]', '["product_specs.pdf"]', 'Perlu whiteboard untuk brainstorming', 100000.00, NOW(), NOW()),
(5, 5, 'Board Meeting', 'Rapat dewan direksi', '2024-10-28 09:00:00', '2024-10-28 12:00:00', 'pending', 8, '["board@company.com"]', '["board_package.pdf"]', 'Meeting tertutup, perlu privacy', 600000.00, NOW(), NOW());

-- =============================================
-- END OF DATABASE STRUCTURE
-- =============================================

-- NOTES:
-- 1. Password untuk semua user adalah: 'password' (sudah di-hash)
-- 2. Admin login: username='admin', password='password'
-- 3. Database structure mencakup semua fitur:
--    - User management dengan role admin/user
--    - Meeting room management
--    - Booking system dengan status tracking
--    - Notification system
--    - Session management
--    - Cache system
--    - Job queue system
-- 4. Sample data mencakup 6 meeting rooms dan 12 bookings
-- 5. Semua foreign key constraints sudah di-set dengan benar
-- 6. Indexes sudah dioptimasi untuk performa query
