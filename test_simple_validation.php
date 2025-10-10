<?php
/**
 * Simple validation test untuk data form
 */

echo "=== Simple Validation Test ===\n";

// Data yang persis sama seperti yang dikirim dari form
$formData = [
    '_token' => 'KGUbJwt4ksUU5MKc5MMHihlcsPfU3PYGt5XIyiat',
    'name' => 'awsdasd',
    'capacity' => '22', // String dari input number
    'description' => 'asxcasd',
    'location' => 'asdasd',
    'is_active' => '0', // String "0" dari select
    'amenities' => 'Kursi, AC, bangku'
];

echo "Form data:\n";
print_r($formData);

// Manual validation dengan rules yang sama
$rules = [
    'name' => 'required|string|max:255',
    'description' => 'nullable|string',
    'capacity' => 'required|numeric|min:1',
    'location' => 'required|string|max:255',
    'is_active' => 'nullable|string|in:0,1,true,false,on,off,',
    'amenities' => 'nullable|string'
];

echo "\nValidation rules:\n";
foreach ($rules as $field => $rule) {
    echo "$field: $rule\n";
}

$errors = [];

// Validate name
if (empty($formData['name']) || !is_string($formData['name']) || strlen($formData['name']) > 255) {
    $errors['name'] = 'Name is required, must be string, and max 255 characters';
}

// Validate description
if (isset($formData['description']) && !is_string($formData['description'])) {
    $errors['description'] = 'Description must be string';
}

// Validate capacity
if (!isset($formData['capacity']) || !is_numeric($formData['capacity']) || (float)$formData['capacity'] < 1) {
    $errors['capacity'] = 'Capacity is required and must be numeric >= 1';
}

// Validate location
if (empty($formData['location']) || !is_string($formData['location']) || strlen($formData['location']) > 255) {
    $errors['location'] = 'Location is required, must be string, and max 255 characters';
}

// Validate is_active
$validIsActiveValues = ['0', '1', 'true', 'false', 'on', 'off', ''];
if (isset($formData['is_active']) && !in_array($formData['is_active'], $validIsActiveValues)) {
    $errors['is_active'] = 'is_active must be one of: ' . implode(', ', $validIsActiveValues);
}

// Validate amenities
if (isset($formData['amenities']) && !is_string($formData['amenities'])) {
    $errors['amenities'] = 'Amenities must be string';
}

if (empty($errors)) {
    echo "\n✅ All validations passed!\n";
    
    // Test conversions
    $capacity = (int)$formData['capacity'];
    echo "Capacity conversion: '{$formData['capacity']}' -> $capacity\n";
    
    $isActiveValue = $formData['is_active'];
    $isActive = false;
    if (in_array($isActiveValue, ['1', 'true', 'on', 1, true])) {
        $isActive = true;
    } elseif (in_array($isActiveValue, ['0', 'false', 'off', 0, false])) {
        $isActive = false;
    }
    echo "is_active conversion: '$isActiveValue' -> " . ($isActive ? 'true' : 'false') . "\n";
    
    // Test amenities processing
    $amenities = $formData['amenities'] ? 
        array_map('trim', explode(',', $formData['amenities'])) : [];
    $amenities = array_values(array_filter($amenities, fn($item) => $item !== ''));
    echo "Amenities processing: '{$formData['amenities']}' -> " . json_encode($amenities) . "\n";
    
} else {
    echo "\n❌ Validation failed!\n";
    echo "Errors:\n";
    print_r($errors);
}

echo "\n=== Simple Validation Test Complete ===\n";
?>
