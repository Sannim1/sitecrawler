FROM php:5.6-cli

MAINTAINER Abdulmusawwir Sanni

RUN apt-get update && apt-get install -y zip unzip curl git \
    && pecl install xdebug-2.5.0 \
    && docker-php-ext-enable xdebug

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --install-dir=/usr/bin --filename=composer
RUN php -r "unlink('composer-setup.php');"

RUN mkdir /sitecrawler
WORKDIR /sitecrawler
