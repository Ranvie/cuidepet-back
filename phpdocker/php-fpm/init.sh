#!/bin/sh

set -e

while ! nc -z mysql 3306; do
  echo "Aguardando banco..."
  sleep 2
done

echo "Banco disponível!"

php artisan migrate --force

exec php-fpm8.3 -F