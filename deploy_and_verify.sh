#!/bin/bash

echo "=== Deploy and Verify Room Update Fix ==="

# Check current status
echo "1. Checking current git status..."
git status

# Pull latest changes
echo "2. Pulling latest changes..."
git pull origin master

# Check if there are any uncommitted changes
if [ -n "$(git status --porcelain)" ]; then
    echo "❌ There are uncommitted changes. Please commit them first."
    exit 1
fi

# Clear all caches
echo "3. Clearing all caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Rebuild caches
echo "4. Rebuilding caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Check if the fix is in the code
echo "5. Verifying the fix is in the code..."
if grep -q "numeric|min:1" app/Http/Controllers/AdminController.php; then
    echo "✅ Capacity validation fix found"
else
    echo "❌ Capacity validation fix NOT found"
    exit 1
fi

if grep -q "array_merge.*flattenedErrors" app/Http/Controllers/AdminController.php; then
    echo "✅ array_flatten fix found"
else
    echo "❌ array_flatten fix NOT found"
    exit 1
fi

# Test validation locally
echo "6. Testing validation locally..."
php test_production_data.php

if [ $? -eq 0 ]; then
    echo "✅ Local validation test passed!"
else
    echo "❌ Local validation test failed!"
    exit 1
fi

# Restart web server
echo "7. Restarting web server..."
sudo systemctl restart apache2

# Wait for server to restart
echo "8. Waiting for server to restart..."
sleep 5

# Test the endpoint
echo "9. Testing production endpoint..."
curl -X PUT "https://pusdatinbgn.web.id/admin/rooms/7" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -H "X-CSRF-TOKEN: test" \
  -H "Accept: application/json" \
  -d "name=TestRoom&capacity=10&description=Test&location=Test&is_active=0&amenities=wifi" \
  -v

echo ""
echo "=== Deployment Complete ==="
echo "Please test the room update functionality in the browser."
echo "Check the Laravel logs: tail -f storage/logs/laravel.log"
echo "Look for 'updateRoom called' and 'Field values received' in the logs."
