#!/usr/bin/env bash

set -e

env=${APP_ENV:-production}

if [ "$env" != "local" ]; then
    echo "Environment is $env. Doing non-local things."
    echo "Composer install..."
    composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

    echo "Production: Caching config"
    (cd /var/www/ &&
        php artisan config:cache &&
        php artisan route:cache
    )
else
    echo "Environment is local..."
    composer install
fi

exec php-fpm