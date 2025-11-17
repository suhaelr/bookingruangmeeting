-- Migration: Add Attendance Confirmation Columns to meeting_invitations Table
-- Description: Menambahkan kolom untuk tracking konfirmasi kehadiran PIC di meeting
-- Date: November 2025

-- Add attendance_status column
ALTER TABLE `meeting_invitations` 
ADD COLUMN `attendance_status` ENUM('pending', 'confirmed', 'declined', 'absent') 
NOT NULL DEFAULT 'pending' 
COMMENT 'Status konfirmasi kehadiran: pending (belum ada respon), confirmed (akan hadir), declined (tidak bisa hadir), absent (tidak hadir - auto setelah meeting lewat)' 
AFTER `status`;

-- Add attendance_confirmed_at column
ALTER TABLE `meeting_invitations` 
ADD COLUMN `attendance_confirmed_at` TIMESTAMP NULL 
COMMENT 'Waktu ketika PIC mengkonfirmasi akan hadir' 
AFTER `attendance_status`;

-- Add attendance_declined_at column
ALTER TABLE `meeting_invitations` 
ADD COLUMN `attendance_declined_at` TIMESTAMP NULL 
COMMENT 'Waktu ketika PIC mengkonfirmasi tidak bisa hadir' 
AFTER `attendance_confirmed_at`;

-- Add index for attendance_status for better query performance
ALTER TABLE `meeting_invitations` 
ADD INDEX `idx_attendance_status` (`attendance_status`);

-- Add index for booking_id and attendance_status combination
ALTER TABLE `meeting_invitations` 
ADD INDEX `idx_booking_attendance` (`booking_id`, `attendance_status`);

-- Update existing records: Set attendance_status based on current status
-- If status is 'accepted', set attendance_status to 'pending' (belum konfirmasi kehadiran)
-- If status is 'declined', set attendance_status to 'declined' (tidak bisa hadir)
-- If status is 'invited', set attendance_status to 'pending' (belum ada respon)
UPDATE `meeting_invitations` 
SET `attendance_status` = CASE 
    WHEN `status` = 'declined' THEN 'declined'
    WHEN `status` = 'accepted' THEN 'pending'
    WHEN `status` = 'invited' THEN 'pending'
    ELSE 'pending'
END;

-- Note: 
-- - attendance_status 'pending' = Belum ada respon (kuning di kalender)
-- - attendance_status 'confirmed' = Dikonfirmasi akan hadir (hijau di kalender)
-- - attendance_status 'declined' = Belum bisa hadir (merah di kalender)
-- - attendance_status 'absent' = Tidak hadir (auto setelah meeting lewat) (merah di kalender)

