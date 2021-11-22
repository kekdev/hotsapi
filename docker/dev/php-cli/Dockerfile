FROM php:8.0-cli-alpine

RUN apk -U upgrade && \
	apk add git curl unzip

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions @composer intl pdo_mysql mcrypt memcached xdebug zip && \
    mv /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

COPY php/conf.d/ /usr/local/etc/php/conf.d/

ARG USER_ID=1000
ARG GROUP_ID=1000

RUN adduser -S -D -G www-data -h /var/www -s /sbin/nologin -u $USER_ID user

USER user

WORKDIR /app
