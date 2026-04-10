FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y \
    apache2 \
    php8.1 \
    php8.1-mysql \
    libapache2-mod-php8.1 \
    && a2enmod php8.1 rewrite \
    && apt-get clean \
    && rm -rf /var/www/html/* \
    && rm -f /etc/apache2/sites-enabled/000-default.conf

COPY app/ /var/www/html/

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2ctl", "-D", "FOREGROUND"]