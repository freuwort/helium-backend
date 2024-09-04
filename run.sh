#!/bin/bash

echo "[1/5] Go to working directory..."
cd /var/www/html

echo "[2/5] Creating storage folders..."
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p storage/logs
mkdir -p storage/app/public
mkdir -p storage/app/domain
mkdir -p storage/app/forms
mkdir -p storage/app/profiles
mkdir -p storage/app/temp

chmod -R 777 storage

echo "[3/5] Sleeping..."
sleep 5

echo "[4/5] Migrating..."
if [ "$APP_ENV" == "local" ]; then
    php artisan migrate:fresh --seed
else
    php artisan migrate
fi

echo "[5/5] Starting Apache..."
apache2-foreground