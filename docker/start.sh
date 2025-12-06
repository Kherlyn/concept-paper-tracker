#!/bin/sh
set -e

echo "Starting application..."

# Create SQLite database if using SQLite and it doesn't exist
if [ "$DB_CONNECTION" = "sqlite" ] && [ ! -f /var/www/html/database/database.sqlite ]; then
    echo "Creating SQLite database..."
    touch /var/www/html/database/database.sqlite
    chown www-data:www-data /var/www/html/database/database.sqlite
fi

# Generate key if not set
if [ -z "$APP_KEY" ]; then
    echo "Warning: APP_KEY not set, generating one..."
    php artisan key:generate --force
fi

# Clear and cache config
echo "Caching configuration..."
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
echo "Running migrations..."
php artisan migrate --force || echo "Migration failed, continuing anyway..."

# Update nginx config with dynamic port
if [ ! -z "$PORT" ]; then
    echo "Configuring nginx for port $PORT..."
    sed -i "s/listen 80;/listen $PORT;/g" /etc/nginx/http.d/default.conf
    sed -i "s/listen \[::\]:80;/listen [::]:$PORT;/g" /etc/nginx/http.d/default.conf
fi

echo "Starting supervisord..."
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
