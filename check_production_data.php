<?php

require_once 'vendor/autoload.php';

use App\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking production data consistency...\n";

// Check all users
$users = User::all();
echo "Total users in database: " . $users->count() . "\n\n";

foreach ($users as $user) {
    echo "ID: {$user->id}, Name: {$user->name}, Email: {$user->email}, Role: {$user->role}\n";
}

// Check for duplicate emails
echo "\nChecking for duplicate emails...\n";
$emails = User::select('email')->get()->pluck('email')->toArray();
$duplicates = array_diff_assoc($emails, array_unique($emails));
if (!empty($duplicates)) {
    echo "Duplicate emails found: " . implode(', ', $duplicates) . "\n";
} else {
    echo "No duplicate emails found.\n";
}

// Check for Suhael Rizqullah specifically
echo "\nChecking for Suhael Rizqullah users...\n";
$suhaelUsers = User::where('name', 'LIKE', '%Suhael%')->orWhere('name', 'LIKE', '%Rizqullah%')->get();
foreach ($suhaelUsers as $user) {
    echo "Found: ID {$user->id}, Name: {$user->name}, Email: {$user->email}, Role: {$user->role}\n";
}
