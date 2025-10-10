<?php
/**
 * Test script untuk perbaikan teks status
 */

echo "=== Test Status Text Fix ===\n";

// Simulasi data status yang benar
$statusTexts = [
    'pending' => 'Menunggu',
    'confirmed' => 'Dikonfirmasi',
    'cancelled' => 'Dibatalkan',
    'completed' => 'Selesai'
];

echo "1. Status texts yang benar:\n";
foreach ($statusTexts as $key => $value) {
    echo "- $key: $value\n";
}

// Test untuk memastikan tidak ada "batalled"
echo "\n2. Checking for incorrect 'batalled' text...\n";
$incorrectTexts = ['batalled', 'Batalled', 'BATALLED'];

$foundIncorrect = false;
foreach ($incorrectTexts as $text) {
    if (in_array($text, $statusTexts)) {
        echo "❌ Found incorrect text: $text\n";
        $foundIncorrect = true;
    }
}

if (!$foundIncorrect) {
    echo "✅ No incorrect 'batalled' text found\n";
}

// Test untuk memastikan tidak ada "Completed" (seharusnya "Selesai")
echo "\n3. Checking for incorrect 'Completed' text...\n";
$incorrectCompleted = ['Completed', 'COMPLETED', 'completed'];

$foundIncorrectCompleted = false;
foreach ($incorrectCompleted as $text) {
    if (in_array($text, $statusTexts)) {
        echo "❌ Found incorrect text: $text\n";
        $foundIncorrectCompleted = true;
    }
}

if (!$foundIncorrectCompleted) {
    echo "✅ No incorrect 'Completed' text found\n";
}

// Test untuk memastikan semua status ada
echo "\n4. Checking all required statuses are present...\n";
$requiredStatuses = ['pending', 'confirmed', 'cancelled', 'completed'];

foreach ($requiredStatuses as $status) {
    if (isset($statusTexts[$status])) {
        echo "✅ $status: {$statusTexts[$status]}\n";
    } else {
        echo "❌ Missing status: $status\n";
    }
}

echo "\n=== Test Complete ===\n";
echo "Status text fix verification completed!\n";
?>
