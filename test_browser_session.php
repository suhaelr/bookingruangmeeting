<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing browser session simulation...\n";

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

echo "Admin session set up\n";
echo "Session ID: " . Session::getId() . "\n";
echo "User logged in: " . (Session::get('user_logged_in') ? 'true' : 'false') . "\n";
echo "User data: " . json_encode(Session::get('user_data')) . "\n";

// Test middleware
$request = new \Illuminate\Http\Request();
$request->setLaravelSession(Session::getStore());

$middleware = new \App\Http\Middleware\AdminAuth();
$response = $middleware->handle($request, function($req) {
    return response()->json(['success' => true]);
});

echo "Middleware response: " . $response->getContent() . "\n";
