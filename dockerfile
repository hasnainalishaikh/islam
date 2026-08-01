FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql

COPY . /var/www/html/

RUN a2enmod rewrite

RUN echo "display_errors=On" >> /usr/local/etc/php/conf.d/errors.ini && \
    echo "display_startup_errors=On" >> /usr/local/etc/php/conf.d/errors.ini && \
    echo "error_reporting=E_ALL" >> /usr/local/etc/php/conf.d/errors.ini
