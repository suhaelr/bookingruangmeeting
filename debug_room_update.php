<?php
/**
 * Debug script untuk masalah update room
 * Jalankan script ini untuk test validasi dan konversi data
 */

require_once 'vendor/autoload.php';

// Simulasi data yang dikirim dari form
$testData = [
    'name' => 'Test Room',
    'description' => 'Test Description',
    'capacity' => 10,
    'location' => 'Test Location',
    'is_active' => '0', // String "0" dari form
    'amenities' => 'wifi,ac,projector'
];

echo "=== Debug Room Update ===\n";
echo "Test data:\n";
print_r($testData);

// Test validasi
$rules = [
    'name' => 'required|string|max:255',
    'description' => 'nullable|string',
    'capacity' => 'required|integer|min:1',
    'location' => 'required|string|max:255',
    'is_active' => 'nullable|string|in:0,1,true,false,on,off',
    'amenities' => 'nullable|string'
];

echo "\nValidation rules:\n";
foreach ($rules as $field => $rule) {
    echo "$field: $rule\n";
}

// Test konversi is_active
$isActiveValue = $testData['is_active'];
echo "\nTesting is_active conversion:\n";
echo "Original value: '$isActiveValue' (type: " . gettype($isActiveValue) . ")\n";

$isActive = false;
if (in_array($isActiveValue, ['1', 'true', 'on', 1, true])) {
    $isActive = true;
    echo "Converted to: true\n";
} elseif (in_array($isActiveValue, ['0', 'false', 'off', 0, false])) {
    $isActive = false;
    echo "Converted to: false\n";
} else {
    echo "No conversion applied, keeping default: false\n";
}

echo "\nFinal is_active value: " . ($isActive ? 'true' : 'false') . "\n";

// Test amenities processing
$amenities = $testData['amenities'] ? 
    array_map('trim', explode(',', $testData['amenities'])) : [];
$amenities = array_values(array_filter($amenities, fn($item) => $item !== ''));

echo "\nAmenities processing:\n";
echo "Original: '{$testData['amenities']}'\n";
echo "Processed: " . json_encode($amenities) . "\n";

echo "\n=== Debug Complete ===\n";
?>
