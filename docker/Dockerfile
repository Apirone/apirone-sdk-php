FROM php:8.3-apache

ARG UID=13344
ARG GID=13344

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

RUN apt-get install libmcrypt-dev && \
    pecl install mcrypt-1.0.9 && docker-php-ext-enable mcrypt && \
    docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Install useful tools and install important libaries
RUN apt-get -y update && \
    apt-get -y --no-install-recommends install nano wget \
        dialog \
        libsqlite3-dev \
        libsqlite3-0 && \
    apt-get -y --no-install-recommends install default-mysql-client \
        zlib1g-dev \
        libzip-dev \
        libicu-dev && \
    apt-get -y --no-install-recommends install --fix-missing apt-utils \
        build-essential \
        git \
        curl \
        libonig-dev && \
    apt-get install -y iputils-ping && \
    apt-get -y --no-install-recommends install --fix-missing libcurl4 \
        libcurl4-openssl-dev \
        zip \
        openssl && \
    rm -rf /var/lib/apt/lists/* && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Other PHP Extensions

RUN docker-php-ext-install curl && \
    docker-php-ext-install zip && \
    docker-php-ext-install mbstring && \
    docker-php-ext-install -j$(nproc) intl && \
    docker-php-ext-install gettext && \
    docker-php-ext-install calendar && \
    docker-php-ext-install exif

# Install xdebug
RUN pecl install xdebug-3.4.0 && docker-php-ext-enable xdebug && mkdir /var/log/xdebug

RUN apt-get update && apt-get install -y libpq-dev \
&& docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
&& docker-php-ext-install pdo pdo_pgsql pgsql

# CA-Certificates
RUN apt-get install ca-certificates

COPY docker/composer_init.sh /usr/bin/composer_init.sh
RUN cd /usr/bin && chmod +x composer_init.sh

CMD ["/usr/bin/composer_init.sh"]

RUN usermod -u ${UID} www-data && groupmod -g ${GID} www-data

# Mod rewrite
RUN a2enmod rewrite


# # Create symbolic link /var/www/src to /var/www/html to fix composer autoload error
# RUN cd /var/www && ln -s ./html src

