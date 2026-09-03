#!/bin/sh
set -e

# Configure Apache port based on Render's $PORT env variable (default 80 if not set)
PORT="${PORT:-80}"
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

# Ensure storage & cache directories exist and have proper permissions
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/app/public \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Create storage symlink if not already created
php artisan storage:link --force || true

# Run database migrations automatically on deploy
php artisan migrate --force || true

# Clear and optimize configuration & routes
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "FinanceFlow Backend ready on port ${PORT}!"
exec apache2-foreground
