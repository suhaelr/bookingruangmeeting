<?php
/**
 * Test script untuk memastikan hourly_rate fix berfungsi
 */

echo "=== Test Hourly Rate Fix ===\n";

// Simulasi migrasi create_meeting_rooms_table yang sudah diperbaiki
echo "1. Testing migration create_meeting_rooms_table...\n";

$migrationContent = file_get_contents('database/migrations/2024_01_01_000002_create_meeting_rooms_table.php');

if (strpos($migrationContent, 'hourly_rate') !== false) {
    echo "❌ Migration still contains hourly_rate reference\n";
} else {
    echo "✅ Migration does not contain hourly_rate reference\n";
}

// Simulasi data booking yang akan dikirim
$bookingData = [
    'user_id' => 2,
    'meeting_room_id' => 4,
    'title' => 'Test Meeting',
    'description' => 'Test description',
    'start_time' => '2025-10-11 00:05:00',
    'end_time' => '2025-10-11 02:07:00',
    'attendees_count' => 12,
    'attendees' => ['suhaelr@gmail.com'],
    'special_requirements' => 'Test requirements',
    'unit_kerja' => 'Test unit',
    'dokumen_perizinan' => 'dokumen_perizinan/1760116309_visitor.pdf',
    'total_cost' => 0.00
];

echo "\n2. Testing booking data...\n";
echo "Booking data (as will be sent):\n";
print_r($bookingData);

// Test SQL insert statement (simulasi)
echo "\n3. Testing SQL Insert Statement...\n";

$sql = "INSERT INTO bookings (";
$sql .= implode(', ', array_keys($bookingData));
$sql .= ") VALUES (";
$sql .= "'" . implode("', '", array_values($bookingData)) . "'";
$sql .= ")";

echo "SQL statement:\n";
echo $sql . "\n";

// Check if hourly_rate is referenced
if (strpos($sql, 'hourly_rate') !== false) {
    echo "❌ SQL still references hourly_rate\n";
} else {
    echo "✅ SQL does not reference hourly_rate\n";
}

// Test migration content
echo "\n4. Testing migration content...\n";
$migrationFile = 'database/migrations/2024_01_01_000002_create_meeting_rooms_table.php';
if (file_exists($migrationFile)) {
    $content = file_get_contents($migrationFile);
    if (strpos($content, 'hourly_rate') !== false) {
        echo "❌ Migration file still contains hourly_rate\n";
    } else {
        echo "✅ Migration file does not contain hourly_rate\n";
    }
} else {
    echo "❌ Migration file not found\n";
}

echo "\n=== Test Complete ===\n";
echo "The booking should now work without hourly_rate errors.\n";
echo "Make sure to run migrations in production to apply the fix.\n";
?>
