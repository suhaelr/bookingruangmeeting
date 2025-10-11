<?php

require_once 'vendor/autoload.php';

use App\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking Suhael Rizqullah user...\n";

// Find user by email
$user = User::where('email', 'rizqullahsuhael@gmail.com')->first();
if ($user) {
    echo "User found: ID {$user->id}, Name: {$user->name}, Email: {$user->email}, Role: {$user->role}\n";
    
    // Update role to admin
    echo "Updating role to admin...\n";
    $user->update(['role' => 'admin']);
    $user->refresh();
    echo "Updated role: {$user->role}\n";
    
    // Test API endpoint
    echo "\nTesting API endpoint...\n";
    $controller = new \App\Http\Controllers\AuthController();
    
    // Set up admin session
    session(['user_logged_in' => true]);
    session(['user_data' => [
        'id' => 1,
        'role' => 'admin',
        'email' => 'admin@pusdatinbgn.web.id'
    ]]);
    
    $response = $controller->getAllUsers();
    echo "API Response: " . $response->getContent() . "\n";
    
} else {
    echo "User not found\n";
}
