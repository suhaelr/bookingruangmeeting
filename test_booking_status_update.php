<?php
/**
 * Test script untuk booking status update
 */

echo "=== Test Booking Status Update ===\n";

// Simulasi data request yang dikirim
$requestData = [
    'status' => 'confirmed',
    'reason' => 'asdsdasw'
];

echo "1. Request data yang dikirim:\n";
print_r($requestData);

// Test validasi
echo "\n2. Testing validation rules...\n";

$rules = [
    'status' => 'required|in:pending,confirmed,cancelled,completed',
    'reason' => 'nullable|string|max:255'
];

$errors = [];

// Validate status
if (empty($requestData['status'])) {
    $errors['status'] = 'Status is required';
    echo "❌ Status validation failed: empty\n";
} elseif (!in_array($requestData['status'], ['pending', 'confirmed', 'cancelled', 'completed'])) {
    $errors['status'] = 'Status must be one of: pending, confirmed, cancelled, completed';
    echo "❌ Status validation failed: invalid value '{$requestData['status']}'\n";
} else {
    echo "✅ Status validation passed: '{$requestData['status']}'\n";
}

// Validate reason
if (isset($requestData['reason']) && !is_string($requestData['reason'])) {
    $errors['reason'] = 'Reason must be string';
    echo "❌ Reason validation failed: not string\n";
} elseif (isset($requestData['reason']) && strlen($requestData['reason']) > 255) {
    $errors['reason'] = 'Reason must be less than 255 characters';
    echo "❌ Reason validation failed: too long\n";
} else {
    echo "✅ Reason validation passed\n";
}

if (empty($errors)) {
    echo "\n✅ All validations passed!\n";
    
    // Test updateStatus method simulation
    echo "\n3. Testing updateStatus method...\n";
    
    $bookingData = [
        'id' => 1,
        'status' => 'pending',
        'cancelled_at' => null,
        'cancellation_reason' => null
    ];
    
    echo "Booking before update:\n";
    print_r($bookingData);
    
    // Simulate updateStatus
    $newStatus = $requestData['status'];
    $reason = $requestData['reason'] ?? null;
    
    $bookingData['status'] = $newStatus;
    
    if ($newStatus === 'cancelled') {
        $bookingData['cancelled_at'] = date('Y-m-d H:i:s');
        $bookingData['cancellation_reason'] = $reason;
    }
    
    echo "\nBooking after update:\n";
    print_r($bookingData);
    
    echo "✅ updateStatus method simulation successful\n";
    
} else {
    echo "\n❌ Validation failed!\n";
    echo "Errors:\n";
    print_r($errors);
}

echo "\n=== Test Complete ===\n";
echo "If all tests pass, the issue might be in the frontend or routing.\n";
?>
