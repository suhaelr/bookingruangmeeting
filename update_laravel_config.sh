#!/bin/bash

# Script untuk mengupdate konfigurasi Laravel setelah memindahkan ke root

echo "🔧 Mengupdate konfigurasi Laravel..."

# Update .env
echo "📝 Mengupdate file .env..."
if [ -f .env ]; then
    # Backup .env
    cp .env .env.backup
    
    # Update APP_URL
    sed -i 's|APP_URL=.*|APP_URL=https://jadixpert.com|g' .env
    
    echo "✅ APP_URL telah diupdate ke https://jadixpert.com"
else
    echo "❌ File .env tidak ditemukan!"
fi

# Update file index.php jika diperlukan
echo "🔧 Mengupdate file index.php..."
if [ -f index.php ]; then
    # Backup index.php
    cp index.php index.php.backup
    
    # Update path di index.php
    sed -i 's|__DIR__.*/vendor/autoload.php|__DIR__."/vendor/autoload.php"|g' index.php
    sed -i 's|__DIR__.*/bootstrap/app.php|__DIR__."/bootstrap/app.php"|g' index.php
    
    echo "✅ File index.php telah diupdate"
else
    echo "❌ File index.php tidak ditemukan!"
fi

# Clear semua cache
echo "🧹 Membersihkan cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache

# Set permissions
echo "🔐 Mengatur permissions..."
chmod -R 755 .
chmod -R 777 storage bootstrap/cache

# Test koneksi
echo "🧪 Testing koneksi..."
php artisan tinker --execute="
try {
    echo 'Testing database connection...' . PHP_EOL;
    DB::connection()->getPdo();
    echo 'Database connection: OK' . PHP_EOL;
} catch (Exception \$e) {
    echo 'Database connection failed: ' . \$e->getMessage() . PHP_EOL;
}

echo 'Testing routes...' . PHP_EOL;
echo 'Login route: ' . route('login') . PHP_EOL;
echo 'Dashboard route: ' . route('dashboard') . PHP_EOL;
"

echo "✅ Konfigurasi selesai!"
echo "🌐 URL aplikasi: https://jadixpert.com"
echo "🔑 Login: https://jadixpert.com/login"
echo "📊 Dashboard: https://jadixpert.com/dashboard"
