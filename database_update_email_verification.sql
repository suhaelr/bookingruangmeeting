-- =============================================
-- DATABASE UPDATE FOR EMAIL VERIFICATION
-- Meeting Room Booking System
-- =============================================

-- 1. ADD EMAIL VERIFICATION TOKEN TO USERS TABLE
-- =============================================
-- Add email_verification_token column for email verification
ALTER TABLE `users` 
ADD COLUMN IF NOT EXISTS `email_verification_token` varchar(255) NULL AFTER `email_verified_at`;

-- 2. UPDATE EXISTING USERS (Optional)
-- =============================================
-- Mark existing users as verified (if you want to keep them active)
UPDATE `users` 
SET `email_verified_at` = NOW() 
WHERE `email_verified_at` IS NULL AND `email_verification_token` IS NULL;

-- 3. CREATE PASSWORD RESET TOKENS TABLE (if not exists)
-- =============================================
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
    `email` varchar(255) NOT NULL,
    `token` varchar(255) NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. ADD LAST LOGIN COLUMN (if not exists)
-- =============================================
ALTER TABLE `users` 
ADD COLUMN IF NOT EXISTS `last_login_at` timestamp NULL DEFAULT NULL AFTER `updated_at`;

-- 5. UPDATE DECIMAL PRECISION (if not already done)
-- =============================================
-- Update total_cost and hourly_rate to support larger values
ALTER TABLE `bookings` 
MODIFY COLUMN `total_cost` decimal(12,2) NOT NULL DEFAULT '0.00';

ALTER TABLE `meeting_rooms` 
MODIFY COLUMN `hourly_rate` decimal(12,2) NOT NULL DEFAULT '0.00';

-- =============================================
-- END OF DATABASE UPDATE
-- =============================================

-- NOTES:
-- 1. Run this SQL in phpMyAdmin or MySQL command line
-- 2. After running this SQL, users must verify their email before login
-- 3. Make sure to update the .env file with mail configuration
-- 4. Test email functionality with: php artisan test:email your_email@example.com
-- 5. Check logs in storage/logs/laravel.log for email debugging
