<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Debugging role update issue...\n";

// Find user by ID 9
$user = User::find(9);
if ($user) {
    echo "User found: ID {$user->id}, Name: {$user->name}, Email: {$user->email}, Role: {$user->role}\n";
    
    // Update role to admin
    echo "Updating role to admin...\n";
    $user->update(['role' => 'admin']);
    $user->refresh();
    echo "Updated role: {$user->role}\n";
    
    // Check database directly
    $userFromDB = User::find(9);
    echo "Database check - Role: {$userFromDB->role}\n";
    
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
        if ($userData['id'] == 9) {
            echo "User ID 9 in API response: Role = {$userData['role']}\n";
        }
    }
    
} else {
    echo "User with ID 9 not found\n";
}
