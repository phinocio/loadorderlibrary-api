# Production build
FROM composer:2.5.7 as build-prod

WORKDIR /app

COPY . .
RUN composer install --prefer-dist --no-dev --optimize-autoloader --no-interaction

##################################### End build step

# Production App
FROM php:8.2-fpm-alpine3.18 as app-prod

ARG user=lolapi
ARG uid=2000
RUN adduser -u $uid -D $user

WORKDIR /srv/api.loadorderlibrary.com

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

RUN cp docker/prod/app/conf.d/php.ini-production /usr/local/etc/php/php.ini

# Copy project to container
COPY --from=build-prod /app .

RUN chown -R $user:$user /srv/api.loadorderlibrary.com

USER $user

