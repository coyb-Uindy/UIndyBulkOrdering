FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y \
    apache2 \
    php8.1 \
    php8.1-mysql \
    php8.1-xml \
    php8.1-curl \
    php8.1-mbstring \
    php8.1-zip \
    php8.1-intl \
    php8.1-sqlite3 \
    libapache2-mod-php8.1 \
    curl \
    unzip \
    composer \
    && a2enmod php8.1 rewrite \
    && apt-get clean \
    && rm -rf /var/www/html/* \
    && rm -f /etc/apache2/sites-enabled/000-default.conf

RUN composer create-project simplesamlphp/simplesamlphp:^2.3 \
    /var/simplesamlphp --no-interaction --prefer-dist \
    --ignore-platform-reqs

RUN mkdir -p /var/simplesamlphp/log \
    && mkdir -p /var/simplesamlphp/data \
    && mkdir -p /var/simplesamlphp/cert \
    && mkdir -p /tmp/simplesaml

COPY app/ /var/www/html/
COPY simplesaml-config/ /var/simplesamlphp/config/

RUN chown -R www-data:www-data /var/www/html \
    && chown -R www-data:www-data /var/simplesamlphp \
    && chown -R www-data:www-data /tmp/simplesaml

COPY apache.conf /etc/apache2/sites-enabled/000-default.conf

EXPOSE 80

CMD ["apache2ctl", "-D", "FOREGROUND"]