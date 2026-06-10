FROM php:8.4-apache

RUN docker-php-ext-install pdo_mysql \
    && a2enmod rewrite headers

WORKDIR /var/www/html

COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
