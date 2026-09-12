#!/usr/bin/env bash
set -e
composer install --no-dev --optimize-autoloader
npm install
npm run build
touch database/database.sqlite
cp -n .env.example .env || true
php artisan key:generate
php artisan migrate --seed
php artisan storage:link || true
php artisan optimize
echo "macbilling_v2 build selesai."
