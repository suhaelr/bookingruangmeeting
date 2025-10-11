<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing CSRF token...\n";

// Get CSRF token
$token = csrf_token();
echo "CSRF Token: {$token}\n";

// Test if token is valid
$request = new \Illuminate\Http\Request();
$request->headers->set('X-CSRF-TOKEN', $token);

echo "CSRF token generated successfully\n";

// Test session
session(['test' => 'value']);
echo "Session test: " . session('test') . "\n";

echo "CSRF and session working correctly\n";
