<?php
/**
 * Test Laravel validation dengan data yang persis sama
 */

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

echo "=== Test Laravel Validation ===\n";

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

// Buat request object
$request = Request::create('/admin/rooms/7', 'PUT', $formData);

// Test validasi yang sama seperti di controller
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

try {
    $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
    
    if ($validator->fails()) {
        echo "\n❌ Validation failed!\n";
        echo "Errors:\n";
        print_r($validator->errors()->toArray());
    } else {
        echo "\n✅ Validation passed!\n";
        echo "Validated data:\n";
        print_r($validator->validated());
    }
} catch (Exception $e) {
    echo "\n❌ Exception occurred!\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Laravel Validation Test Complete ===\n";
?>
