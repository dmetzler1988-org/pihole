FROM php:8.3-apache AS base

ARG TIMEZONE

# Required tools for system
RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libpng-dev \
    libzip-dev \
    libxml2-dev

# Tools for analysis and support
RUN apt-get install -y \
    vim \
    iputils-ping \
    tcptraceroute \
    netcat-traditional

RUN docker-php-ext-install \
    zip \
    gd \
    intl

# Set timezone and reload config
RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime
RUN echo ${TIMEZONE} > /etc/timezone

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy php config
COPY ./docker/web/conf.d/php-ini-overrides.ini $PHP_INI_DIR/conf.d/

# Apache configuration
RUN sed -i 's!/var/www/html!/var/www/public!g' /etc/apache2/sites-available/000-default.conf
RUN rm -rf /var/www/html && mkdir /var/www/html # Cleanup folder
RUN echo "AllowEncodedSlashes On" >> /etc/apache2/apache2.conf
RUN a2enmod rewrite
RUN a2enmod headers
COPY ./docker/web/apache/vhost.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

# Copy files and install
COPY . .
RUN composer install

# Set correct permissions for apache
RUN chown -R www-data:www-data .

#############################################################################
################################### PROD ####################################
#############################################################################

FROM base AS prod

ENV APP_ENV=prod

#############################################################################
#################################### DEV ####################################
#############################################################################

FROM base AS dev

ENV APP_ENV=dev

# Install xdebug
RUN pecl install xdebug-3.3.2 && docker-php-ext-enable xdebug

# Copy xdebug config
COPY ./docker/web/conf.d/xdebug.ini $PHP_INI_DIR/conf.d/
