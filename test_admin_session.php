<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing admin session and API...\n";

// Set up admin session
Session::put('user_logged_in', true);
Session::put('user_data', [
    'id' => 1,
    'role' => 'admin',
    'email' => 'admin@pusdatinbgn.web.id'
]);
Session::save();

echo "Admin session set up\n";

// Test getAllUsers API
$controller = new \App\Http\Controllers\AuthController();
$response = $controller->getAllUsers();

echo "getAllUsers Response: " . $response->getContent() . "\n";

// Test updateUserRole API
$user = User::where('email', 'rizqullahsuhael@gmail.com')->first();
if ($user) {
    echo "User found: {$user->name} - Current role: {$user->role}\n";
    
    $request = new \Illuminate\Http\Request();
    $request->merge(['role' => 'admin']);
    
    $response = $controller->updateUserRole($request, $user->id);
    echo "updateUserRole Response: " . $response->getContent() . "\n";
    
    $user->refresh();
    echo "User role after update: {$user->role}\n";
}
