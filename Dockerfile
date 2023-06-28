FROM composer:2.5.7 as build-prod

WORKDIR /app

COPY . .
RUN composer install --prefer-dist --no-dev --optimize-autoloader --no-interaction

# Production App
FROM php:8.2-fpm-alpine3.18 as prod

ARG user=lolapi
ARG group=uploads
ARG uid=2000
ARG gid=2010
RUN adduser -u $uid -D $user --disabled-password
RUN addgroup -g $gid $group && addgroup $user $group

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

RUN chown -R $user:$user /var/www && chown -R $user:$group /var/www/storage/app/uploads

RUN rmdir /var/www/html

USER $user

