<?php
/**
 * Script untuk test user registration
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

echo "=== Test User Registration ===\n\n";

try {
    // Simulate registration data
    $registrationData = [
        'username' => 'testuser123',
        'full_name' => 'Test User',
        'email' => 'testuser123@example.com',
        'password' => 'password123',
        'phone' => '08123456789',
        'department' => 'IT',
    ];
    
    echo "Registration data:\n";
    foreach ($registrationData as $key => $value) {
        if ($key === 'password') {
            echo "- $key: " . str_repeat('*', strlen($value)) . "\n";
        } else {
            echo "- $key: $value\n";
        }
    }
    
    // Check if user already exists
    $existingUser = User::where('email', $registrationData['email'])
                       ->orWhere('username', $registrationData['username'])
                       ->first();
    
    if ($existingUser) {
        echo "\n⚠️  User already exists, deleting for test...\n";
        $existingUser->delete();
    }
    
    // Create user (simulate AuthController::register)
    $verificationToken = Str::random(64);
    
    $user = User::create([
        'username' => $registrationData['username'],
        'name' => $registrationData['full_name'],
        'full_name' => $registrationData['full_name'],
        'email' => $registrationData['email'],
        'password' => Hash::make($registrationData['password']),
        'phone' => $registrationData['phone'],
        'department' => $registrationData['department'],
        'role' => 'user',
        'email_verified_at' => null,
        'email_verification_token' => Hash::make($verificationToken),
    ]);
    
    echo "\n✅ User created successfully!\n";
    echo "User ID: {$user->id}\n";
    echo "Username: {$user->username}\n";
    echo "Name: {$user->name}\n";
    echo "Full Name: {$user->full_name}\n";
    echo "Email: {$user->email}\n";
    echo "Role: {$user->role}\n";
    
    // Clean up test user
    $user->delete();
    echo "\n🧹 Test user cleaned up.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
