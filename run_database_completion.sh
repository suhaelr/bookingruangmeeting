#!/bin/bash

echo "=== Database Completion Script ==="

# Database credentials
DB_HOST="127.0.0.1"
DB_NAME="dsvbpgpt_lara812"
DB_USER="dsvbpgpt_lara812"
DB_PASS="superadmin123"

echo "1. Connecting to database: $DB_NAME"

# Test database connection
mysql -h $DB_HOST -u $DB_USER -p$DB_PASS -e "USE $DB_NAME; SELECT 'Database connection successful' as status;"

if [ $? -eq 0 ]; then
    echo "✅ Database connection successful"
else
    echo "❌ Database connection failed"
    exit 1
fi

echo ""
echo "2. Running database completion script..."

# Run the SQL script
mysql -h $DB_HOST -u $DB_USER -p$DB_PASS $DB_NAME < simple_database_completion.sql

if [ $? -eq 0 ]; then
    echo "✅ Database completion script executed successfully"
else
    echo "❌ Database completion script failed"
    exit 1
fi

echo ""
echo "3. Verifying database structure..."

# Show tables
echo "Tables in database:"
mysql -h $DB_HOST -u $DB_USER -p$DB_PASS $DB_NAME -e "SHOW TABLES;"

# Show record counts
echo ""
echo "Record counts:"
mysql -h $DB_HOST -u $DB_USER -p$DB_PASS $DB_NAME -e "
SELECT 'meeting_rooms' as table_name, COUNT(*) as count FROM meeting_rooms
UNION ALL
SELECT 'bookings' as table_name, COUNT(*) as count FROM bookings
UNION ALL
SELECT 'users' as table_name, COUNT(*) as count FROM users;"

# Show meeting_rooms structure
echo ""
echo "Meeting Rooms Structure:"
mysql -h $DB_HOST -u $DB_USER -p$DB_PASS $DB_NAME -e "DESCRIBE meeting_rooms;"

# Show bookings structure
echo ""
echo "Bookings Structure:"
mysql -h $DB_HOST -u $DB_USER -p$DB_PASS $DB_NAME -e "DESCRIBE bookings;"

# Show users structure
echo ""
echo "Users Structure:"
mysql -h $DB_HOST -u $DB_USER -p$DB_PASS $DB_NAME -e "DESCRIBE users;"

echo ""
echo "=== Database Completion Finished ==="
echo "Database structure has been completed successfully!"
