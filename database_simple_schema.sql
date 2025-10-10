-- =====================================================
-- MEETING ROOM BOOKING SYSTEM - SIMPLE DATABASE SCHEMA
-- =====================================================
-- Script sederhana untuk database meeting room booking
-- Hanya tabel utama tanpa fitur advanced

-- Drop existing tables if they exist
DROP TABLE IF EXISTS `bookings`;
DROP TABLE IF EXISTS `meeting_rooms`;
DROP TABLE IF EXISTS `users`;

-- =====================================================
-- 1. USERS TABLE
-- =====================================================
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20) NULL,
    `department` VARCHAR(50) NULL,
    `role` ENUM('admin', 'user') DEFAULT 'user',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- 2. MEETING ROOMS TABLE
-- =====================================================
CREATE TABLE `meeting_rooms` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT NULL,
    `capacity` INT NOT NULL,
    `location` VARCHAR(100) NOT NULL,
    `hourly_rate` DECIMAL(10,2) DEFAULT 0.00,
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- 3. BOOKINGS TABLE
-- =====================================================
CREATE TABLE `bookings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `meeting_room_id` INT NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT NULL,
    `start_time` DATETIME NOT NULL,
    `end_time` DATETIME NOT NULL,
    `status` ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    `attendees_count` INT DEFAULT 1,
    `total_cost` DECIMAL(10,2) DEFAULT 0.00,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`meeting_room_id`) REFERENCES `meeting_rooms`(`id`) ON DELETE CASCADE,
    
    INDEX `idx_user_time` (`user_id`, `start_time`),
    INDEX `idx_room_time` (`meeting_room_id`, `start_time`, `end_time`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- 4. SAMPLE DATA
-- =====================================================

-- Insert admin user
INSERT INTO `users` (`username`, `name`, `email`, `password`, `phone`, `department`, `role`) VALUES
('admin', 'Administrator', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08123456789', 'IT', 'admin');

-- Insert regular user
INSERT INTO `users` (`username`, `name`, `email`, `password`, `phone`, `department`, `role`) VALUES
('john.doe', 'John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08123456790', 'Marketing', 'user');

-- Insert meeting rooms
INSERT INTO `meeting_rooms` (`name`, `description`, `capacity`, `location`, `hourly_rate`) VALUES
('Conference Room A', 'Large conference room with projector', 20, 'Floor 1, Building A', 150000.00),
('Meeting Room B', 'Small meeting room for 8 people', 8, 'Floor 2, Building A', 75000.00),
('Executive Boardroom', 'Premium boardroom for executives', 12, 'Floor 3, Building A', 250000.00),
('Training Room', 'Large space for training sessions', 50, 'Floor 1, Building B', 200000.00);

-- Insert sample bookings
INSERT INTO `bookings` (`user_id`, `meeting_room_id`, `title`, `start_time`, `end_time`, `status`, `attendees_count`, `total_cost`) VALUES
(2, 1, 'Weekly Team Meeting', DATE_ADD(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 2 HOUR, 'confirmed', 5, 300000.00),
(2, 2, 'Client Presentation', DATE_ADD(NOW(), INTERVAL 3 DAY), DATE_ADD(NOW(), INTERVAL 3 DAY) + INTERVAL 1 HOUR, 'pending', 3, 75000.00),
(2, 3, 'Board Meeting', DATE_ADD(NOW(), INTERVAL 7 DAY), DATE_ADD(NOW(), INTERVAL 7 DAY) + INTERVAL 3 HOUR, 'confirmed', 8, 750000.00);

-- =====================================================
-- 5. USEFUL VIEWS
-- =====================================================

-- View for booking details with user and room info
CREATE VIEW `booking_details` AS
SELECT 
    b.id,
    b.title,
    b.start_time,
    b.end_time,
    b.status,
    b.total_cost,
    u.username,
    u.name as user_name,
    u.email as user_email,
    mr.name as room_name,
    mr.location,
    mr.capacity
FROM `bookings` b
JOIN `users` u ON b.user_id = u.id
JOIN `meeting_rooms` mr ON b.meeting_room_id = mr.id
ORDER BY b.start_time DESC;

-- View for room availability
CREATE VIEW `room_availability` AS
SELECT 
    mr.id,
    mr.name,
    mr.capacity,
    mr.location,
    mr.hourly_rate,
    mr.is_active,
    COUNT(b.id) as active_bookings
FROM `meeting_rooms` mr
LEFT JOIN `bookings` b ON mr.id = b.meeting_room_id 
    AND b.status IN ('pending', 'confirmed')
    AND b.start_time >= NOW()
GROUP BY mr.id, mr.name, mr.capacity, mr.location, mr.hourly_rate, mr.is_active;

-- =====================================================
-- COMPLETION MESSAGE
-- =====================================================
SELECT 'Simple database schema created successfully!' as message;
SELECT 'Tables: users, meeting_rooms, bookings' as tables_created;
SELECT 'Views: booking_details, room_availability' as views_created;
SELECT 'Sample data inserted for testing' as sample_data;
