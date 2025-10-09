#!/bin/bash

# Script untuk memindahkan file Laravel ke root domain
# Menghilangkan /public dari URL

echo "🚀 Memindahkan file Laravel ke root domain..."

# Backup file yang ada
echo "📦 Membuat backup..."
cp -r /home/ypimfbgf/public_html /home/ypimfbgf/public_html_backup_$(date +%Y%m%d_%H%M%S)

# Pindahkan file dari public ke root
echo "📁 Memindahkan file dari public ke root..."
cd /home/ypimfbgf/public_html

# Pindahkan semua file dari public
mv public/* . 2>/dev/null || true
mv public/.* . 2>/dev/null || true

# Hapus folder public yang kosong
rmdir public 2>/dev/null || true

# Update file index.php
echo "🔧 Mengupdate file index.php..."
cat > index.php << 'EOF'
<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
EOF

# Update .env
echo "⚙️ Mengupdate file .env..."
sed -i 's|APP_URL=.*|APP_URL=https://jadixpert.com|g' .env

# Set permissions
echo "🔐 Mengatur permissions..."
chmod -R 755 /home/ypimfbgf/public_html
chown -R www-data:www-data /home/ypimfbgf/public_html

# Clear cache
echo "🧹 Membersihkan cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "✅ Selesai! File Laravel telah dipindahkan ke root domain."
echo "🌐 URL sekarang: https://jadixpert.com"
echo "📊 Dashboard: https://jadixpert.com/dashboard"
echo "🔑 Login: https://jadixpert.com/login"
