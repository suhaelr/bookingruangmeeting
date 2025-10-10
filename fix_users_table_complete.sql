-- SQL lengkap untuk memperbaiki tabel users di production
-- Jalankan satu per satu untuk menghindari error

-- 1. Tambahkan kolom name (PENTING untuk registrasi)
ALTER TABLE users ADD COLUMN name VARCHAR(255) NOT NULL DEFAULT '';

-- 2. Tambahkan kolom username jika belum ada
ALTER TABLE users ADD COLUMN username VARCHAR(255) NOT NULL DEFAULT '';

-- 3. Tambahkan kolom full_name jika belum ada
ALTER TABLE users ADD COLUMN full_name VARCHAR(255) NOT NULL DEFAULT '';

-- 4. Tambahkan kolom phone jika belum ada
ALTER TABLE users ADD COLUMN phone VARCHAR(255) NULL;

-- 5. Tambahkan kolom department jika belum ada
ALTER TABLE users ADD COLUMN department VARCHAR(255) NULL;

-- 6. Tambahkan kolom role jika belum ada
ALTER TABLE users ADD COLUMN role ENUM('admin','user') NOT NULL DEFAULT 'user';

-- 7. Tambahkan kolom avatar jika belum ada
ALTER TABLE users ADD COLUMN avatar VARCHAR(255) NULL;

-- 8. Tambahkan kolom last_login_at jika belum ada
ALTER TABLE users ADD COLUMN last_login_at TIMESTAMP NULL;

-- 9. Tambahkan kolom email_verified_at jika belum ada
ALTER TABLE users ADD COLUMN email_verified_at TIMESTAMP NULL;

-- 10. Tambahkan kolom email_verification_token jika belum ada
ALTER TABLE users ADD COLUMN email_verification_token VARCHAR(255) NULL;

-- 11. Tambahkan kolom remember_token jika belum ada
ALTER TABLE users ADD COLUMN remember_token VARCHAR(100) NULL;

-- 12. Verifikasi struktur tabel
DESCRIBE users;
