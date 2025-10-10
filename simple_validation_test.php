<?php
/**
 * Simple validation test untuk room update
 */

echo "=== Simple Validation Test ===\n";

// Test data yang dikirim dari form
$testData = [
    'name' => 'Test Room',
    'description' => 'Test Description',
    'capacity' => 10,
    'location' => 'Test Location',
    'is_active' => '0', // String "0" dari form
    'amenities' => 'wifi,ac,projector'
];

echo "Test data:\n";
print_r($testData);

// Manual validation
$errors = [];

// Validate name
if (empty($testData['name']) || !is_string($testData['name']) || strlen($testData['name']) > 255) {
    $errors['name'] = 'Name is required, must be string, and max 255 characters';
}

// Validate capacity
if (!isset($testData['capacity']) || !is_numeric($testData['capacity']) || $testData['capacity'] < 1) {
    $errors['capacity'] = 'Capacity is required and must be integer >= 1';
}

// Validate location
if (empty($testData['location']) || !is_string($testData['location']) || strlen($testData['location']) > 255) {
    $errors['location'] = 'Location is required, must be string, and max 255 characters';
}

// Validate is_active
$validIsActiveValues = ['0', '1', 'true', 'false', 'on', 'off'];
if (isset($testData['is_active']) && !in_array($testData['is_active'], $validIsActiveValues)) {
    $errors['is_active'] = 'is_active must be one of: ' . implode(', ', $validIsActiveValues);
}

if (empty($errors)) {
    echo "\n✅ All validations passed!\n";
    
    // Test conversion
    $isActiveValue = $testData['is_active'];
    $isActive = false;
    if (in_array($isActiveValue, ['1', 'true', 'on', 1, true])) {
        $isActive = true;
    } elseif (in_array($isActiveValue, ['0', 'false', 'off', 0, false])) {
        $isActive = false;
    }
    
    echo "is_active conversion: '$isActiveValue' -> " . ($isActive ? 'true' : 'false') . "\n";
} else {
    echo "\n❌ Validation failed!\n";
    echo "Errors:\n";
    print_r($errors);
}

echo "\n=== Test Complete ===\n";
?>
