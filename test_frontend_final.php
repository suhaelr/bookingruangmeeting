<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing frontend final...\n";

// Check if admin user exists
$adminUser = User::where('username', 'admin')->first();
if ($adminUser) {
    echo "Admin user found: {$adminUser->name} - Role: {$adminUser->role}\n";
} else {
    echo "Admin user not found\n";
}

// Test login process
echo "\nTesting login process...\n";

// Simulate admin login
Session::put('user_logged_in', true);
Session::put('user_data', [
    'id' => $adminUser->id,
    'username' => $adminUser->username,
    'full_name' => $adminUser->full_name ?? $adminUser->name,
    'email' => $adminUser->email,
    'role' => $adminUser->role,
    'department' => $adminUser->department ?? 'IT'
]);
Session::save();

echo "Admin session set up\n";
echo "Session ID: " . Session::getId() . "\n";
echo "User logged in: " . (Session::get('user_logged_in') ? 'true' : 'false') . "\n";
echo "User data: " . json_encode(Session::get('user_data')) . "\n";

// Test API endpoints
$controller = new \App\Http\Controllers\AuthController();

echo "\nTesting getAllUsers API...\n";
$response = $controller->getAllUsers();
echo "getAllUsers Response: " . $response->getContent() . "\n";

echo "\nTesting updateUserRole API...\n";
$user = User::where('email', 'rizqullahsuhael@gmail.com')->first();
if ($user) {
    $request = new \Illuminate\Http\Request();
    $request->merge(['role' => 'admin']);
    
    $response = $controller->updateUserRole($request, $user->id);
    echo "updateUserRole Response: " . $response->getContent() . "\n";
    
    $user->refresh();
    echo "User role after update: {$user->role}\n";
}
