FROM php:8.3-apache

RUN docker-php-ext-install pdo pdo_mysql

RUN a2dismod -f mpm_event || true
RUN a2dismod -f mpm_worker || true
RUN a2enmod mpm_prefork rewrite

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
