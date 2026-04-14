FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y \
    software-properties-common \
    curl \
    unzip \
    && add-apt-repository ppa:ondrej/php \
    && add-apt-repository ppa:ondrej/apache2 \
    && apt-get update && apt-get install -y \
    apache2 \
    php8.2 \
    php8.2-mysql \
    php8.2-xml \
    php8.2-curl \
    php8.2-mbstring \
    php8.2-zip \
    php8.2-intl \
    php8.2-sqlite3 \
    libapache2-mod-php8.2 \
    && a2enmod php8.2 rewrite headers \
    && apt-get clean \
    && rm -rf /var/www/html/* \
    && rm -f /etc/apache2/sites-enabled/000-default.conf

RUN curl -sS https://getcomposer.org/installer | php8.2 -- --install-dir=/usr/local/bin --filename=composer

RUN composer create-project simplesamlphp/simplesamlphp:^2.3 \
    /var/simplesamlphp --no-interaction --prefer-dist

RUN mkdir -p /var/simplesamlphp/log \
    && mkdir -p /var/simplesamlphp/data \
    && mkdir -p /var/simplesamlphp/cert \
    && mkdir -p /tmp/simplesaml

COPY fix_proxy.php /var/www/fix_proxy.php
COPY app/ /var/www/html/
COPY simplesaml-config/ /var/simplesamlphp/config/
COPY simplesaml-config/saml20-idp-remote.php /var/simplesamlphp/metadata/saml20-idp-remote.php
COPY simplesaml-config/cert/ /var/simplesamlphp/cert/

RUN chown -R www-data:www-data /var/www/html \
    && chown -R www-data:www-data /var/simplesamlphp \
    && chown -R www-data:www-data /tmp/simplesaml

COPY apache.conf /etc/apache2/sites-enabled/000-default.conf

EXPOSE 80

CMD ["apache2ctl", "-D", "FOREGROUND"]