#!/bin/bash
set -e

echo "Deploy Akaunting"
composer install --no-dev
php artisan key:generate
php artisan migrate --force