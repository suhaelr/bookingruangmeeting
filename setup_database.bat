@echo off
echo =====================================================
echo MEETING ROOM BOOKING SYSTEM - DATABASE SETUP
echo =====================================================
echo.

echo Pilih jenis database yang ingin Anda setup:
echo 1. MySQL/MariaDB Complete Schema
echo 2. MySQL/MariaDB Simple Schema  
echo 3. SQLite Schema
echo 4. PostgreSQL Schema
echo 5. Keluar
echo.

set /p choice="Masukkan pilihan (1-5): "

if "%choice%"=="1" goto mysql_complete
if "%choice%"=="2" goto mysql_simple
if "%choice%"=="3" goto sqlite
if "%choice%"=="4" goto postgresql
if "%choice%"=="5" goto exit
goto invalid

:mysql_complete
echo.
echo Setting up MySQL/MariaDB Complete Schema...
echo.
set /p db_name="Masukkan nama database (default: meeting_room_booking): "
if "%db_name%"=="" set db_name=meeting_room_booking

set /p db_user="Masukkan username MySQL (default: root): "
if "%db_user%"=="" set db_user=root

set /p db_pass="Masukkan password MySQL: "

echo Membuat database %db_name%...
mysql -u %db_user% -p%db_pass% -e "CREATE DATABASE IF NOT EXISTS %db_name%;"

echo Importing schema...
mysql -u %db_user% -p%db_pass% %db_name% < database_complete_schema.sql

if %errorlevel%==0 (
    echo.
    echo ✅ Database MySQL Complete Schema berhasil dibuat!
    echo Database: %db_name%
    echo Schema: Complete (11 tables)
) else (
    echo.
    echo ❌ Error saat membuat database!
)
goto end

:mysql_simple
echo.
echo Setting up MySQL/MariaDB Simple Schema...
echo.
set /p db_name="Masukkan nama database (default: meeting_room_booking_simple): "
if "%db_name%"=="" set db_name=meeting_room_booking_simple

set /p db_user="Masukkan username MySQL (default: root): "
if "%db_user%"=="" set db_user=root

set /p db_pass="Masukkan password MySQL: "

echo Membuat database %db_name%...
mysql -u %db_user% -p%db_pass% -e "CREATE DATABASE IF NOT EXISTS %db_name%;"

echo Importing schema...
mysql -u %db_user% -p%db_pass% %db_name% < database_simple_schema.sql

if %errorlevel%==0 (
    echo.
    echo ✅ Database MySQL Simple Schema berhasil dibuat!
    echo Database: %db_name%
    echo Schema: Simple (3 tables)
) else (
    echo.
    echo ❌ Error saat membuat database!
)
goto end

:sqlite
echo.
echo Setting up SQLite Schema...
echo.
set /p db_file="Masukkan nama file database (default: database.sqlite): "
if "%db_file%"=="" set db_file=database.sqlite

echo Membuat database SQLite...
sqlite3 %db_file% < database_sqlite_schema.sql

if %errorlevel%==0 (
    echo.
    echo ✅ Database SQLite berhasil dibuat!
    echo File: %db_file%
    echo Schema: Complete (11 tables)
) else (
    echo.
    echo ❌ Error saat membuat database!
)
goto end

:postgresql
echo.
echo Setting up PostgreSQL Schema...
echo.
set /p db_name="Masukkan nama database (default: meeting_room_booking): "
if "%db_name%"=="" set db_name=meeting_room_booking

set /p db_user="Masukkan username PostgreSQL (default: postgres): "
if "%db_user%"=="" set db_user=postgres

echo Membuat database %db_name%...
psql -U %db_user% -c "CREATE DATABASE %db_name%;"

echo Importing schema...
psql -U %db_user% -d %db_name% -f database_postgresql_schema.sql

if %errorlevel%==0 (
    echo.
    echo ✅ Database PostgreSQL berhasil dibuat!
    echo Database: %db_name%
    echo Schema: Complete (11 tables)
) else (
    echo.
    echo ❌ Error saat membuat database!
)
goto end

:invalid
echo.
echo ❌ Pilihan tidak valid! Silakan pilih 1-5.
goto end

:exit
echo.
echo Keluar dari setup database.
goto end

:end
echo.
echo =====================================================
echo SETUP SELESAI
echo =====================================================
echo.
echo File yang tersedia:
echo - database_complete_schema.sql (MySQL Complete)
echo - database_simple_schema.sql (MySQL Simple)
echo - database_sqlite_schema.sql (SQLite)
echo - database_postgresql_schema.sql (PostgreSQL)
echo - DATABASE_SETUP_README.md (Dokumentasi)
echo.
echo Untuk informasi lebih lanjut, baca DATABASE_SETUP_README.md
echo.
pause
