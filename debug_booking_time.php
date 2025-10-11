<?php
// Simple debug untuk cek waktu booking
echo "=== DEBUG BOOKING TIME ===\n";
echo "Waktu sekarang: " . date('Y-m-d H:i:s') . "\n";
echo "Waktu + 1 jam: " . date('Y-m-d H:i:s', strtotime('+1 hour')) . "\n\n";

// Simulasi booking jam 17:10
$bookingTime = '2025-10-11 17:10:00';
echo "Booking time: {$bookingTime}\n";

$now = time();
$bookingTimestamp = strtotime($bookingTime);
$oneHourFromNow = strtotime('+1 hour');

echo "Timestamp sekarang: {$now}\n";
echo "Timestamp booking: {$bookingTimestamp}\n";
echo "Timestamp +1 jam: {$oneHourFromNow}\n\n";

// Cek apakah booking dalam range reminder (1 jam ± 30 menit)
$reminderStart = $oneHourFromNow - (30 * 60); // -30 menit
$reminderEnd = $oneHourFromNow + (30 * 60);   // +30 menit

echo "Reminder start: " . date('Y-m-d H:i:s', $reminderStart) . "\n";
echo "Reminder end: " . date('Y-m-d H:i:s', $reminderEnd) . "\n\n";

if ($bookingTimestamp >= $reminderStart && $bookingTimestamp <= $reminderEnd) {
    echo "✅ BOOKING DALAM RANGE REMINDER!\n";
} else {
    echo "❌ Booking TIDAK dalam range reminder\n";
}

// Cek waktu sampai meeting
$minutesUntilMeeting = ($bookingTimestamp - $now) / 60;
echo "Menit sampai meeting: " . round($minutesUntilMeeting) . "\n";
echo "Jam sampai meeting: " . round($minutesUntilMeeting / 60, 2) . "\n";
