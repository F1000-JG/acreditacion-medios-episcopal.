FROM php:8.3-apache

RUN docker-php-ext-install pdo pdo_mysql
RUN a2enmod rewrite

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

ENV APACHE_DOCUMENT_ROOT=/var/www/html
CMD ["apache2-foreground"]
