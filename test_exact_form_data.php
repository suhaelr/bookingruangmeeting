<?php
/**
 * Test dengan data form yang persis sama seperti yang dikirim
 */

echo "=== Test Exact Form Data ===\n";

// Data yang persis sama seperti yang dikirim dari form (dari gambar)
$exactFormData = [
    '_token' => 'KGUbJwt4ksUU5MKc5MMHihlcsPfU3PYGt5XIyiat',
    'name' => 'awsdasd',
    'capacity' => '22', // String dari input number
    'description' => 'asxcasd',
    'location' => 'asdasd',
    'is_active' => '0', // String "0" dari select
    'amenities' => 'Kursi, AC, bangku'
];

echo "Exact form data (from production error):\n";
print_r($exactFormData);

// Test validasi manual
$errors = [];

// Validate name
if (empty($exactFormData['name']) || !is_string($exactFormData['name']) || strlen($exactFormData['name']) > 255) {
    $errors['name'] = 'Name is required, must be string, and max 255 characters';
}

// Validate capacity (string to numeric)
if (!isset($exactFormData['capacity']) || !is_numeric($exactFormData['capacity']) || (float)$exactFormData['capacity'] < 1) {
    $errors['capacity'] = 'Capacity is required and must be numeric >= 1';
}

// Validate location
if (empty($exactFormData['location']) || !is_string($exactFormData['location']) || strlen($exactFormData['location']) > 255) {
    $errors['location'] = 'Location is required, must be string, and max 255 characters';
}

// Validate is_active
$validIsActiveValues = ['0', '1', 'true', 'false', 'on', 'off', ''];
if (isset($exactFormData['is_active']) && !in_array($exactFormData['is_active'], $validIsActiveValues)) {
    $errors['is_active'] = 'is_active must be one of: ' . implode(', ', $validIsActiveValues);
}

// Validate amenities
if (isset($exactFormData['amenities']) && !is_string($exactFormData['amenities'])) {
    $errors['amenities'] = 'Amenities must be string';
}

if (empty($errors)) {
    echo "\n✅ All validations passed!\n";
    
    // Test conversions
    $capacity = (int)$exactFormData['capacity'];
    echo "Capacity conversion: '{$exactFormData['capacity']}' -> $capacity\n";
    
    $isActiveValue = $exactFormData['is_active'];
    $isActive = false;
    if (in_array($isActiveValue, ['1', 'true', 'on', 1, true])) {
        $isActive = true;
    } elseif (in_array($isActiveValue, ['0', 'false', 'off', 0, false])) {
        $isActive = false;
    }
    echo "is_active conversion: '$isActiveValue' -> " . ($isActive ? 'true' : 'false') . "\n";
    
    // Test amenities processing
    $amenities = $exactFormData['amenities'] ? 
        array_map('trim', explode(',', $exactFormData['amenities'])) : [];
    $amenities = array_values(array_filter($amenities, fn($item) => $item !== ''));
    echo "Amenities processing: '{$exactFormData['amenities']}' -> " . json_encode($amenities) . "\n";
    
} else {
    echo "\n❌ Validation failed!\n";
    echo "Errors:\n";
    print_r($errors);
}

echo "\n=== Exact Form Data Test Complete ===\n";
?>
