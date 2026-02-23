FROM php:8.5-apache

RUN apt-get update \
  && apt-get install -y --no-install-recommends \
    libapache2-mod-evasive \
  && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install -j"$(nproc)" mysqli \
  && a2enmod rewrite proxy proxy_http reqtimeout evasive

COPY ./docker/apache-phpmyadmin.conf /etc/apache2/conf-available/phpmyadmin.conf
COPY ./docker/apache-security.conf /etc/apache2/conf-available/apache-security.conf
COPY ./docker/php/custom.ini /usr/local/etc/php/conf.d/zz-custom.ini
RUN a2enconf phpmyadmin
RUN a2enconf apache-security

WORKDIR /var/www/html
