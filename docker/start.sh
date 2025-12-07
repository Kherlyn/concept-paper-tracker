#!/bin/sh
set -e

echo "=== Starting application ==="
echo "Working directory: $(pwd)"
echo "PORT: $PORT"
echo "APP_ENV: $APP_ENV"
echo "DB_CONNECTION: $DB_CONNECTION"

# Ensure storage directories exist with proper permissions
echo "Setting up storage directories..."
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/cache/data
mkdir -p storage/logs
mkdir -p bootstrap/cache

touch storage/logs/laravel.log
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Create SQLite database if using SQLite
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    echo "Setting up SQLite database..."
    touch database/database.sqlite
    chown www-data:www-data database/database.sqlite
    chmod 664 database/database.sqlite
fi

# Create storage link
echo "Creating storage link..."
php artisan storage:link --force 2>/dev/null || true

# Clear all caches first
echo "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Run migrations
echo "Running migrations..."
php artisan migrate --force 2>&1 || echo "Migration warning (may be OK if tables exist)"

# Only cache in production
if [ "$APP_ENV" = "production" ] && [ "$APP_DEBUG" != "true" ]; then
    echo "Caching for production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

# Update nginx config with dynamic port
if [ ! -z "$PORT" ]; then
    echo "Configuring nginx for port $PORT..."
    sed -i "s/listen 80;/listen $PORT;/g" /etc/nginx/http.d/default.conf
    sed -i "s/listen \[::\]:80;/listen [::]:$PORT;/g" /etc/nginx/http.d/default.conf
fi

echo "=== Starting services ==="
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
