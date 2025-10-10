<?php
/**
 * Script untuk memeriksa kolom yang hilang di tabel users
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking Missing Columns in Users Table ===\n\n";

try {
    // Get current table structure
    $columns = DB::select('DESCRIBE users');
    $existingColumns = array_column($columns, 'Field');
    
    echo "Current columns in users table:\n";
    foreach ($existingColumns as $column) {
        echo "- $column\n";
    }
    
    // Required columns based on User model
    $requiredColumns = [
        'id',
        'name',
        'username', 
        'email',
        'password',
        'full_name',
        'phone',
        'department',
        'role',
        'avatar',
        'last_login_at',
        'email_verified_at',
        'email_verification_token',
        'remember_token',
        'created_at',
        'updated_at'
    ];
    
    echo "\nRequired columns based on User model:\n";
    foreach ($requiredColumns as $column) {
        echo "- $column\n";
    }
    
    // Check missing columns
    $missingColumns = array_diff($requiredColumns, $existingColumns);
    
    if (empty($missingColumns)) {
        echo "\n✅ All required columns are present!\n";
    } else {
        echo "\n❌ Missing columns:\n";
        foreach ($missingColumns as $column) {
            echo "- $column\n";
        }
        
        echo "\nSQL to add missing columns:\n";
        foreach ($missingColumns as $column) {
            switch ($column) {
                case 'name':
                    echo "ALTER TABLE users ADD COLUMN name VARCHAR(255) NOT NULL DEFAULT '';\n";
                    break;
                case 'username':
                    echo "ALTER TABLE users ADD COLUMN username VARCHAR(255) NOT NULL DEFAULT '';\n";
                    break;
                case 'full_name':
                    echo "ALTER TABLE users ADD COLUMN full_name VARCHAR(255) NOT NULL DEFAULT '';\n";
                    break;
                case 'phone':
                    echo "ALTER TABLE users ADD COLUMN phone VARCHAR(255) NULL;\n";
                    break;
                case 'department':
                    echo "ALTER TABLE users ADD COLUMN department VARCHAR(255) NULL;\n";
                    break;
                case 'role':
                    echo "ALTER TABLE users ADD COLUMN role ENUM('admin','user') NOT NULL DEFAULT 'user';\n";
                    break;
                case 'avatar':
                    echo "ALTER TABLE users ADD COLUMN avatar VARCHAR(255) NULL;\n";
                    break;
                case 'last_login_at':
                    echo "ALTER TABLE users ADD COLUMN last_login_at TIMESTAMP NULL;\n";
                    break;
                case 'email_verified_at':
                    echo "ALTER TABLE users ADD COLUMN email_verified_at TIMESTAMP NULL;\n";
                    break;
                case 'email_verification_token':
                    echo "ALTER TABLE users ADD COLUMN email_verification_token VARCHAR(255) NULL;\n";
                    break;
                case 'remember_token':
                    echo "ALTER TABLE users ADD COLUMN remember_token VARCHAR(100) NULL;\n";
                    break;
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
