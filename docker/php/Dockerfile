FROM php:7-fpm-alpine

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
RUN install-php-extensions opcache apcu intl pdo_mysql

COPY --from=composer /usr/bin/composer /usr/bin/composer

