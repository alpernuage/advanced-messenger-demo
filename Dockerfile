FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    postgresql-dev \
    $PHPIZE_DEPS \
    git \
    && docker-php-ext-install pdo_pgsql \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
