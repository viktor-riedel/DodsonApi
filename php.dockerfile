FROM php:8.2-fpm-alpine

ARG UID
ARG GID

ENV UID=${UID}
ENV GID=${GID}

RUN mkdir -p /var/www/html

WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN delgroup dialout

RUN addgroup -g ${GID} --system laravel
RUN adduser -G laravel --system -D -s /bin/sh -u ${UID} laravel

RUN sed -i "s/user = www-data/user = laravel/g" /usr/local/etc/php-fpm.d/www.conf
RUN sed -i "s/group = www-data/group = laravel/g" /usr/local/etc/php-fpm.d/www.conf
RUN echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf

RUN apk add --no-cache coreutils
RUN apk add --no-cache net-snmp-tools
RUN apk add --no-cache linux-headers

#extensions
RUN apk add --no-cache bzip2-dev
RUN apk add --no-cache libzip-dev
RUN apk add --no-cache zlib-dev
RUN apk add --no-cache zip
RUN apk add --no-cache jpegoptim optipng pngquant gifsicle
RUN apk add --no-cache nano
RUN apk add --no-cache unzip
RUN apk add --no-cache zip
RUN apk add --no-cache git
RUN apk add --no-cache curl
RUN apk add --no-cache wget
RUN apk add --no-cache libxml2-dev
RUN apk add --no-cache libxslt-dev
RUN apk add --no-cache libgd
RUN apk add --no-cache libpng-dev
RUN apk add --no-cache libzip-dev
RUN apk add --no-cache libmcrypt-dev
RUN apk add --no-cache gmp-dev
RUN apk add --no-cache icu-dev
RUN apk add --no-cache gettext
RUN apk add --no-cache freetype
RUN apk add --no-cache libjpeg-turbo
RUN apk add --no-cache libpng
RUN apk add --no-cache freetype-dev
RUN apk add --no-cache libjpeg-turbo-dev
RUN apk add --no-cache oniguruma-dev
RUN apk add --no-cache curl-dev
RUN apk add --no-cache libedit-dev
RUN apk add --no-cache libxml2-dev
RUN apk add --no-cache openssl-dev
RUN apk add --no-cache sqlite-dev
RUN apk add --no-cache --virtual .build-dependencies gettext-dev

RUN docker-php-ext-configure zip
RUN docker-php-ext-install -j "$(nproc)" zip
RUN docker-php-ext-configure gmp
RUN docker-php-ext-install -j "$(nproc)" gmp
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd
RUN docker-php-ext-configure intl
RUN docker-php-ext-install -j$(nproc) intl
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install bcmath
RUN docker-php-ext-install gettext
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install zip
RUN docker-php-ext-install xml
RUN docker-php-ext-install ftp
RUN docker-php-ext-install sockets

RUN mkdir -p /usr/src/php/ext/redis \
    && curl -L https://github.com/phpredis/phpredis/archive/5.3.4.tar.gz | tar xvz -C /usr/src/php/ext/redis --strip 1 \
    && echo 'redis' >> /usr/src/php-available-exts \
    && docker-php-ext-install redis

RUN apk del .build-dependencies

USER laravel

CMD ["php-fpm", "-y", "/usr/local/etc/php-fpm.conf", "-R"]

