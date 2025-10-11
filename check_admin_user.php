<?php

require_once 'vendor/autoload.php';

use App\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking admin user...\n";

// Check admin user
$adminUser = User::where('username', 'admin')->first();
if ($adminUser) {
    echo "Admin user found: {$adminUser->name} - Role: {$adminUser->role}\n";
} else {
    echo "Admin user not found\n";
}

// Check all users
$users = User::all();
echo "\nAll users:\n";
foreach ($users as $user) {
    echo "- {$user->name} ({$user->email}) - Role: {$user->role}\n";
}
