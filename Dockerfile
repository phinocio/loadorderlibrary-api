FROM composer:2.5.7 as build-prod

WORKDIR /app

COPY . .
RUN composer install --prefer-dist --no-dev --optimize-autoloader --no-interaction

# Production App
FROM php:8.2-fpm-alpine3.18 as prod

ARG user=lolapi
RUN adduser -D $user

WORKDIR /var/www

# Set important ENV
ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS=0 \
    PHP_OPCACHE_MAX_ACCELERATED_FILES=10000 \
    PHP_OPCACHE_MEMORY_CONSUMPTION=192

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

RUN rmdir /var/www/html && rm /var/www/composer.*

USER $user


# Development build
FROM composer:2.5.7 as build-dev

WORKDIR /app

COPY . .
RUN composer install

# Development app
FROM php:8.2-fpm as dev

ARG user=lolapi
RUN useradd -G www-data,root -d /home/$user $user

WORKDIR /var/www

# Set important ENV
ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS=1 \
    PHP_OPCACHE_MAX_ACCELERATED_FILES=10000 \
    PHP_OPCACHE_MEMORY_CONSUMPTION=192

# Install system deps
RUN apt-get update && apt-get install -y \
    libzip-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install needed PHP extensions
RUN docker-php-ext-configure opcache --enable-opcache && \
	docker-php-ext-install pdo_mysql zip

# Set move OPCache config
COPY docker/dev/php/conf.d/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Copy project to container
COPY --from=build-dev /app .

RUN mkdir /home/$user && \
	chown -R $user:$user /home/$user && \
    chown -R $user:$user /var/www

RUN rmdir /var/www/html

USER $user

