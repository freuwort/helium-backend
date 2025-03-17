#!/bin/bash

echo "[1/8] Go to working directory..."
cd /var/www/html


echo "[2/8] Creating storage folders..."
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p storage/logs
mkdir -p storage/app/domain
mkdir -p storage/app/forms
mkdir -p storage/app/public
mkdir -p storage/app/scribe
mkdir -p storage/app/temp
chmod -R 777 storage


echo "[3/8] Linking storage..."
php artisan storage:link


echo "[4/8] Migrating..."
if [ "$APP_ENV" == "local" ]; then
    php artisan migrate:fresh
else
    php artisan migrate
fi


echo "[5/8] Generating Docs..."
php artisan scribe:generate


echo "[6/8] Seeding..."
php artisan seed:production


echo "[7/8] Creating super user..."
php artisan superuser:init


echo "[8/8] Starting Apache..."
apache2-foreground