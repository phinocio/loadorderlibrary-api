FROM composer:2.5.7 as build-prod

WORKDIR /app

COPY . .
RUN composer install --prefer-dist --no-dev --optimize-autoloader --no-interaction

# Production App
FROM php:8.2-fpm-alpine3.18 as prod

ARG user=lolapi
ARG uid=2000
RUN adduser -u $uid -D $user

WORKDIR /var/www

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
COPY docker/prod/php/conf.d/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

# Copy project to container
COPY --from=build-prod /app .

RUN chown -R $user:$user /var/www

RUN rmdir /var/www/html

USER $user

######################################################################################3
# Development build
FROM composer:2.5.7 as build-dev

WORKDIR /app

COPY . .
RUN composer install

# Development app
FROM php:8.2-fpm-alpine3.18 as dev

ARG user=lolapi
# Most users are 1000 UID, use ARG if it's different...
ARG uid=1000
RUN adduser -u $uid -D $user

WORKDIR /var/www

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
COPY docker/dev/php/conf.d/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

# Copy project to container
COPY --from=build-dev /app .

RUN chown -R $user:$user /var/www

RUN rmdir /var/www/html

USER $user

