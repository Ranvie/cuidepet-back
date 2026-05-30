#!/bin/sh

set -e

while ! nc -z mysql 3306; do
  echo "Aguardando banco..."
  sleep 2
done

echo "Banco disponível!"

php artisan migrate --force

php artisan queue:work --sleep=3 --tries=3 &
exec php-fpm8.3 -F