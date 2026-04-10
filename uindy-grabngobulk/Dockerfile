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
    libapache2-mod-php8.1 \
    curl \
    unzip \
    && a2enmod php8.1 rewrite \
    && apt-get clean \
    && rm -rf /var/www/html/* \
    && rm -f /etc/apache2/sites-enabled/000-default.conf

# Install SimpleSAMLphp
RUN curl -L https://github.com/simplesamlphp/simplesamlphp/releases/download/v2.3.4/simplesamlphp-2.3.4.tar.gz \
    -o /var/simplesamlphp.tar.gz \
    && tar -xzf /var/simplesamlphp.tar.gz -C /var/ \
    && mv /var/simplesamlphp-2.3.4 /var/simplesamlphp \
    && rm /var/simplesamlphp.tar.gz

COPY app/ /var/www/html/
COPY simplesaml-config/ /var/simplesamlphp/config/

RUN apt-get install -y composer \
    && composer create-project simplesamlphp/simplesamlphp /var/simplesamlphp --no-interaction --prefer-dist

COPY apache.conf /etc/apache2/sites-enabled/000-default.conf

EXPOSE 80

CMD ["apache2ctl", "-D", "FOREGROUND"]