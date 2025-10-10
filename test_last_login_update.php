<?php
/**
 * Script untuk test update last_login_at
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== Testing last_login_at Update ===\n";

try {
    // Find user with ID 2
    $user = User::find(2);
    
    if (!$user) {
        echo "❌ User with ID 2 not found\n";
        exit;
    }
    
    echo "✅ User found: {$user->username} ({$user->email})\n";
    
    // Check if last_login_at column exists in fillable
    $fillable = $user->getFillable();
    if (in_array('last_login_at', $fillable)) {
        echo "✅ last_login_at is in fillable array\n";
    } else {
        echo "❌ last_login_at is NOT in fillable array\n";
    }
    
    // Try to update last_login_at
    echo "Attempting to update last_login_at...\n";
    
    $result = $user->update(['last_login_at' => now()]);
    
    if ($result) {
        echo "✅ last_login_at updated successfully\n";
        echo "New last_login_at: {$user->fresh()->last_login_at}\n";
    } else {
        echo "❌ Failed to update last_login_at\n";
    }
    
    // Check current last_login_at value
    $user->refresh();
    echo "Current last_login_at: " . ($user->last_login_at ? $user->last_login_at : 'NULL') . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
