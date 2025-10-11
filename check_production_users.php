<?php

require_once 'vendor/autoload.php';

use App\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking production database users...\n";

// Check all users
$users = User::all();
echo "Total users: " . $users->count() . "\n\n";

foreach ($users as $user) {
    echo "ID: {$user->id}, Name: {$user->name}, Email: {$user->email}, Role: {$user->role}\n";
}

// Check if there are any users with ID 9
$user9 = User::find(9);
if ($user9) {
    echo "\nUser ID 9 exists: {$user9->name} - {$user9->email} - Role: {$user9->role}\n";
    
    // Update role to admin
    echo "Updating user ID 9 role to admin...\n";
    $user9->update(['role' => 'admin']);
    $user9->refresh();
    echo "Updated role: {$user9->role}\n";
    
} else {
    echo "\nUser ID 9 does not exist in local database.\n";
    echo "This means the production database has different data than local.\n";
    echo "You need to check the production database directly.\n";
}

// Check for users with specific emails from the frontend
echo "\nChecking for specific emails from frontend...\n";
$frontendEmails = [
    'rizqullahsuhael@gmail.com',
    'syifawsl3007@gmail.com', 
    'nonameemail991@gmail.com',
    'suhaelr@gmail.com',
    'admin@pusdatinbgn.web.id'
];

foreach ($frontendEmails as $email) {
    $user = User::where('email', $email)->first();
    if ($user) {
        echo "Found: {$email} -> ID: {$user->id}, Role: {$user->role}\n";
    } else {
        echo "Not found: {$email}\n";
    }
}