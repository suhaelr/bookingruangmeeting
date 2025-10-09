-- =============================================
-- FIX DECIMAL PRECISION FOR TOTAL_COST
-- =============================================

-- Update total_cost column to support larger values
-- Change from decimal(8,2) to decimal(12,2) to support up to 9,999,999,999.99
ALTER TABLE `bookings` MODIFY COLUMN `total_cost` decimal(12,2) NOT NULL DEFAULT '0.00';

-- Update hourly_rate column to support larger values
-- Change from decimal(8,2) to decimal(12,2) to support up to 9,999,999,999.99
ALTER TABLE `meeting_rooms` MODIFY COLUMN `hourly_rate` decimal(12,2) NOT NULL DEFAULT '0.00';

-- =============================================
-- END OF DECIMAL PRECISION FIX
-- =============================================

-- NOTES:
-- 1. This will fix the "Numeric value out of range" error
-- 2. New maximum values:
--    - total_cost: 9,999,999,999.99 (almost 10 billion)
--    - hourly_rate: 9,999,999,999.99 (almost 10 billion)
-- 3. This should handle even very expensive meeting rooms
-- 4. Run this SQL in phpMyAdmin to apply the changes
