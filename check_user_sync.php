<?php

require_once 'vendor/autoload.php';

use App\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking user synchronization between database and admin panel...\n";

// Check all users in database
$users = User::all();
echo "Total users in database: " . $users->count() . "\n\n";

echo "Database users:\n";
foreach ($users as $user) {
    echo "ID: {$user->id}, Name: " . ($user->name ?: 'NULL') . ", Email: {$user->email}, Role: {$user->role}\n";
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

echo "\nAPI users:\n";
foreach ($responseData['users'] as $index => $userData) {
    echo "API User {$index}: ID={$userData['id']}, Name={$userData['name']}, Email={$userData['email']}, Role={$userData['role']}\n";
}

// Check for discrepancies
echo "\nChecking for discrepancies...\n";
$dbUserIds = $users->pluck('id')->toArray();
$apiUserIds = array_column($responseData['users'], 'id');

$missingInAPI = array_diff($dbUserIds, $apiUserIds);
$extraInAPI = array_diff($apiUserIds, $dbUserIds);

if (!empty($missingInAPI)) {
    echo "Users missing in API: " . implode(', ', $missingInAPI) . "\n";
}

if (!empty($extraInAPI)) {
    echo "Users extra in API: " . implode(', ', $extraInAPI) . "\n";
}

if (empty($missingInAPI) && empty($extraInAPI)) {
    echo "No discrepancies found. Database and API are in sync.\n";
}
