FROM php:7.0-fpm
RUN usermod -u 1000 www-data
ADD symfony.ini /usr/local/etc/php/conf.d
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng12-dev git unzip \
    && docker-php-ext-install -j$(nproc) iconv mcrypt pdo_mysql zip\
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && pecl install apcu \
    && pecl install xdebug \
    && pecl install apcu_bc-beta \
    && docker-php-ext-enable apcu \
    && docker-php-ext-enable xdebug \
    && echo extension=apc.so >> /usr/local/etc/php/conf.d/docker-php-ext-apcu.ini

# Available EXT on php 7
# bcmath bz2 calendar ctype curl dba dom enchant exif fileinfo filter ftp gd gettext gmp hash
# iconv imap interbase intl json ldap mbstring mcrypt mysqli oci8 odbc opcache pcntl pdo pdo_dblib
# pdo_firebird pdo_mysql pdo_oci pdo_odbc pdo_pgsql pdo_sqlite pgsql phar posix pspell readline recode
# reflection session shmop simplexml snmp soap sockets spl standard sysvmsg sysvsem sysvshm tidy tokenizer
# wddx xml xmlreader xmlrpc xmlwriter xsl zip



