# Adapted from https://www.digitalocean.com/community/tutorials/how-to-install-and-set-up-laravel-with-docker-compose-on-ubuntu-22-04

FROM php:8.2-fpm

# Set important ENV

ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS=0 \
    PHP_OPCACHE_MAX_ACCELERATED_FILES=10000 \
    PHP_OPCACHE_MEMORY_CONSUMPTION=192

ARG user
ARG uid

# Install system deps
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

COPY docker/start.sh /usr/local/bin/start
RUN chmod +x /usr/local/bin/start

# Install needed PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd opcache iconv

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set move OPCache config
COPY docker/php/conf.d/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Copy project to container
COPY . /var/www

# Create system user to run Composer and Artisan commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user && \
    chown -R $user:$user /var/www

WORKDIR /var/www

USER $user

CMD ["/usr/local/bin/start"]
