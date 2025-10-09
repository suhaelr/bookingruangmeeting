#!/bin/bash

# Deploy script untuk Laravel ke /home/ypimfbgf/public_html
# Pastikan Anda sudah login ke server dengan SSH

echo "🚀 Starting deployment..."

# 1. Build assets
echo "📦 Building assets..."
npm run build

# 2. Install/Update dependencies
echo "📚 Installing dependencies..."
composer install --optimize-autoloader --no-dev

# 3. Clear caches
echo "🧹 Clearing caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Run migrations (jika ada)
echo "🗄️ Running migrations..."
php artisan migrate --force

# 5. Set permissions
echo "🔐 Setting permissions..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

echo "✅ Deployment completed!"
echo "🌐 Your site should be available at: https://jadixpert.com/public/"
