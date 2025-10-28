<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing notifications system...\n";

// Test user
$user = \App\Models\User::first();
if (!$user) {
    echo "No users found in database\n";
    exit;
}

echo "User found: {$user->id} ({$user->full_name})\n";

// Test notifications
$notifications = $user->notifications()->get();
echo "Total notifications: " . $notifications->count() . "\n";

$unread = $user->notifications()->where('is_read', false)->count();
echo "Unread notifications: " . $unread . "\n";

// Test mark all as read
echo "Testing mark all as read...\n";
$updatedCount = $user->notifications()
    ->where('is_read', false)
    ->update([
        'is_read' => true,
        'read_at' => now()
    ]);

echo "Updated count: " . $updatedCount . "\n";

// Check again
$unreadAfter = $user->notifications()->where('is_read', false)->count();
echo "Unread after update: " . $unreadAfter . "\n";

echo "Test completed.\n";
