-- ============================================
-- Migration: Make capacity nullable in meeting_rooms table
-- Date: 2025-11-05
-- Description: Change capacity column from NOT NULL to NULL to allow optional capacity
-- ============================================

-- For MySQL
ALTER TABLE `meeting_rooms` MODIFY COLUMN `capacity` INTEGER NULL;

-- Verification query (optional - uncomment to check)
-- DESCRIBE `meeting_rooms`;
-- SELECT COLUMN_NAME, IS_NULLABLE, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'meeting_rooms' AND COLUMN_NAME = 'capacity';

