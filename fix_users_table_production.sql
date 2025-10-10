-- SQL untuk memperbaiki tabel users di production
-- Jalankan satu per satu untuk menghindari error

-- 1. Tambahkan kolom last_login_at (PENTING untuk fix error login)
ALTER TABLE users ADD COLUMN last_login_at TIMESTAMP NULL;

-- 2. Tambahkan kolom avatar
ALTER TABLE users ADD COLUMN avatar VARCHAR(255) NULL;

-- 3. Tambahkan kolom username
ALTER TABLE users ADD COLUMN username VARCHAR(255) NOT NULL DEFAULT '';

-- 4. Tambahkan kolom full_name
ALTER TABLE users ADD COLUMN full_name VARCHAR(255) NOT NULL DEFAULT '';

-- 5. Tambahkan kolom phone
ALTER TABLE users ADD COLUMN phone VARCHAR(255) NULL;

-- 6. Tambahkan kolom department
ALTER TABLE users ADD COLUMN department VARCHAR(255) NULL;

-- 7. Tambahkan kolom role
ALTER TABLE users ADD COLUMN role ENUM('admin','user') NOT NULL DEFAULT 'user';

-- 8. Tambahkan kolom email_verification_token
ALTER TABLE users ADD COLUMN email_verification_token VARCHAR(255) NULL;

-- 9. Verifikasi struktur tabel
DESCRIBE users;
