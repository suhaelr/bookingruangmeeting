#!/bin/bash

echo "=== Deploy and Test Room Update Fix ==="

# Pull latest changes
echo "1. Pulling latest changes..."
git pull origin master

# Clear all caches
echo "2. Clearing all caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Rebuild caches
echo "3. Rebuilding caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Test validation locally
echo "4. Testing validation locally..."
php test_simple_validation.php

# Check if validation passes
if [ $? -eq 0 ]; then
    echo "✅ Local validation test passed!"
else
    echo "❌ Local validation test failed!"
    exit 1
fi

# Restart web server
echo "5. Restarting web server..."
sudo systemctl restart apache2

# Wait a moment for server to restart
sleep 3

echo "6. Testing production endpoint..."
# Test the endpoint with curl
curl -X PUT "https://pusdatinbgn.web.id/admin/rooms/7" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -H "X-CSRF-TOKEN: test" \
  -H "Accept: application/json" \
  -d "name=Test Room&capacity=10&description=Test&location=Test&is_active=0&amenities=wifi" \
  -v

echo ""
echo "=== Deployment Complete ==="
echo "Please test the room update functionality in the browser."
echo "Check the browser console for detailed error messages."
echo "Check the Laravel logs: tail -f storage/logs/laravel.log"
