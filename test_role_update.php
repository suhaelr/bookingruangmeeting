<?php

require_once 'vendor/autoload.php';

use App\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing role update...\n";

// Find user
$user = User::where('email', 'rizqullahsuhael@gmail.com')->first();

if ($user) {
    echo "User found: {$user->name} - Current role: {$user->role}\n";
    
    // Update role
    $user->update(['role' => 'admin']);
    $user->refresh();
    
    echo "Updated role: {$user->role}\n";
    
    // Test API endpoint
    $request = new \Illuminate\Http\Request();
    $request->merge(['role' => 'admin']);
    
    // Mock session
    session(['user_data' => [
        'id' => 1,
        'role' => 'admin',
        'email' => 'admin@pusdatinbgn.web.id'
    ]]);
    
    $controller = new \App\Http\Controllers\AuthController();
    $response = $controller->updateUserRole($request, $user->id);
    
    echo "API Response: " . $response->getContent() . "\n";
    
} else {
    echo "User not found\n";
}
