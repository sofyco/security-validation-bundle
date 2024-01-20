FROM php:8.3-cli-alpine

RUN apk add icu-dev && docker-php-ext-configure intl && docker-php-ext-install intl
RUN curl -s https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app
