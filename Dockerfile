FROM phpdockerio/php:8.3-fpm
WORKDIR "/var/www"
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