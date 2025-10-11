<?php
// Debug script untuk cek booking reminder
// Jalankan: php debug_booking_reminder.php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Booking;
use Carbon\Carbon;

echo "🔍 DEBUG BOOKING REMINDER SYSTEM\n";
echo "================================\n\n";

// 1. Cek semua booking yang confirmed
echo "📋 1. SEMUA BOOKING CONFIRMED:\n";
$confirmedBookings = Booking::where('status', 'confirmed')
    ->with(['user', 'meetingRoom'])
    ->get();

foreach ($confirmedBookings as $booking) {
    echo "ID: {$booking->id} | Title: {$booking->title}\n";
    echo "Start: {$booking->start_time} | End: {$booking->end_time}\n";
    echo "User: {$booking->user->full_name} ({$booking->user->email})\n";
    echo "Room: {$booking->meetingRoom->name}\n";
    echo "---\n";
}

echo "\n";

// 2. Cek waktu sekarang
echo "⏰ 2. WAKTU SEKARANG:\n";
$now = now();
echo "Sekarang: {$now}\n";
echo "Timezone: {$now->timezone}\n\n";

// 3. Cek range waktu reminder (1 jam dari sekarang)
echo "🎯 3. RANGE WAKTU REMINDER (1 jam dari sekarang):\n";
$reminderTime = $now->addHour();
$startTime = $reminderTime->copy()->subMinutes(30);
$endTime = $reminderTime->copy()->addMinutes(30);

echo "Reminder Time: {$reminderTime}\n";
echo "Start Range: {$startTime}\n";
echo "End Range: {$endTime}\n\n";

// 4. Cek booking yang masuk range
echo "🔍 4. BOOKING YANG MASUK RANGE:\n";
$bookingsInRange = Booking::where('status', 'confirmed')
    ->whereBetween('start_time', [$startTime, $endTime])
    ->with(['user', 'meetingRoom'])
    ->get();

echo "Jumlah booking dalam range: " . $bookingsInRange->count() . "\n\n";

foreach ($bookingsInRange as $booking) {
    echo "✅ MATCH! ID: {$booking->id} | Title: {$booking->title}\n";
    echo "Start: {$booking->start_time}\n";
    echo "User: {$booking->user->full_name} ({$booking->user->email})\n";
    echo "---\n";
}

// 5. Cek booking yang akan mulai dalam 1 jam (lebih detail)
echo "📊 5. ANALISIS DETAIL:\n";
foreach ($confirmedBookings as $booking) {
    $timeDiff = $booking->start_time->diffInMinutes($now);
    echo "Booking ID {$booking->id}:\n";
    echo "  Start Time: {$booking->start_time}\n";
    echo "  Time Diff: {$timeDiff} menit dari sekarang\n";
    echo "  Status: " . ($timeDiff <= 90 && $timeDiff >= 30 ? "✅ AKAN DAPAT REMINDER" : "❌ BELUM WAKTUNYA") . "\n";
    echo "---\n";
}

echo "\n🎯 KESIMPULAN:\n";
echo "- Booking perlu mulai dalam 30-90 menit untuk dapat reminder\n";
echo "- Jika tidak ada yang masuk range, akan muncul 'Sent: 0'\n";
echo "- Ini normal jika booking masih jauh dari waktu mulai\n";
?>
