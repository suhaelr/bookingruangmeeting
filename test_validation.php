<?php
/**
 * Test validasi Laravel untuk room update
 */

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

// Simulasi request data
$requestData = [
    'name' => 'Test Room',
    'description' => 'Test Description', 
    'capacity' => 10,
    'location' => 'Test Location',
    'is_active' => '0', // String "0" dari form
    'amenities' => 'wifi,ac,projector'
];

echo "=== Test Laravel Validation ===\n";
echo "Request data:\n";
print_r($requestData);

// Buat request object
$request = Request::create('/test', 'PUT', $requestData);

// Test validasi
$rules = [
    'name' => 'required|string|max:255',
    'description' => 'nullable|string',
    'capacity' => 'required|integer|min:1',
    'location' => 'required|string|max:255',
    'is_active' => 'nullable|string|in:0,1,true,false,on,off',
    'amenities' => 'nullable|string'
];

try {
    $validated = $request->validate($rules);
    echo "\n✅ Validation passed!\n";
    echo "Validated data:\n";
    print_r($validated);
} catch (ValidationException $e) {
    echo "\n❌ Validation failed!\n";
    echo "Errors:\n";
    print_r($e->errors());
}

echo "\n=== Test Complete ===\n";
?>
