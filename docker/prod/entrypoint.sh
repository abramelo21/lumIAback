#!/bin/sh
set -e
cd /var/www/html
php artisan config:cache || true
php artisan route:cache || true
php artisan migrate --force || true
exec "$@"
