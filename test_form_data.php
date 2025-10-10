<?php
/**
 * Test dengan data form yang realistis
 */

echo "=== Test Form Data ===\n";

// Simulasi data yang dikirim dari form HTML
$formData = [
    'name' => 'Meeting Room A',
    'description' => 'Large conference room with projector',
    'capacity' => '25', // String dari input number
    'location' => 'Floor 2, Building A',
    'is_active' => '0', // String "0" dari select option
    'amenities' => 'Projector, Whiteboard, WiFi, AC'
];

echo "Form data (as received from HTML form):\n";
print_r($formData);

// Simulasi validasi Laravel
$rules = [
    'name' => 'required|string|max:255',
    'description' => 'nullable|string',
    'capacity' => 'required|integer|min:1',
    'location' => 'required|string|max:255',
    'is_active' => 'nullable|string|in:0,1,true,false,on,off,',
    'amenities' => 'nullable|string'
];

echo "\nValidation rules:\n";
foreach ($rules as $field => $rule) {
    echo "$field: $rule\n";
}

// Manual validation
$errors = [];

// Validate name
if (empty($formData['name']) || !is_string($formData['name']) || strlen($formData['name']) > 255) {
    $errors['name'] = 'Name is required, must be string, and max 255 characters';
}

// Validate capacity (convert string to int)
if (!isset($formData['capacity']) || !is_numeric($formData['capacity']) || (int)$formData['capacity'] < 1) {
    $errors['capacity'] = 'Capacity is required and must be integer >= 1';
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

if (empty($errors)) {
    echo "\n✅ All validations passed!\n";
    
    // Test conversion
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
    
    // Test capacity conversion
    $capacity = (int)$formData['capacity'];
    echo "Capacity conversion: '{$formData['capacity']}' -> $capacity\n";
    
} else {
    echo "\n❌ Validation failed!\n";
    echo "Errors:\n";
    print_r($errors);
}

echo "\n=== Form Data Test Complete ===\n";
?>
