<?php
/**
 * Script untuk memperbaiki masalah migrasi di production
 */

echo "=== Fix Production Migration Issue ===\n";

// Koneksi ke database
try {
    $pdo = new PDO(
        'mysql:host=127.0.0.1;dbname=dsvbpgpt_lara812',
        'dsvbpgpt_lara812',
        'superadmin123'
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Database connection successful\n";
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Cek apakah tabel admin_sessions sudah ada
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'admin_sessions'");
    $tableExists = $stmt->rowCount() > 0;
    
    if ($tableExists) {
        echo "✅ Table admin_sessions already exists\n";
        
        // Cek struktur tabel
        $stmt = $pdo->query("DESCRIBE admin_sessions");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Table structure:\n";
        foreach ($columns as $column) {
            echo "- {$column['Field']} ({$column['Type']})\n";
        }
        
        // Cek apakah tabel memiliki struktur yang benar
        $hasUserId = false;
        $hasPayload = false;
        foreach ($columns as $column) {
            if ($column['Field'] === 'user_id') $hasUserId = true;
            if ($column['Field'] === 'payload') $hasPayload = true;
        }
        
        if ($hasUserId && $hasPayload) {
            echo "✅ Table has correct structure (updated version)\n";
        } else {
            echo "⚠️ Table has old structure, needs update\n";
        }
        
    } else {
        echo "❌ Table admin_sessions does not exist\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Error checking table: " . $e->getMessage() . "\n";
}

// Cek status migrasi
try {
    $stmt = $pdo->query("SELECT * FROM migrations WHERE migration = '2024_01_01_000000_create_admin_sessions_table'");
    $migration = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($migration) {
        echo "✅ Migration 2024_01_01_000000_create_admin_sessions_table is recorded\n";
        echo "Batch: {$migration['batch']}\n";
    } else {
        echo "❌ Migration 2024_01_01_000000_create_admin_sessions_table is NOT recorded\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Error checking migration status: " . $e->getMessage() . "\n";
}

echo "\n=== Recommendations ===\n";
echo "1. If table exists with correct structure, mark migration as completed\n";
echo "2. If table exists with old structure, update it\n";
echo "3. If table doesn't exist, create it\n";

echo "\n=== Fix Commands ===\n";
echo "Option 1 - Mark migration as completed:\n";
echo "INSERT INTO migrations (migration, batch) VALUES ('2024_01_01_000000_create_admin_sessions_table', 1);\n\n";

echo "Option 2 - Drop and recreate table:\n";
echo "DROP TABLE IF EXISTS admin_sessions;\n";
echo "Then run: php artisan migrate\n\n";

echo "Option 3 - Skip problematic migration:\n";
echo "php artisan migrate --path=database/migrations/2024_01_01_000002_create_meeting_rooms_table.php\n";
echo "php artisan migrate --path=database/migrations/2024_01_01_000003_create_bookings_table.php\n";

echo "\n=== Script Complete ===\n";
?>
