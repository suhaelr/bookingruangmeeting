<?php
/**
 * Test dengan JSON form data
 */

echo "=== Test JSON Form Data ===\n";

// Simulasi data JSON yang akan dikirim
$jsonData = [
    'name' => 'awsdasdada',
    'capacity' => '222',
    'description' => 'asxcasd23ewqasd',
    'location' => 'asdasd3wedsa',
    'is_active' => '0',
    'amenities' => 'Kursi, AC, bangku, ayam'
];

echo "JSON data (as will be sent):\n";
print_r($jsonData);

// Test validasi manual
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
if (empty($jsonData['name']) || !is_string($jsonData['name']) || strlen($jsonData['name']) > 255) {
    $errors['name'] = 'Name is required, must be string, and max 255 characters';
    echo "❌ Name validation failed: '" . $jsonData['name'] . "'\n";
} else {
    echo "✅ Name validation passed: '" . $jsonData['name'] . "'\n";
}

// Validate description
if (isset($jsonData['description']) && !is_string($jsonData['description'])) {
    $errors['description'] = 'Description must be string';
    echo "❌ Description validation failed\n";
} else {
    echo "✅ Description validation passed\n";
}

// Validate capacity
if (!isset($jsonData['capacity']) || !is_numeric($jsonData['capacity']) || (float)$jsonData['capacity'] < 1) {
    $errors['capacity'] = 'Capacity is required and must be numeric >= 1';
    echo "❌ Capacity validation failed: '" . $jsonData['capacity'] . "'\n";
} else {
    echo "✅ Capacity validation passed: '" . $jsonData['capacity'] . "'\n";
}

// Validate location
if (empty($jsonData['location']) || !is_string($jsonData['location']) || strlen($jsonData['location']) > 255) {
    $errors['location'] = 'Location is required, must be string, and max 255 characters';
    echo "❌ Location validation failed: '" . $jsonData['location'] . "'\n";
} else {
    echo "✅ Location validation passed: '" . $jsonData['location'] . "'\n";
}

// Validate is_active
$validIsActiveValues = ['0', '1', 'true', 'false', 'on', 'off', ''];
if (isset($jsonData['is_active']) && !in_array($jsonData['is_active'], $validIsActiveValues)) {
    $errors['is_active'] = 'is_active must be one of: ' . implode(', ', $validIsActiveValues);
    echo "❌ is_active validation failed: '" . $jsonData['is_active'] . "'\n";
} else {
    echo "✅ is_active validation passed: '" . $jsonData['is_active'] . "'\n";
}

// Validate amenities
if (isset($jsonData['amenities']) && !is_string($jsonData['amenities'])) {
    $errors['amenities'] = 'Amenities must be string';
    echo "❌ Amenities validation failed\n";
} else {
    echo "✅ Amenities validation passed\n";
}

if (empty($errors)) {
    echo "\n✅ All validations passed!\n";
    
    // Test conversions
    $capacity = (int)$jsonData['capacity'];
    echo "Capacity conversion: '{$jsonData['capacity']}' -> $capacity\n";
    
    $isActiveValue = $jsonData['is_active'];
    $isActive = false;
    if (in_array($isActiveValue, ['1', 'true', 'on', 1, true])) {
        $isActive = true;
    } elseif (in_array($isActiveValue, ['0', 'false', 'off', 0, false])) {
        $isActive = false;
    }
    echo "is_active conversion: '$isActiveValue' -> " . ($isActive ? 'true' : 'false') . "\n";
    
    // Test amenities processing
    $amenities = $jsonData['amenities'] ? 
        array_map('trim', explode(',', $jsonData['amenities'])) : [];
    $amenities = array_values(array_filter($amenities, fn($item) => $item !== ''));
    echo "Amenities processing: '{$jsonData['amenities']}' -> " . json_encode($amenities) . "\n";
    
} else {
    echo "\n❌ Validation failed!\n";
    echo "Errors:\n";
    print_r($errors);
}

echo "\n=== JSON Form Data Test Complete ===\n";
?>
