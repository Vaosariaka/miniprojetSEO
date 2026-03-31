FROM php:8.2-apache

RUN apt-get update \
    && docker-php-ext-install pdo pdo_mysql \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY ./docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf
COPY . /var/www/html

WORKDIR /var/www/html
