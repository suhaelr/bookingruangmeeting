<?php
/**
 * Script untuk debug last_login_at issue
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== Debug last_login_at Issue ===\n";

try {
    // Check database connection
    echo "1. Database Connection:\n";
    $connection = DB::connection()->getPdo();
    echo "✅ Database connected successfully\n";
    
    // Check users table structure
    echo "\n2. Users Table Structure:\n";
    $columns = DB::select('DESCRIBE users');
    $hasLastLogin = false;
    foreach ($columns as $column) {
        if ($column->Field === 'last_login_at') {
            $hasLastLogin = true;
            echo "✅ last_login_at column found: {$column->Type}\n";
            break;
        }
    }
    
    if (!$hasLastLogin) {
        echo "❌ last_login_at column NOT found\n";
        exit;
    }
    
    // Check if user exists
    echo "\n3. User Check:\n";
    $user = User::find(2);
    if (!$user) {
        echo "❌ User with ID 2 not found\n";
        exit;
    }
    echo "✅ User found: {$user->username} ({$user->email})\n";
    
    // Check fillable attributes
    echo "\n4. Model Configuration:\n";
    $fillable = $user->getFillable();
    echo "Fillable attributes: " . implode(', ', $fillable) . "\n";
    
    if (in_array('last_login_at', $fillable)) {
        echo "✅ last_login_at is fillable\n";
    } else {
        echo "❌ last_login_at is NOT fillable\n";
    }
    
    // Check casts
    $casts = $user->getCasts();
    echo "Casts: " . json_encode($casts) . "\n";
    
    if (isset($casts['last_login_at'])) {
        echo "✅ last_login_at has cast: {$casts['last_login_at']}\n";
    } else {
        echo "❌ last_login_at has NO cast\n";
    }
    
    // Try direct SQL update
    echo "\n5. Direct SQL Update Test:\n";
    $now = now()->format('Y-m-d H:i:s');
    $result = DB::update("UPDATE users SET last_login_at = ? WHERE id = ?", [$now, 2]);
    echo "Direct SQL update result: {$result} rows affected\n";
    
    // Check if update worked
    $user->refresh();
    echo "User last_login_at after direct SQL: " . ($user->last_login_at ? $user->last_login_at : 'NULL') . "\n";
    
    // Try Eloquent update
    echo "\n6. Eloquent Update Test:\n";
    $result = $user->update(['last_login_at' => now()]);
    echo "Eloquent update result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
    
    $user->refresh();
    echo "User last_login_at after Eloquent: " . ($user->last_login_at ? $user->last_login_at : 'NULL') . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
