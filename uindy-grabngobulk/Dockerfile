FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libpng-dev \
    && docker-php-ext-install pdo pdo_mysql mysqli \
    && a2dismod mpm_event mpm_worker \
    && a2enmod mpm_prefork rewrite

COPY app/ /var/www/html/

RUN chown -R www-data:www-data /var/www/html

ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data

CMD ["apache2-foreground"]

EXPOSE 80