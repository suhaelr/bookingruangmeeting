#!/bin/bash

echo "====================================================="
echo "MEETING ROOM BOOKING SYSTEM - DATABASE SETUP"
echo "====================================================="
echo

echo "Pilih jenis database yang ingin Anda setup:"
echo "1. MySQL/MariaDB Complete Schema"
echo "2. MySQL/MariaDB Simple Schema"  
echo "3. SQLite Schema"
echo "4. PostgreSQL Schema"
echo "5. Keluar"
echo

read -p "Masukkan pilihan (1-5): " choice

case $choice in
    1)
        echo
        echo "Setting up MySQL/MariaDB Complete Schema..."
        echo
        read -p "Masukkan nama database (default: meeting_room_booking): " db_name
        db_name=${db_name:-meeting_room_booking}
        
        read -p "Masukkan username MySQL (default: root): " db_user
        db_user=${db_user:-root}
        
        read -s -p "Masukkan password MySQL: " db_pass
        echo
        
        echo "Membuat database $db_name..."
        mysql -u $db_user -p$db_pass -e "CREATE DATABASE IF NOT EXISTS $db_name;"
        
        echo "Importing schema..."
        mysql -u $db_user -p$db_pass $db_name < database_complete_schema.sql
        
        if [ $? -eq 0 ]; then
            echo
            echo "✅ Database MySQL Complete Schema berhasil dibuat!"
            echo "Database: $db_name"
            echo "Schema: Complete (11 tables)"
        else
            echo
            echo "❌ Error saat membuat database!"
        fi
        ;;
    2)
        echo
        echo "Setting up MySQL/MariaDB Simple Schema..."
        echo
        read -p "Masukkan nama database (default: meeting_room_booking_simple): " db_name
        db_name=${db_name:-meeting_room_booking_simple}
        
        read -p "Masukkan username MySQL (default: root): " db_user
        db_user=${db_user:-root}
        
        read -s -p "Masukkan password MySQL: " db_pass
        echo
        
        echo "Membuat database $db_name..."
        mysql -u $db_user -p$db_pass -e "CREATE DATABASE IF NOT EXISTS $db_name;"
        
        echo "Importing schema..."
        mysql -u $db_user -p$db_pass $db_name < database_simple_schema.sql
        
        if [ $? -eq 0 ]; then
            echo
            echo "✅ Database MySQL Simple Schema berhasil dibuat!"
            echo "Database: $db_name"
            echo "Schema: Simple (3 tables)"
        else
            echo
            echo "❌ Error saat membuat database!"
        fi
        ;;
    3)
        echo
        echo "Setting up SQLite Schema..."
        echo
        read -p "Masukkan nama file database (default: database.sqlite): " db_file
        db_file=${db_file:-database.sqlite}
        
        echo "Membuat database SQLite..."
        sqlite3 $db_file < database_sqlite_schema.sql
        
        if [ $? -eq 0 ]; then
            echo
            echo "✅ Database SQLite berhasil dibuat!"
            echo "File: $db_file"
            echo "Schema: Complete (11 tables)"
        else
            echo
            echo "❌ Error saat membuat database!"
        fi
        ;;
    4)
        echo
        echo "Setting up PostgreSQL Schema..."
        echo
        read -p "Masukkan nama database (default: meeting_room_booking): " db_name
        db_name=${db_name:-meeting_room_booking}
        
        read -p "Masukkan username PostgreSQL (default: postgres): " db_user
        db_user=${db_user:-postgres}
        
        echo "Membuat database $db_name..."
        psql -U $db_user -c "CREATE DATABASE $db_name;"
        
        echo "Importing schema..."
        psql -U $db_user -d $db_name -f database_postgresql_schema.sql
        
        if [ $? -eq 0 ]; then
            echo
            echo "✅ Database PostgreSQL berhasil dibuat!"
            echo "Database: $db_name"
            echo "Schema: Complete (11 tables)"
        else
            echo
            echo "❌ Error saat membuat database!"
        fi
        ;;
    5)
        echo
        echo "Keluar dari setup database."
        exit 0
        ;;
    *)
        echo
        echo "❌ Pilihan tidak valid! Silakan pilih 1-5."
        ;;
esac

echo
echo "====================================================="
echo "SETUP SELESAI"
echo "====================================================="
echo
echo "File yang tersedia:"
echo "- database_complete_schema.sql (MySQL Complete)"
echo "- database_simple_schema.sql (MySQL Simple)"
echo "- database_sqlite_schema.sql (SQLite)"
echo "- database_postgresql_schema.sql (PostgreSQL)"
echo "- DATABASE_SETUP_README.md (Dokumentasi)"
echo
echo "Untuk informasi lebih lanjut, baca DATABASE_SETUP_README.md"
echo
