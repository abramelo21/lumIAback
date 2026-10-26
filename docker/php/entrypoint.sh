#!/bin/sh
set -e

cd /var/www/html

if [ ! -d vendor ]; then
  composer install --no-interaction --prefer-dist
fi

if [ ! -f .env ] && [ -f .env.example ]; then
  cp .env.example .env
fi

if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
  php artisan key:generate --force || true
fi

mkdir -p storage/app/private/resumes storage/framework/{cache,sessions,views} storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R ug+rwx storage bootstrap/cache || true

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
  php artisan migrate --force || true
  php artisan db:seed --force || true
fi

exec "$@"
