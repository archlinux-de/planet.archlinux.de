FROM php:8.1-fpm-alpine

RUN apk add --no-cache git

COPY --from=mlocati/php-extension-installer:1.5 /usr/bin/install-php-extensions /usr/bin/
RUN install-php-extensions opcache apcu intl pdo_mysql

COPY --from=composer:2.3 /usr/bin/composer /usr/bin/composer
