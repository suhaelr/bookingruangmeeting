<?php
/**
 * Test array_flatten fix
 */

echo "=== Test Array Flatten Fix ===\n";

// Simulasi error validation
$errors = [
    'name' => ['Name is required'],
    'capacity' => ['Capacity must be numeric'],
    'is_active' => ['Invalid status value']
];

echo "Original errors:\n";
print_r($errors);

// Test manual flattening (seperti di controller)
$flattenedErrors = [];
foreach ($errors as $field => $messages) {
    $flattenedErrors = array_merge($flattenedErrors, $messages);
}

echo "\nFlattened errors:\n";
print_r($flattenedErrors);

echo "\nError message: " . implode(', ', $flattenedErrors) . "\n";

echo "\n=== Test Complete ===\n";
?>
