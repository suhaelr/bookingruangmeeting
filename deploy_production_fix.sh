#!/bin/bash

echo "=== Deploy Production Fix ==="
echo "Memperbaiki konfigurasi untuk production..."

# Backup file app.php saat ini
cp config/app.php config/app.php.backup

# Update konfigurasi app.php untuk production
sed -i "s/'url' => env('APP_URL', '[^']*'),/'url' => env('APP_URL', 'https:\/\/pusdatinbgn.web.id'),/" config/app.php

echo "Konfigurasi app.php telah diupdate untuk production"
echo "URL default: https://pusdatinbgn.web.id"

# Buat file .env untuk production
cat > .env.production << EOF
APP_NAME="Admin Panel"
APP_ENV=production
APP_KEY=base64:RJLU1a7rGvaKGEBrz6ZDvPXsvKfkkn0/NlilHzs>
APP_DEBUG=true
APP_URL=https://pusdatinbgn.web.id

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dsvbpgpt_lara812
DB_USERNAME=dsvbpgpt_lara812
DB_PASSWORD=superadmin123

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail Configuration for pusdatinbgn.web.id
MAIL_MAILER=smtp
MAIL_HOST=mail.pusdatinbgn.web.id
MAIL_PORT=465
MAIL_USERNAME=admin@pusdatinbgn.web.id
MAIL_PASSWORD=superadmin123
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=admin@pusdatinbgn.web.id
MAIL_FROM_NAME="\${APP_NAME}"

VITE_APP_NAME="\${APP_NAME}"
EOF

echo "File .env.production telah dibuat"
echo "Copy file .env.production ke .env di server production"

# Git operations
echo "Menambahkan perubahan ke git..."
git add config/app.php
git add env_production_template.txt
git add fix_app_config.php
git add deploy_production_fix.sh

echo "Commit perubahan..."
git commit -m "Fix app.php configuration for production environment

- Update default URL to https://pusdatinbgn.web.id
- Add production environment template
- Add deployment script for production fix"

echo "Push ke GitHub..."
git push origin master

echo "=== Deploy Production Fix Selesai ==="
echo "Konfigurasi telah diperbaiki dan di-push ke GitHub"
echo "Untuk deploy ke server:"
echo "1. Copy .env.production ke .env di server"
echo "2. Jalankan: php artisan config:cache"
echo "3. Jalankan: php artisan route:cache"
