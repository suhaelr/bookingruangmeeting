#!/bin/bash

echo "=== Quick Migration Fix for Production ==="

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Not in Laravel root directory. Please cd to your Laravel app directory first."
    exit 1
fi

echo "1. Checking current migration status..."
php artisan migrate:status

echo ""
echo "2. Checking if admin_sessions table exists..."
mysql -u dsvbpgpt_lara812 -p dsvbpgpt_lara812 -e "SHOW TABLES LIKE 'admin_sessions';" 2>/dev/null

if [ $? -eq 0 ]; then
    echo "✅ admin_sessions table exists"
    
    echo ""
    echo "3. Checking table structure..."
    mysql -u dsvbpgpt_lara812 -p dsvbpgpt_lara812 -e "DESCRIBE admin_sessions;" 2>/dev/null
    
    echo ""
    echo "4. Marking migration as completed..."
    mysql -u dsvbpgpt_lara812 -p dsvbpgpt_lara812 -e "INSERT IGNORE INTO migrations (migration, batch) VALUES ('2024_01_01_000000_create_admin_sessions_table', 1);" 2>/dev/null
    
    if [ $? -eq 0 ]; then
        echo "✅ Migration marked as completed"
    else
        echo "❌ Failed to mark migration as completed"
    fi
    
else
    echo "❌ admin_sessions table does not exist"
    echo "4. Running migration..."
    php artisan migrate
fi

echo ""
echo "5. Final migration status..."
php artisan migrate:status

echo ""
echo "6. Running any pending migrations..."
php artisan migrate

echo ""
echo "=== Migration Fix Complete ==="
echo "If there are still errors, please check the table structure and migration records manually."
