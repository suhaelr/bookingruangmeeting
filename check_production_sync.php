<?php

require_once 'vendor/autoload.php';

use App\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking production database sync...\n";

// Check all users with detailed info
$users = User::all();
echo "Total users: " . $users->count() . "\n\n";

foreach ($users as $user) {
    echo "ID: {$user->id}\n";
    echo "Name: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "Role: {$user->role}\n";
    echo "Google ID: " . ($user->google_id ?: 'None') . "\n";
    echo "Created: {$user->created_at}\n";
    echo "Updated: {$user->updated_at}\n";
    echo "Last Login: " . ($user->last_login_at ?: 'Never') . "\n";
    echo "---\n";
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
