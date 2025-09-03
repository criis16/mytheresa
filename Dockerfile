FROM php:8.1-fpm-alpine

RUN docker-php-ext-install pdo_mysql

# INSTALL COMPOSER
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
