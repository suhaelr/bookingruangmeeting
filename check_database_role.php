<?php

require_once 'vendor/autoload.php';

use App\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking database role for user ID 7...\n";

// Check specific user
$user = User::find(7);
if ($user) {
    echo "User found: {$user->name} - Email: {$user->email} - Role: {$user->role}\n";
} else {
    echo "User with ID 7 not found\n";
}

// Check all users and their roles
echo "\nAll users and their roles:\n";
$users = User::all();
foreach ($users as $user) {
    echo "- ID: {$user->id}, Name: {$user->name}, Email: {$user->email}, Role: {$user->role}\n";
}

// Test API endpoint directly
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
echo "API Response: " . $response->getContent() . "\n";
