#!/bin/sh
set -e

echo "===== Laravel boot (Railway) ====="

php artisan config:clear || true
php artisan cache:clear || true

php artisan migrate --force
php artisan db:seed --force || true

echo "===== Starting PHP-FPM ====="
php-fpm -D

echo "===== Starting Nginx ====="
nginx -g "daemon off;"
