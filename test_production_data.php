<?php
/**
 * Test dengan data production yang persis sama
 */

echo "=== Test Production Data ===\n";

// Data yang persis sama seperti yang dikirim dari production (dari gambar)
$productionData = [
    '_token' => 'KGUbJwt4ksUUSMKc5MMHihlcsPfU3PYGt5XIyiat',
    'name' => 'awsdasdada',
    'capacity' => '222', // String dari input number
    'description' => 'asxcasd23ewqasd',
    'location' => 'asdasd3wedsa',
    'is_active' => '0', // String "0" dari select
    'amenities' => 'Kursi, AC, bangku, ayam'
];

echo "Production data (from error screenshot):\n";
print_r($productionData);

// Test validasi manual dengan rules yang sama
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
if (empty($productionData['name']) || !is_string($productionData['name']) || strlen($productionData['name']) > 255) {
    $errors['name'] = 'Name is required, must be string, and max 255 characters';
    echo "❌ Name validation failed: '" . $productionData['name'] . "'\n";
} else {
    echo "✅ Name validation passed: '" . $productionData['name'] . "'\n";
}

// Validate description
if (isset($productionData['description']) && !is_string($productionData['description'])) {
    $errors['description'] = 'Description must be string';
    echo "❌ Description validation failed\n";
} else {
    echo "✅ Description validation passed\n";
}

// Validate capacity
if (!isset($productionData['capacity']) || !is_numeric($productionData['capacity']) || (float)$productionData['capacity'] < 1) {
    $errors['capacity'] = 'Capacity is required and must be numeric >= 1';
    echo "❌ Capacity validation failed: '" . $productionData['capacity'] . "'\n";
} else {
    echo "✅ Capacity validation passed: '" . $productionData['capacity'] . "'\n";
}

// Validate location
if (empty($productionData['location']) || !is_string($productionData['location']) || strlen($productionData['location']) > 255) {
    $errors['location'] = 'Location is required, must be string, and max 255 characters';
    echo "❌ Location validation failed: '" . $productionData['location'] . "'\n";
} else {
    echo "✅ Location validation passed: '" . $productionData['location'] . "'\n";
}

// Validate is_active
$validIsActiveValues = ['0', '1', 'true', 'false', 'on', 'off', ''];
if (isset($productionData['is_active']) && !in_array($productionData['is_active'], $validIsActiveValues)) {
    $errors['is_active'] = 'is_active must be one of: ' . implode(', ', $validIsActiveValues);
    echo "❌ is_active validation failed: '" . $productionData['is_active'] . "'\n";
} else {
    echo "✅ is_active validation passed: '" . $productionData['is_active'] . "'\n";
}

// Validate amenities
if (isset($productionData['amenities']) && !is_string($productionData['amenities'])) {
    $errors['amenities'] = 'Amenities must be string';
    echo "❌ Amenities validation failed\n";
} else {
    echo "✅ Amenities validation passed\n";
}

if (empty($errors)) {
    echo "\n✅ All validations passed!\n";
    
    // Test conversions
    $capacity = (int)$productionData['capacity'];
    echo "Capacity conversion: '{$productionData['capacity']}' -> $capacity\n";
    
    $isActiveValue = $productionData['is_active'];
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

echo "\n=== Production Data Test Complete ===\n";
?>
