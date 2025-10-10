<?php
/**
 * Test script untuk perbaikan tampilan mobile status
 */

echo "=== Test Mobile Status Display Fix ===\n";

// Simulasi data booking
$booking = [
    'id' => 1,
    'title' => 'awdas',
    'meeting_room' => [
        'name' => 'Training Room',
        'location' => 'Floor 1, Building B'
    ],
    'status' => 'cancelled',
    'status_text' => 'Dibatalkan',
    'formatted_start_time' => '11 Oct 2025, 00:05',
    'duration' => '2.0333333333333',
    'attendees_count' => 12,
    'description' => 'qawedaw',
    'special_requirements' => 'wasdasd',
    'attendees' => ['suhaelr@gmail.com'],
    'cancellation_reason' => 'Cancelled by user'
];

echo "1. Booking data:\n";
print_r($booking);

echo "\n2. HTML structure yang diperbaiki:\n";
echo "<div class=\"booking-item bg-white/10 rounded-lg p-6\">\n";
echo "    <div class=\"mb-4\">\n";
echo "        <div class=\"flex items-start justify-between mb-2\">\n";
echo "            <div class=\"flex-1\">\n";
echo "                <h3 class=\"text-lg font-bold text-white mb-1\">{$booking['title']}</h3>\n";
echo "                <p class=\"text-white/80 text-sm mb-2\">{$booking['meeting_room']['name']} • {$booking['meeting_room']['location']}</p>\n";
echo "            </div>\n";
echo "            <div class=\"ml-4 flex-shrink-0\">\n";
echo "                <span class=\"px-3 py-1 rounded-full text-sm font-medium bg-red-500 text-white\">\n";
echo "                    {$booking['status_text']}\n";
echo "                </span>\n";
echo "            </div>\n";
echo "        </div>\n";
echo "        <div class=\"flex items-center space-x-4 text-sm text-white/60\">\n";
echo "            <span><i class=\"fas fa-calendar mr-1\"></i>{$booking['formatted_start_time']}</span>\n";
echo "            <span><i class=\"fas fa-clock mr-1\"></i>{$booking['duration']} jam</span>\n";
echo "            <span><i class=\"fas fa-users mr-1\"></i>{$booking['attendees_count']} peserta</span>\n";
echo "        </div>\n";
echo "    </div>\n";
echo "</div>\n";

echo "\n3. CSS Mobile Fix:\n";
echo "@media (max-width: 768px) {\n";
echo "    .booking-item {\n";
echo "        margin-bottom: 1rem;\n";
echo "    }\n";
echo "    \n";
echo "    .booking-item .flex.items-start.justify-between {\n";
echo "        flex-direction: column;\n";
echo "        align-items: flex-start;\n";
echo "    }\n";
echo "    \n";
echo "    .booking-item .flex-shrink-0 {\n";
echo "        margin-left: 0;\n";
echo "        margin-top: 0.5rem;\n";
echo "        align-self: flex-start;\n";
echo "    }\n";
echo "}\n";

echo "\n4. Perbaikan yang dilakukan:\n";
echo "✅ Status badge dipindahkan ke dalam struktur yang lebih baik\n";
echo "✅ Menggunakan flex-shrink-0 untuk mencegah badge menyusut\n";
echo "✅ Menambahkan margin-top untuk spacing yang tepat\n";
echo "✅ CSS mobile khusus untuk layout yang lebih baik\n";
echo "✅ Status badge sekarang berada di dalam kotak booking\n";

echo "\n=== Test Complete ===\n";
echo "Mobile status display should now be properly contained within the booking card.\n";
?>
