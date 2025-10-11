<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Fixing user ID mismatch issue...\n";

// Check if user ID 9 exists
$user9 = User::find(9);
if ($user9) {
    echo "User ID 9 exists: {$user9->name} - {$user9->email} - Role: {$user9->role}\n";
    
    // Update role to admin
    echo "Updating user ID 9 role to admin...\n";
    $user9->update(['role' => 'admin']);
    $user9->refresh();
    echo "Updated role: {$user9->role}\n";
    
} else {
    echo "User ID 9 does not exist in database.\n";
    
    // Check if there are users with similar names
    $similarUsers = User::where('name', 'LIKE', '%Suhael%')->get();
    echo "Users with 'Suhael' in name:\n";
    foreach ($similarUsers as $user) {
        echo "- ID: {$user->id}, Name: {$user->name}, Email: {$user->email}, Role: {$user->role}\n";
    }
}

// Test API endpoint
echo "\nTesting getAllUsers API...\n";
$controller = new \App\Http\Controllers\AuthController();

// Set up admin session
session(['user_logged_in' => true]);
session(['user_data' => [
    'id' => 1,
    'role' => 'admin',
    'email' => 'admin@pusdatinbgn.web.id'
]]);

$response = $controller->getAllUsers();
$responseData = json_decode($response->getContent(), true);

echo "API Response success: " . ($responseData['success'] ? 'true' : 'false') . "\n";
echo "Users count: " . count($responseData['users']) . "\n";

foreach ($responseData['users'] as $index => $userData) {
    echo "User {$index}: ID={$userData['id']}, Name={$userData['name']}, Role={$userData['role']}\n";
}
