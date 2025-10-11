<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing session debug...\n";

// Check current session
echo "Current session ID: " . Session::getId() . "\n";
echo "User logged in: " . (Session::get('user_logged_in') ? 'true' : 'false') . "\n";
echo "User data: " . json_encode(Session::get('user_data')) . "\n";

// Simulate admin login
Session::put('user_logged_in', true);
Session::put('user_data', [
    'id' => 1,
    'username' => 'admin',
    'full_name' => 'Super Administrator',
    'email' => 'admin@jadixpert.com',
    'role' => 'admin',
    'department' => 'IT'
]);
Session::save();

echo "\nAfter setting admin session:\n";
echo "User logged in: " . (Session::get('user_logged_in') ? 'true' : 'false') . "\n";
echo "User data: " . json_encode(Session::get('user_data')) . "\n";

// Test API with session
$controller = new \App\Http\Controllers\AuthController();
$response = $controller->getAllUsers();

echo "\nAPI Response: " . $response->getContent() . "\n";
