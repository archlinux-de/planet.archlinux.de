FROM php:8.4-fpm-alpine

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN apk add --no-cache git

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
RUN install-php-extensions opcache apcu intl pdo_mysql
RUN IPE_DONT_ENABLE=1 install-php-extensions xdebug-stable

COPY --from=composer /usr/bin/composer /usr/bin/composer
