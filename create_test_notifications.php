<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Creating test notifications...\n";

// Get first user
$user = \App\Models\User::first();
if (!$user) {
    echo "No users found in database\n";
    exit;
}

echo "User found: {$user->id} ({$user->full_name})\n";

// Create test notifications
$notifications = [
    [
        'user_id' => $user->id,
        'type' => 'info',
        'title' => 'Test Notification 1',
        'message' => 'This is a test notification to verify the system works.',
        'is_read' => false,
    ],
    [
        'user_id' => $user->id,
        'type' => 'success',
        'title' => 'Test Notification 2',
        'message' => 'Another test notification for testing mark all as read.',
        'is_read' => false,
    ],
    [
        'user_id' => $user->id,
        'type' => 'warning',
        'title' => 'Test Notification 3',
        'message' => 'Third test notification to ensure proper counting.',
        'is_read' => false,
    ],
];

foreach ($notifications as $notificationData) {
    $notification = \App\Models\UserNotification::create($notificationData);
    echo "Created notification: {$notification->id} - {$notification->title}\n";
}

// Check count
$total = $user->notifications()->count();
$unread = $user->notifications()->where('is_read', false)->count();

echo "Total notifications: {$total}\n";
echo "Unread notifications: {$unread}\n";

echo "Test notifications created successfully!\n";
