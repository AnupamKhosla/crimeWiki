FROM php:8.5-apache

RUN docker-php-ext-install -j"$(nproc)" mysqli \
  && a2enmod rewrite proxy proxy_http

COPY ./docker/apache-phpmyadmin.conf /etc/apache2/conf-available/phpmyadmin.conf
RUN a2enconf phpmyadmin

WORKDIR /var/www/html
