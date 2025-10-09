-- =============================================
-- DATABASE UPDATE FOR AUTH FEATURES
-- Meeting Room Booking System
-- =============================================

-- 1. CREATE PASSWORD RESET TOKENS TABLE
-- =============================================
-- This table stores password reset tokens for users
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
    `email` varchar(255) NOT NULL,
    `token` varchar(255) NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. UPDATE USERS TABLE (if needed)
-- =============================================
-- Add last_login_at column if it doesn't exist
ALTER TABLE `users` 
ADD COLUMN IF NOT EXISTS `last_login_at` timestamp NULL DEFAULT NULL AFTER `updated_at`;

-- 3. UPDATE DECIMAL PRECISION (if not already done)
-- =============================================
-- Update total_cost and hourly_rate to support larger values
ALTER TABLE `bookings` 
MODIFY COLUMN `total_cost` decimal(12,2) NOT NULL DEFAULT '0.00';

ALTER TABLE `meeting_rooms` 
MODIFY COLUMN `hourly_rate` decimal(12,2) NOT NULL DEFAULT '0.00';

-- 4. INSERT SAMPLE DATA (Optional)
-- =============================================
-- Insert a sample admin user for testing
INSERT IGNORE INTO `users` (
    `id`, `username`, `name`, `full_name`, `email`, `password`, 
    `phone`, `department`, `role`, `email_verified_at`, `created_at`, `updated_at`
) VALUES (
    1, 'admin', 'Super Administrator', 'Super Administrator', 
    'admin@jadixpert.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
    '081234567890', 'IT', 'admin', NOW(), NOW(), NOW()
);

-- Insert a sample regular user for testing
INSERT IGNORE INTO `users` (
    `id`, `username`, `name`, `full_name`, `email`, `password`, 
    `phone`, `department`, `role`, `email_verified_at`, `created_at`, `updated_at`
) VALUES (
    2, 'user', 'Regular User', 'Regular User', 
    'user@jadixpert.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
    '081234567891', 'General', 'user', NOW(), NOW(), NOW()
);

-- =============================================
-- END OF DATABASE UPDATE
-- =============================================

-- NOTES:
-- 1. Run this SQL in phpMyAdmin or MySQL command line
-- 2. The password for both sample users is 'password'
-- 3. Make sure to update the .env file with mail configuration
-- 4. Test the registration and password reset features after running this SQL
