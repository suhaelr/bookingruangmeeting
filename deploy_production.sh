#!/bin/bash

# Production Deployment Script untuk jadixpert.com
# Database: ypimfbgf_lara470

echo "🚀 Starting production deployment..."

# 1. Generate application key
echo "🔑 Generating application key..."
php artisan key:generate

# 2. Build assets untuk production
echo "📦 Building production assets..."
npm run build

# 3. Install/Update dependencies untuk production
echo "📚 Installing production dependencies..."
composer install --optimize-autoloader --no-dev

# 4. Clear dan cache config
echo "🧹 Clearing and caching configurations..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# 5. Cache untuk production
echo "⚡ Caching for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Run migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# 7. Set permissions
echo "🔐 Setting proper permissions..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 8. Create .htaccess untuk Apache (jika diperlukan)
echo "📝 Creating .htaccess file..."
cat > public/.htaccess << 'EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
EOF

echo "✅ Production deployment completed!"
echo "🌐 Your site should be available at: https://jadixpert.com"
echo "📊 Database: ypimfbgf_lara470"
echo "📁 Directory: /home/ypimfbgf/public_html"

# 9. Test database connection
echo "🔍 Testing database connection..."
php artisan tinker --execute="
try {
    DB::connection()->getPdo();
    echo 'Database connection successful!';
} catch (Exception \$e) {
    echo 'Database connection failed: ' . \$e->getMessage();
}
"
