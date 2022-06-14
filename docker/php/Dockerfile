FROM php:8.1-fpm as build

ADD ./ /app/

WORKDIR /app/

RUN apt-get update -y && \
    apt-get install -y git zip && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin && \
    mv /usr/local/bin/composer.phar /usr/local/bin/composer && \
    chmod +x /usr/local/bin/composer && \
    composer install -o --ignore-platform-req pgsql --prefer-dist --no-interaction && \
    composer dump-env prod

FROM php:8.1-fpm-alpine as runtime

ENV APP_ENV='prod'
ENV PHPFPM_MAX_CHILDREN=5
ENV PHPFPM_START_SERVERS=5
ENV PHPFPM_MIN_SPARE_SERVERS=5
ENV PHPFPM_MAX_SPARE_SERVERS=5

COPY docker/php/php.ini "$PHP_INI_DIR/php.ini"
COPY docker/php/opcache.ini $PHP_INI_DIR/conf.d/opcache.ini
COPY docker/php/php-fpm.conf /etc/php/8.1/fpm/pool.d/www.conf
COPY docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

COPY --from=build /app/ /app/

RUN apk add --no-cache --virtual .build-deps \
        postgresql-dev \
        autoconf \
        g++ \
        make \
        curl && \
    apk add --no-cache libpq && \
    docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql && \
    docker-php-ext-install pdo pgsql pdo_pgsql opcache && \
    apk del .build-deps && \
    mkdir -p /app/var/cache/prod && mkdir -p /app/var/log && \
    chown -R www-data:www-data /app/var/

WORKDIR /app/
