#!/usr/bin/env bash

set -euo pipefail

cd /var/www/html

# The container is configured entirely through compose environment variables.
# An empty .env keeps dotenv quiet and avoids leaking host secrets into images.
[ -f .env ] || touch .env

mkdir -p \
    storage/app/public/documents \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/testing \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# Keep the writable directories owned by Apache even when Docker creates
# named volumes for the first time.
chown -R www-data:www-data storage bootstrap/cache

if [ ! -L public/storage ]; then
    php artisan storage:link || true
fi

php artisan optimize:clear >/dev/null 2>&1 || true

php artisan migrate --force

if [ "${SEED_DATABASE:-true}" = "true" ]; then
    php artisan db:seed --force
fi

# Production benefits from the compiled config, routes and views. In local
# mode we leave them uncached so tests and edits are picked up immediately.
if [ "${APP_ENV:-local}" = "production" ]; then
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
else
    php artisan optimize:clear >/dev/null 2>&1 || true
fi

chown -R www-data:www-data storage bootstrap/cache

exec apache2-foreground
