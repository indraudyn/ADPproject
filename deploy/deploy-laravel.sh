#!/bin/bash
# Script Deploy Laravel

cd /var/www/adp-laravel

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies & build assets (Vite)
npm install
npm run build

# Generate app key (jika belum ada)
# php artisan key:generate

# Cache konfigurasi untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Symlink storage
php artisan storage:link

# Set permissions
chown -R www-data:www-data /var/www/adp-laravel
chmod -R 775 /var/www/adp-laravel/storage
chmod -R 775 /var/www/adp-laravel/bootstrap/cache

echo "Deploy Laravel selesai!"
