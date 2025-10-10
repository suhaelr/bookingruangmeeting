<?php
/**
 * Script untuk memeriksa struktur tabel users
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking Users Table Structure ===\n";

try {
    $columns = DB::select('DESCRIBE users');
    
    echo "Columns in users table:\n";
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type}) - {$column->Null} - {$column->Key}\n";
    }
    
    // Check if last_login_at exists
    $hasLastLogin = false;
    foreach ($columns as $column) {
        if ($column->Field === 'last_login_at') {
            $hasLastLogin = true;
            break;
        }
    }
    
    if ($hasLastLogin) {
        echo "\n✅ last_login_at column exists\n";
    } else {
        echo "\n❌ last_login_at column does NOT exist\n";
        echo "This is the problem!\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
