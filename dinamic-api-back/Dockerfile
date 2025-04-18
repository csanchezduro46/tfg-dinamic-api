FROM php:8.2-fpm

WORKDIR /var/html/www

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update -y \
    && apt-get install -y \
    libxml2-dev \
    libzip-dev \
    libxslt-dev \
    zip \
    unzip \
    libpng-dev \
    vim \
    acl \
    net-tools \
    ntp \
    ntpdate \
    telnet \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    supervisor \
    nano \
    && apt-get clean -y

RUN docker-php-ext-install \
    intl \
    pdo \
    xml \
    gd \
    zip \
    bcmath \
    xsl \
    mysqli


RUN apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

VOLUME ["/var/www/html"]

COPY . .

RUN mv .env.dev .env

# RUN composer install && php artisan jwt:secret
RUN composer install
