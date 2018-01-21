FROM php:7.1-cli

RUN apt-get update \
    && apt-get install -y \
        zlib1g-dev \
        git \
    && docker-php-ext-install \
        zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY composer.json composer.lock /app/

RUN composer install --no-dev

COPY . /app

CMD make -C propel && \
    composer dump-autoload && \
    mv propel/generated-conf/config.php app/conf/propel.php && \
    mv propel/generated-sql/default.sql /qrawler.sql && \
    rm -rf propel && \
    rm -f composer.json composer.lock
