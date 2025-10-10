<?php
/**
 * Script untuk membuat SQL yang aman untuk menambahkan kolom yang hilang
 */

echo "=== Safe SQL untuk Menambahkan Kolom yang Hilang ===\n\n";

echo "Masalah: Kolom 'avatar' tidak ada, jadi tidak bisa menggunakan 'AFTER avatar'\n\n";

echo "Solusi: Gunakan SQL yang aman tanpa referensi ke kolom yang mungkin tidak ada\n\n";

echo "=== SQL yang Aman untuk Production ===\n\n";

echo "-- 1. Tambahkan kolom last_login_at (tanpa AFTER)\n";
echo "ALTER TABLE users ADD COLUMN last_login_at TIMESTAMP NULL;\n\n";

echo "-- 2. Tambahkan kolom avatar jika belum ada\n";
echo "ALTER TABLE users ADD COLUMN avatar VARCHAR(255) NULL;\n\n";

echo "-- 3. Tambahkan kolom username jika belum ada\n";
echo "ALTER TABLE users ADD COLUMN username VARCHAR(255) NOT NULL DEFAULT '';\n\n";

echo "-- 4. Tambahkan kolom full_name jika belum ada\n";
echo "ALTER TABLE users ADD COLUMN full_name VARCHAR(255) NOT NULL DEFAULT '';\n\n";

echo "-- 5. Tambahkan kolom phone jika belum ada\n";
echo "ALTER TABLE users ADD COLUMN phone VARCHAR(255) NULL;\n\n";

echo "-- 6. Tambahkan kolom department jika belum ada\n";
echo "ALTER TABLE users ADD COLUMN department VARCHAR(255) NULL;\n\n";

echo "-- 7. Tambahkan kolom role jika belum ada\n";
echo "ALTER TABLE users ADD COLUMN role ENUM('admin','user') NOT NULL DEFAULT 'user';\n\n";

echo "-- 8. Tambahkan kolom email_verification_token jika belum ada\n";
echo "ALTER TABLE users ADD COLUMN email_verification_token VARCHAR(255) NULL;\n\n";

echo "=== SQL dengan IF NOT EXISTS (MySQL 8.0+) ===\n\n";

echo "-- Alternatif untuk MySQL 8.0+ dengan IF NOT EXISTS\n";
echo "ALTER TABLE users ADD COLUMN IF NOT EXISTS last_login_at TIMESTAMP NULL;\n";
echo "ALTER TABLE users ADD COLUMN IF NOT EXISTS avatar VARCHAR(255) NULL;\n";
echo "ALTER TABLE users ADD COLUMN IF NOT EXISTS username VARCHAR(255) NOT NULL DEFAULT '';\n";
echo "ALTER TABLE users ADD COLUMN IF NOT EXISTS full_name VARCHAR(255) NOT NULL DEFAULT '';\n";
echo "ALTER TABLE users ADD COLUMN IF NOT EXISTS phone VARCHAR(255) NULL;\n";
echo "ALTER TABLE users ADD COLUMN IF NOT EXISTS department VARCHAR(255) NULL;\n";
echo "ALTER TABLE users ADD COLUMN IF NOT EXISTS role ENUM('admin','user') NOT NULL DEFAULT 'user';\n";
echo "ALTER TABLE users ADD COLUMN IF NOT EXISTS email_verification_token VARCHAR(255) NULL;\n\n";

echo "=== SQL dengan Error Handling ===\n\n";

echo "-- Gunakan stored procedure untuk menangani error\n";
echo "DELIMITER //\n";
echo "CREATE PROCEDURE AddMissingColumns()\n";
echo "BEGIN\n";
echo "    DECLARE CONTINUE HANDLER FOR 1060 DO BEGIN END;\n";
echo "    \n";
echo "    ALTER TABLE users ADD COLUMN last_login_at TIMESTAMP NULL;\n";
echo "    ALTER TABLE users ADD COLUMN avatar VARCHAR(255) NULL;\n";
echo "    ALTER TABLE users ADD COLUMN username VARCHAR(255) NOT NULL DEFAULT '';\n";
echo "    ALTER TABLE users ADD COLUMN full_name VARCHAR(255) NOT NULL DEFAULT '';\n";
echo "    ALTER TABLE users ADD COLUMN phone VARCHAR(255) NULL;\n";
echo "    ALTER TABLE users ADD COLUMN department VARCHAR(255) NULL;\n";
echo "    ALTER TABLE users ADD COLUMN role ENUM('admin','user') NOT NULL DEFAULT 'user';\n";
echo "    ALTER TABLE users ADD COLUMN email_verification_token VARCHAR(255) NULL;\n";
echo "END //\n";
echo "DELIMITER ;\n\n";
echo "CALL AddMissingColumns();\n";
echo "DROP PROCEDURE AddMissingColumns;\n\n";

echo "=== Rekomendasi ===\n\n";
echo "Gunakan SQL pertama (tanpa IF NOT EXISTS) karena lebih kompatibel.\n";
echo "Jika ada error 'Duplicate column name', abaikan saja.\n";
echo "Yang penting kolom last_login_at ditambahkan.\n";
?>
