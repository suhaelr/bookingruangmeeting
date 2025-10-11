<?php

require_once 'vendor/autoload.php';

use App\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking production database...\n";

// Check all users
$users = User::all();
echo "Total users: " . $users->count() . "\n\n";

foreach ($users as $user) {
    echo "ID: {$user->id}, Name: {$user->name}, Email: {$user->email}, Role: {$user->role}\n";
}

// Check if there are any users with ID 10
$user10 = User::find(10);
if ($user10) {
    echo "\nUser ID 10 exists: {$user10->name} - {$user10->email} - Role: {$user10->role}\n";
} else {
    echo "\nUser ID 10 does not exist in local database.\n";
}

// Check for users with specific emails from the frontend
echo "\nChecking for specific emails from frontend...\n";
$frontendEmails = [
    'mohammadluthfi5@gmail.com',
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