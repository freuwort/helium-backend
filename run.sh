#!/bin/bash

cd /var/www/html

echo "Sleeping..."
sleep 5

echo "Migrating..."
if [ "$APP_ENV" == "local" ]; then
    php artisan migrate:fresh --seed
else
    php artisan migrate
fi

echo "Starting Apache..."
apache2-foreground