FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y \
    apache2 \
    php8.1 \
    php8.1-mysql \
    libapache2-mod-php8.1 \
    && a2enmod php8.1 rewrite \
    && apt-get clean

COPY app/ /var/www/html/

RUN chown -R www-data:www-data /var/www/html \
    && rm -f /var/www/html/index.html

EXPOSE 80

CMD ["apache2ctl", "-D", "FOREGROUND"]