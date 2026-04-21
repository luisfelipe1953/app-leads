#!/bin/sh
set -e

php artisan config:cache
php artisan route:cache
php artisan migrate --force
php artisan db:seed --force
php artisan l5-swagger:generate

php-fpm -D
nginx -g "daemon off;"
