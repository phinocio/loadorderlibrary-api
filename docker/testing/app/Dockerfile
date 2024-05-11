# Testing build
FROM composer:2.5.7 as build-testing-composer

WORKDIR /app

COPY . .
RUN composer install --prefer-dist --no-dev --optimize-autoloader --no-interaction

##################################### End build step

# Testing App
FROM php:8.2-fpm-alpine3.18 as app-testing

ARG user=lolapi
ARG uid=2000
RUN adduser -u $uid -D $user

WORKDIR /srv/testingapi.loadorderlibrary.com

# Install system deps
RUN apk update && apk add \
    libzip-dev

# Clear cache
RUN apk cache clean && \
    rm -rf /var/lib/apk/lists/* && \
    rm -rf /var/cache/apk/*

# Install needed PHP extensions
RUN docker-php-ext-configure opcache --enable-opcache && \
	docker-php-ext-install pdo_mysql zip

# Set move OPCache config
COPY docker/prod/app/conf.d/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Copy php-fpm settings to container
COPY docker/prod/app/conf.d/www.conf /usr/local/etc/php-fpm.d/www.conf

# Instead of copying the one from inside contianer, do this because memory limit changed.
COPY docker/prod/app/conf.d/php.ini-production /usr/local/etc/php/php.ini

# Copy project to container
COPY --from=build-testing-composer /app .

RUN chown -R $user:$user /srv/testingapi.loadorderlibrary.com

USER $user

