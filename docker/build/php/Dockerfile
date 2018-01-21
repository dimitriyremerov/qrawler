FROM php:7.1-fpm

RUN docker-php-ext-install \
    pdo_mysql

COPY . /

RUN rm -rf /var/www/html

WORKDIR /var/www
