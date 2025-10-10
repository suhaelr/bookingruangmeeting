-- =====================================================
-- MEETING ROOM BOOKING SYSTEM - COMPLETE DATABASE SCHEMA
-- =====================================================
-- Script ini dibuat berdasarkan migration files Laravel
-- untuk sistem booking meeting room

-- Drop existing tables if they exist (in correct order due to foreign keys)
DROP TABLE IF EXISTS `bookings`;
DROP TABLE IF EXISTS `meeting_rooms`;
DROP TABLE IF EXISTS `admin_sessions`;
DROP TABLE IF EXISTS `sessions`;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `failed_jobs`;
DROP TABLE IF EXISTS `job_batches`;
DROP TABLE IF EXISTS `jobs`;
DROP TABLE IF EXISTS `cache_locks`;
DROP TABLE IF EXISTS `cache`;

-- =====================================================
-- 1. CACHE TABLES
-- =====================================================

-- Cache table for Laravel caching
CREATE TABLE `cache` (
    `key` VARCHAR(255) NOT NULL PRIMARY KEY,
    `value` MEDIUMTEXT NOT NULL,
    `expiration` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cache locks table
CREATE TABLE `cache_locks` (
    `key` VARCHAR(255) NOT NULL PRIMARY KEY,
    `owner` VARCHAR(255) NOT NULL,
    `expiration` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. JOB QUEUE TABLES
-- =====================================================

-- Jobs table for queue processing
CREATE TABLE `jobs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `queue` VARCHAR(255) NOT NULL,
    `payload` LONGTEXT NOT NULL,
    `attempts` TINYINT UNSIGNED NOT NULL,
    `reserved_at` INT UNSIGNED NULL,
    `available_at` INT UNSIGNED NOT NULL,
    `created_at` INT UNSIGNED NOT NULL,
    INDEX `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Job batches table
CREATE TABLE `job_batches` (
    `id` VARCHAR(255) NOT NULL PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `total_jobs` INT NOT NULL,
    `pending_jobs` INT NOT NULL,
    `failed_jobs` INT NOT NULL,
    `failed_job_ids` LONGTEXT NOT NULL,
    `options` MEDIUMTEXT NULL,
    `cancelled_at` INT NULL,
    `created_at` INT NOT NULL,
    `finished_at` INT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Failed jobs table
CREATE TABLE `failed_jobs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `uuid` VARCHAR(255) NOT NULL UNIQUE,
    `connection` TEXT NOT NULL,
    `queue` TEXT NOT NULL,
    `payload` LONGTEXT NOT NULL,
    `exception` LONGTEXT NOT NULL,
    `failed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. USER MANAGEMENT TABLES
-- =====================================================

-- Users table (main user table with all columns)
CREATE TABLE `users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(255) NOT NULL UNIQUE,
    `name` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `email_verified_at` TIMESTAMP NULL,
    `email_verification_token` VARCHAR(255) NULL,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(255) NULL,
    `department` VARCHAR(255) NULL,
    `role` ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    `avatar` VARCHAR(255) NULL,
    `last_login_at` TIMESTAMP NULL,
    `remember_token` VARCHAR(100) NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    INDEX `users_email_index` (`email`),
    INDEX `users_username_index` (`username`),
    INDEX `users_role_index` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Password reset tokens table
CREATE TABLE `password_reset_tokens` (
    `email` VARCHAR(255) NOT NULL PRIMARY KEY,
    `token` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sessions table for user sessions
CREATE TABLE `sessions` (
    `id` VARCHAR(255) NOT NULL PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `payload` LONGTEXT NOT NULL,
    `last_activity` INT NOT NULL,
    INDEX `sessions_user_id_index` (`user_id`),
    INDEX `sessions_last_activity_index` (`last_activity`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin sessions table
CREATE TABLE `admin_sessions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(255) NOT NULL,
    `session_id` VARCHAR(255) NOT NULL UNIQUE,
    `last_activity` TIMESTAMP NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 4. MEETING ROOM TABLES
-- =====================================================

-- Meeting rooms table
CREATE TABLE `meeting_rooms` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `capacity` INT NOT NULL,
    `amenities` JSON NULL COMMENT 'Array of amenities: ["projector", "whiteboard", "wifi", "ac"]',
    `location` VARCHAR(255) NOT NULL,
    `hourly_rate` DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    `images` JSON NULL COMMENT 'Array of image file paths',
    `is_active` BOOLEAN NOT NULL DEFAULT TRUE,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    INDEX `meeting_rooms_is_active_index` (`is_active`),
    INDEX `meeting_rooms_capacity_index` (`capacity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 5. BOOKING TABLES
-- =====================================================

-- Bookings table
CREATE TABLE `bookings` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `meeting_room_id` BIGINT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `start_time` DATETIME NOT NULL,
    `end_time` DATETIME NOT NULL,
    `status` ENUM('pending', 'confirmed', 'cancelled', 'completed') NOT NULL DEFAULT 'pending',
    `attendees_count` INT NOT NULL DEFAULT 1,
    `attendees` JSON NULL COMMENT 'Array of attendee emails: ["email1@example.com", "email2@example.com"]',
    `attachments` JSON NULL COMMENT 'Array of attachment file paths: ["file1.pdf", "file2.docx"]',
    `special_requirements` TEXT NULL,
    `total_cost` DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    `cancelled_at` TIMESTAMP NULL,
    `cancellation_reason` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    
    -- Foreign key constraints
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`meeting_room_id`) REFERENCES `meeting_rooms`(`id`) ON DELETE CASCADE,
    
    -- Indexes for optimization
    INDEX `bookings_meeting_room_time_index` (`meeting_room_id`, `start_time`, `end_time`),
    INDEX `bookings_user_time_index` (`user_id`, `start_time`),
    INDEX `bookings_status_index` (`status`),
    INDEX `bookings_start_time_index` (`start_time`),
    INDEX `bookings_end_time_index` (`end_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 6. SAMPLE DATA INSERTION
-- =====================================================

-- Insert sample admin user
INSERT INTO `users` (`username`, `name`, `full_name`, `email`, `email_verified_at`, `password`, `phone`, `department`, `role`, `created_at`, `updated_at`) VALUES
('admin', 'Admin User', 'Administrator', 'admin@example.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+6281234567890', 'IT', 'admin', NOW(), NOW());

-- Insert sample regular user
INSERT INTO `users` (`username`, `name`, `full_name`, `email`, `email_verified_at`, `password`, `phone`, `department`, `role`, `created_at`, `updated_at`) VALUES
('john.doe', 'John Doe', 'John Doe', 'john.doe@example.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+6281234567891', 'Marketing', 'user', NOW(), NOW());

-- Insert sample meeting rooms
INSERT INTO `meeting_rooms` (`name`, `description`, `capacity`, `amenities`, `location`, `hourly_rate`, `images`, `is_active`, `created_at`, `updated_at`) VALUES
('Conference Room A', 'Large conference room with modern facilities', 20, '["projector", "whiteboard", "wifi", "ac", "video_conference"]', 'Floor 1, Building A', 150000.00, '["room_a_1.jpg", "room_a_2.jpg"]', TRUE, NOW(), NOW()),
('Meeting Room B', 'Medium-sized meeting room for small groups', 8, '["whiteboard", "wifi", "ac"]', 'Floor 2, Building A', 75000.00, '["room_b_1.jpg"]', TRUE, NOW(), NOW()),
('Executive Boardroom', 'Premium boardroom for executive meetings', 12, '["projector", "whiteboard", "wifi", "ac", "video_conference", "catering"]', 'Floor 3, Building A', 250000.00, '["boardroom_1.jpg", "boardroom_2.jpg"]', TRUE, NOW(), NOW()),
('Training Room', 'Large space for training and workshops', 50, '["projector", "whiteboard", "wifi", "ac", "sound_system"]', 'Floor 1, Building B', 200000.00, '["training_room_1.jpg"]', TRUE, NOW(), NOW()),
('Small Meeting Room', 'Intimate space for 1-on-1 meetings', 4, '["wifi", "ac"]', 'Floor 2, Building B', 50000.00, '["small_room_1.jpg"]', TRUE, NOW(), NOW());

-- Insert sample bookings
INSERT INTO `bookings` (`user_id`, `meeting_room_id`, `title`, `description`, `start_time`, `end_time`, `status`, `attendees_count`, `attendees`, `total_cost`, `created_at`, `updated_at`) VALUES
(2, 1, 'Weekly Team Meeting', 'Regular weekly team sync meeting', DATE_ADD(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 2 HOUR, 'confirmed', 5, '["team1@example.com", "team2@example.com", "team3@example.com"]', 300000.00, NOW(), NOW()),
(2, 2, 'Client Presentation', 'Presentation for new client proposal', DATE_ADD(NOW(), INTERVAL 3 DAY), DATE_ADD(NOW(), INTERVAL 3 DAY) + INTERVAL 1 HOUR, 'pending', 3, '["client@example.com", "manager@example.com"]', 75000.00, NOW(), NOW()),
(2, 3, 'Board Meeting', 'Monthly board of directors meeting', DATE_ADD(NOW(), INTERVAL 7 DAY), DATE_ADD(NOW(), INTERVAL 7 DAY) + INTERVAL 3 HOUR, 'confirmed', 8, '["director1@example.com", "director2@example.com", "ceo@example.com"]', 750000.00, NOW(), NOW());

-- =====================================================
-- 7. VIEWS FOR COMMON QUERIES
-- =====================================================

-- View for active bookings with user and room details
CREATE VIEW `active_bookings_view` AS
SELECT 
    b.id,
    b.title,
    b.description,
    b.start_time,
    b.end_time,
    b.status,
    b.attendees_count,
    b.total_cost,
    u.username,
    u.full_name as user_name,
    u.email as user_email,
    mr.name as room_name,
    mr.location,
    mr.capacity
FROM `bookings` b
JOIN `users` u ON b.user_id = u.id
JOIN `meeting_rooms` mr ON b.meeting_room_id = mr.id
WHERE b.status IN ('pending', 'confirmed')
ORDER BY b.start_time;

-- View for room availability
CREATE VIEW `room_availability_view` AS
SELECT 
    mr.id,
    mr.name,
    mr.capacity,
    mr.location,
    mr.hourly_rate,
    mr.is_active,
    COUNT(b.id) as active_bookings_count,
    CASE 
        WHEN mr.is_active = 1 THEN 'Available'
        ELSE 'Inactive'
    END as availability_status
FROM `meeting_rooms` mr
LEFT JOIN `bookings` b ON mr.id = b.meeting_room_id 
    AND b.status IN ('pending', 'confirmed')
    AND b.start_time >= NOW()
GROUP BY mr.id, mr.name, mr.capacity, mr.location, mr.hourly_rate, mr.is_active;

-- =====================================================
-- 8. STORED PROCEDURES
-- =====================================================

DELIMITER //

-- Procedure to check room availability
CREATE PROCEDURE CheckRoomAvailability(
    IN room_id INT,
    IN start_datetime DATETIME,
    IN end_datetime DATETIME
)
BEGIN
    SELECT 
        CASE 
            WHEN COUNT(*) = 0 THEN 'Available'
            ELSE 'Not Available'
        END as availability_status,
        COUNT(*) as conflicting_bookings
    FROM bookings 
    WHERE meeting_room_id = room_id 
        AND status IN ('pending', 'confirmed')
        AND (
            (start_time < end_datetime AND end_time > start_datetime)
        );
END //

-- Procedure to get user booking history
CREATE PROCEDURE GetUserBookingHistory(IN user_id INT)
BEGIN
    SELECT 
        b.id,
        b.title,
        b.start_time,
        b.end_time,
        b.status,
        b.total_cost,
        mr.name as room_name,
        mr.location
    FROM bookings b
    JOIN meeting_rooms mr ON b.meeting_room_id = mr.id
    WHERE b.user_id = user_id
    ORDER BY b.start_time DESC;
END //

DELIMITER ;

-- =====================================================
-- 9. TRIGGERS
-- =====================================================

-- Trigger to update total_cost when booking is created/updated
DELIMITER //
CREATE TRIGGER calculate_booking_cost
BEFORE INSERT ON bookings
FOR EACH ROW
BEGIN
    DECLARE room_rate DECIMAL(8,2);
    DECLARE duration_hours DECIMAL(8,2);
    
    -- Get room hourly rate
    SELECT hourly_rate INTO room_rate 
    FROM meeting_rooms 
    WHERE id = NEW.meeting_room_id;
    
    -- Calculate duration in hours
    SET duration_hours = TIMESTAMPDIFF(MINUTE, NEW.start_time, NEW.end_time) / 60.0;
    
    -- Calculate total cost
    SET NEW.total_cost = room_rate * duration_hours;
END //
DELIMITER ;

-- =====================================================
-- 10. INDEXES FOR PERFORMANCE
-- =====================================================

-- Additional indexes for better performance
CREATE INDEX idx_bookings_date_range ON bookings(start_time, end_time);
CREATE INDEX idx_meeting_rooms_active ON meeting_rooms(is_active, capacity);
CREATE INDEX idx_users_department ON users(department);
CREATE INDEX idx_users_last_login ON users(last_login_at);

-- =====================================================
-- COMPLETION MESSAGE
-- =====================================================

SELECT 'Database schema created successfully!' as message;
SELECT 'Tables created: cache, cache_locks, jobs, job_batches, failed_jobs, users, password_reset_tokens, sessions, admin_sessions, meeting_rooms, bookings' as tables_created;
SELECT 'Views created: active_bookings_view, room_availability_view' as views_created;
SELECT 'Procedures created: CheckRoomAvailability, GetUserBookingHistory' as procedures_created;
SELECT 'Sample data inserted for testing' as sample_data;
