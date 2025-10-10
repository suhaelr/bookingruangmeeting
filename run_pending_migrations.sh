#!/bin/bash

echo "=== Running Pending Migrations ==="

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Not in Laravel root directory. Please cd to your Laravel app directory first."
    exit 1
fi

echo "1. Current migration status:"
php artisan migrate:status

echo ""
echo "2. Running all pending migrations..."
php artisan migrate

if [ $? -eq 0 ]; then
    echo "✅ All migrations completed successfully!"
else
    echo "❌ Some migrations failed. Trying individual migrations..."
    
    echo ""
    echo "3. Running individual migrations..."
    
    # Run migrations one by one
    php artisan migrate --path=database/migrations/2024_01_01_000002_create_meeting_rooms_table.php
    php artisan migrate --path=database/migrations/2024_01_01_000003_create_bookings_table.php
    php artisan migrate --path=database/migrations/2025_10_09_151949_add_columns_to_users_table.php
    php artisan migrate --path=database/migrations/2025_10_09_173254_add_email_verification_to_users_table.php
    php artisan migrate --path=database/migrations/2025_10_10_051815_remove_hourly_rate_from_meeting_rooms_table.php
    php artisan migrate --path=database/migrations/2025_10_10_052238_add_unit_kerja_and_dokumen_perizinan_to_bookings_table.php
    php artisan migrate --path=database/migrations/2025_10_10_061751_update_admin_sessions_table_structure.php
fi

echo ""
echo "4. Final migration status:"
php artisan migrate:status

echo ""
echo "5. Checking if all tables exist:"
mysql -u dsvbpgpt_lara812 -p dsvbpgpt_lara812 -e "SHOW TABLES;" 2>/dev/null

echo ""
echo "=== Migration Process Complete ==="
echo "If all migrations are successful, you can now deploy the room update fix."
