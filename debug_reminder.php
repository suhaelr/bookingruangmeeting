<?php
// Debug script untuk cek email reminder
require_once 'vendor/autoload.php';

use App\Models\Booking;
use Carbon\Carbon;

echo "=== DEBUG EMAIL REMINDER ===\n";
echo "Waktu sekarang: " . now()->format('Y-m-d H:i:s') . "\n";
echo "Waktu + 1 jam: " . now()->addHour()->format('Y-m-d H:i:s') . "\n\n";

// Cek booking yang confirmed
$confirmedBookings = Booking::where('status', 'confirmed')->get();
echo "Total booking confirmed: " . $confirmedBookings->count() . "\n\n";

foreach ($confirmedBookings as $booking) {
    echo "Booking ID: {$booking->id}\n";
    echo "Title: {$booking->title}\n";
    echo "Start Time: {$booking->start_time}\n";
    echo "End Time: {$booking->end_time}\n";
    echo "User: {$booking->user->email}\n";
    
    // Cek apakah dalam range reminder
    $reminderTime = now()->addHour();
    $startTime = $reminderTime->copy()->subMinutes(30);
    $endTime = $reminderTime->copy()->addMinutes(30);
    
    echo "Reminder Time: {$reminderTime->format('Y-m-d H:i:s')}\n";
    echo "Start Range: {$startTime->format('Y-m-d H:i:s')}\n";
    echo "End Range: {$endTime->format('Y-m-d H:i:s')}\n";
    
    $isInRange = $booking->start_time->between($startTime, $endTime);
    echo "Dalam range reminder: " . ($isInRange ? 'YA' : 'TIDAK') . "\n";
    
    // Cek waktu sampai meeting
    $timeUntilMeeting = now()->diffInMinutes($booking->start_time);
    echo "Menit sampai meeting: {$timeUntilMeeting}\n";
    echo "Jam sampai meeting: " . round($timeUntilMeeting / 60, 2) . "\n";
    
    echo "---\n\n";
}

// Test query yang digunakan di command
$reminderTime = now()->addHour();
$startTime = $reminderTime->copy()->subMinutes(30);
$endTime = $reminderTime->copy()->addMinutes(30);

$bookingsToRemind = Booking::with(['user', 'meetingRoom'])
    ->where('status', 'confirmed')
    ->whereBetween('start_time', [$startTime, $endTime])
    ->get();

echo "Booking yang akan dikirim reminder: " . $bookingsToRemind->count() . "\n";
