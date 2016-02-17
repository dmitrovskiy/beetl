FROM php:5.6-fpm

MAINTAINER Vladimir Dmitrovskiy "dmitrovskiyvl@gmail.com"

WORKDIR /var/www/html

RUN apt-get update \
 && curl -sL https://deb.nodesource.com/setup | bash - \
 && apt-get install -y zlib1g-dev libpng-dev git libssl-dev libjpeg-dev libpng12-0 libpng3 nodejs \
 && rm -rf /var/lib/apt/lists/* \
 && pecl install mongo \
 && echo 'extension=mongo.so' >> /usr/local/etc/php/conf.d/mongo.ini \
 && pecl install redis \
 && echo 'extension=redis.so' >> /usr/local/etc/php/conf.d/redis.ini \
 && pecl install memcache \
 && echo 'extension=memcache.so' >> /usr/local/etc/php/conf.d/memcache.ini \
 && docker-php-ext-configure gd --with-jpeg-dir=/usr/lib/x86_64-linux-gnu \
 && docker-php-ext-configure pcntl \
 && docker-php-ext-install zip mysql pdo_mysql mbstring opcache gd pcntl \
 && php -r "readfile('https://getcomposer.org/installer');" | php \
 && mv composer.phar /usr/bin/composer \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
 && chmod 700 /root/.ssh \
 && chmod 600 /root/.ssh/*

VOLUME /var/www/html

ENTRYPOINT php -S 0.0.0.0:8080 ./web/index.php

