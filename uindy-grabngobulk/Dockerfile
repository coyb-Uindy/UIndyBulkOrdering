FROM php:8.2-apache

# Enable required Apache modules and fix MPM conflict
RUN a2dismod mpm_event 2>/dev/null || true \
    && a2enmod mpm_prefork rewrite headers

# Install PHP extensions needed by the app and SimpleSAMLphp
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install \
        pdo_mysql \
        intl \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copy pre-bundled SimpleSAMLphp (avoids slow Composer download at build time)
COPY simplesamlphp/ /var/simplesamlphp/

RUN mkdir -p /var/simplesamlphp/log \
    && mkdir -p /var/simplesamlphp/data \
    && mkdir -p /var/simplesamlphp/cert \
    && mkdir -p /tmp/simplesaml

# Copy app files
COPY app/ /var/www/html/
COPY simplesaml-config/ /var/simplesamlphp/config/
COPY simplesaml-config/saml20-idp-remote.php /var/simplesamlphp/metadata/saml20-idp-remote.php
COPY simplesaml-config/cert/ /var/simplesamlphp/cert/

RUN chown -R www-data:www-data /var/www/html \
    && chown -R www-data:www-data /var/simplesamlphp \
    && chown -R www-data:www-data /tmp/simplesaml

# Apache config
COPY apache.conf /etc/apache2/sites-enabled/000-default.conf
RUN a2dissite 000-default 2>/dev/null || true \
    && a2ensite 000-default

EXPOSE 80

CMD ["apache2-foreground"]