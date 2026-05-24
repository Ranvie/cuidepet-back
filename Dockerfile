FROM phpdockerio/php:8.3-fpm
WORKDIR "/var/www"

# instala dependências do sistema + composer
RUN apt-get update && apt-get install -y \
    git unzip curl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# copia primeiro só o composer files (melhor cache)
COPY composer.json composer.lock ./

COPY . /var/www

RUN apt-get update \
    && apt-get -y --no-install-recommends install \
        php8.3-bcmath \
        php8.3-mysql \
        php8.3-redis \
    && apt-get install -y netcat-openbsd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

RUN composer install --no-dev --optimize-autoloader