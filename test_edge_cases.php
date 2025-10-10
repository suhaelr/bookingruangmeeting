<?php
/**
 * Test edge cases untuk room update
 */

echo "=== Test Edge Cases ===\n";

$testCases = [
    [
        'name' => 'Edge Case 1',
        'description' => 'Test with empty is_active',
        'capacity' => 5,
        'location' => 'Test Location',
        'is_active' => '', // Empty string
        'amenities' => 'wifi'
    ],
    [
        'name' => 'Edge Case 2', 
        'description' => 'Test with null is_active',
        'capacity' => 5,
        'location' => 'Test Location',
        'is_active' => null, // Null value
        'amenities' => 'wifi'
    ],
    [
        'name' => 'Edge Case 3',
        'description' => 'Test with missing is_active',
        'capacity' => 5,
        'location' => 'Test Location',
        // is_active not set
        'amenities' => 'wifi'
    ],
    [
        'name' => 'Edge Case 4',
        'description' => 'Test with invalid is_active',
        'capacity' => 5,
        'location' => 'Test Location',
        'is_active' => 'invalid', // Invalid value
        'amenities' => 'wifi'
    ]
];

foreach ($testCases as $index => $testData) {
    echo "\n--- Test Case " . ($index + 1) . " ---\n";
    echo "Data: " . json_encode($testData) . "\n";
    
    // Validate is_active
    $validIsActiveValues = ['0', '1', 'true', 'false', 'on', 'off'];
    $isActiveValid = true;
    
    if (isset($testData['is_active'])) {
        if ($testData['is_active'] !== '' && !in_array($testData['is_active'], $validIsActiveValues)) {
            $isActiveValid = false;
            echo "❌ is_active validation failed: '{$testData['is_active']}' not in valid values\n";
        } else {
            echo "✅ is_active validation passed\n";
        }
    } else {
        echo "ℹ️ is_active not set (nullable field)\n";
    }
    
    // Test conversion
    if (isset($testData['is_active']) && $testData['is_active'] !== '') {
        $isActiveValue = $testData['is_active'];
        $isActive = false;
        if (in_array($isActiveValue, ['1', 'true', 'on', 1, true])) {
            $isActive = true;
        } elseif (in_array($isActiveValue, ['0', 'false', 'off', 0, false])) {
            $isActive = false;
        }
        echo "Conversion: '$isActiveValue' -> " . ($isActive ? 'true' : 'false') . "\n";
    } else {
        echo "No conversion needed (empty or null)\n";
    }
}

echo "\n=== Edge Cases Test Complete ===\n";
?>
