<?php

require_once 'vendor/autoload.php';

use App\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking for missing user in admin panel...\n";

// Check all users in database
$users = User::all();
echo "Total users in database: " . $users->count() . "\n\n";

foreach ($users as $user) {
    echo "ID: {$user->id}, Name: {$user->name}, Email: {$user->email}, Role: {$user->role}\n";
}

// Check if user ID 10 exists
$user10 = User::find(10);
if ($user10) {
    echo "\nUser ID 10 found: {$user10->name} - {$user10->email} - Role: {$user10->role}\n";
    echo "This user is missing from admin panel!\n";
} else {
    echo "\nUser ID 10 not found.\n";
}

// Test getAllUsers API
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
echo "Users count in API: " . count($responseData['users']) . "\n";

foreach ($responseData['users'] as $index => $userData) {
    echo "API User {$index}: ID={$userData['id']}, Name={$userData['name']}, Role={$userData['role']}\n";
}
