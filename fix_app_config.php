<?php
/**
 * Script untuk memperbaiki konflik konfigurasi app.php
 * antara environment lokal dan server
 */

echo "Memperbaiki konfigurasi app.php...\n";

// Baca file app.php saat ini
$appConfigPath = 'config/app.php';
$appConfig = file_get_contents($appConfigPath);

// Deteksi environment berdasarkan APP_ENV
$isProduction = (getenv('APP_ENV') === 'production' || 
                 (isset($_SERVER['APP_ENV']) && $_SERVER['APP_ENV'] === 'production'));

$defaultUrl = $isProduction ? 'https://pusdatinbgn.web.id' : 'http://localhost';

echo "Environment: " . ($isProduction ? 'Production' : 'Local') . "\n";
echo "Default URL: $defaultUrl\n";

// Update konfigurasi URL
$updatedConfig = preg_replace(
    "/'url' => env\('APP_URL', '[^']*'\),/",
    "'url' => env('APP_URL', '$defaultUrl'),",
    $appConfig
);

// Simpan perubahan
file_put_contents($appConfigPath, $updatedConfig);

echo "Konfigurasi app.php telah diperbaiki!\n";
echo "URL default sekarang: $defaultUrl\n";
echo "Untuk mengubah URL, edit file .env dan set APP_URL\n";
echo "\nKonfigurasi production yang benar:\n";
echo "APP_URL=https://pusdatinbgn.web.id\n";
echo "APP_ENV=production\n";
echo "APP_NAME=\"Admin Panel\"\n";
?>
