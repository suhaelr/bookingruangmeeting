-- SQL Migration: Tambah kolom unit_kerja ke tabel users
-- Jalankan manual di database jika kolom unit_kerja belum ada

-- Cek apakah kolom unit_kerja sudah ada
-- Jika belum ada, jalankan query di bawah ini:

ALTER TABLE users 
ADD COLUMN IF NOT EXISTS unit_kerja VARCHAR(255) NULL 
AFTER department;

-- Jika database tidak mendukung IF NOT EXISTS, gunakan query ini:
-- ALTER TABLE users 
-- ADD COLUMN unit_kerja VARCHAR(255) NULL 
-- AFTER department;

-- Verifikasi kolom sudah ditambahkan
-- DESCRIBE users;

