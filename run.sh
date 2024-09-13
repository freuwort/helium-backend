#!/bin/bash

echo "[1/7] Go to working directory..."
cd /var/www/html


echo "[2/7] Creating storage folders..."
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


echo "[3/7] Linking storage..."
php artisan storage:link


echo "[4/7] Migrating..."
if [ "$APP_ENV" == "local" ]; then
    php artisan migrate:fresh
else
    php artisan migrate
fi


echo "[5/7] Seeding..."
php artisan seed:production


echo "[6/7] Creating super user..."
php artisan superuser:init


echo "[7/7] Starting Apache..."
apache2-foreground