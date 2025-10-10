-- SQL yang aman untuk memperbaiki tabel users di production
-- Menggunakan stored procedure untuk menangani error

DELIMITER //

CREATE PROCEDURE AddMissingColumns()
BEGIN
    -- Handler untuk error "Duplicate column name" (1060)
    DECLARE CONTINUE HANDLER FOR 1060 DO BEGIN 
        SELECT CONCAT('Column already exists: ', @column_name) AS message;
    END;
    
    -- Tambahkan kolom last_login_at
    SET @column_name = 'last_login_at';
    ALTER TABLE users ADD COLUMN last_login_at TIMESTAMP NULL;
    
    -- Tambahkan kolom avatar
    SET @column_name = 'avatar';
    ALTER TABLE users ADD COLUMN avatar VARCHAR(255) NULL;
    
    -- Tambahkan kolom username
    SET @column_name = 'username';
    ALTER TABLE users ADD COLUMN username VARCHAR(255) NOT NULL DEFAULT '';
    
    -- Tambahkan kolom full_name
    SET @column_name = 'full_name';
    ALTER TABLE users ADD COLUMN full_name VARCHAR(255) NOT NULL DEFAULT '';
    
    -- Tambahkan kolom phone
    SET @column_name = 'phone';
    ALTER TABLE users ADD COLUMN phone VARCHAR(255) NULL;
    
    -- Tambahkan kolom department
    SET @column_name = 'department';
    ALTER TABLE users ADD COLUMN department VARCHAR(255) NULL;
    
    -- Tambahkan kolom role
    SET @column_name = 'role';
    ALTER TABLE users ADD COLUMN role ENUM('admin','user') NOT NULL DEFAULT 'user';
    
    -- Tambahkan kolom email_verification_token
    SET @column_name = 'email_verification_token';
    ALTER TABLE users ADD COLUMN email_verification_token VARCHAR(255) NULL;
    
    SELECT 'All columns added successfully!' AS result;
END //

DELIMITER ;

-- Jalankan stored procedure
CALL AddMissingColumns();

-- Hapus stored procedure
DROP PROCEDURE AddMissingColumns;

-- Verifikasi struktur tabel
DESCRIBE users;
