<?php

require_once 'vendor/autoload.php';

use App\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking production database synchronization...\n";

// Check all users in database
$users = User::all();
echo "Total users in database: " . $users->count() . "\n\n";

echo "Database users:\n";
foreach ($users as $user) {
    echo "ID: {$user->id}, Name: " . ($user->name ?: 'NULL') . ", Email: {$user->email}, Role: {$user->role}\n";
}

// Check for specific users that should exist based on frontend
echo "\nChecking for specific users from frontend...\n";
$frontendUsers = [
    'mohammadluthfi5@gmail.com',
    'rizqullahsuhael@gmail.com',
    'syifawsl3007@gmail.com', 
    'nonameemail991@gmail.com',
    'suhaelr@gmail.com',
    'admin@pusdatinbgn.web.id'
];

foreach ($frontendUsers as $email) {
    $user = User::where('email', $email)->first();
    if ($user) {
        echo "Found: {$email} -> ID: {$user->id}, Name: " . ($user->name ?: 'NULL') . ", Role: {$user->role}\n";
    } else {
        echo "Not found: {$email}\n";
    }
}

// Check if there are users with ID 6-10
echo "\nChecking for users with ID 6-10...\n";
for ($i = 6; $i <= 10; $i++) {
    $user = User::find($i);
    if ($user) {
        echo "User ID {$i}: {$user->name} - {$user->email} - Role: {$user->role}\n";
    } else {
        echo "User ID {$i}: Not found\n";
    }
}
