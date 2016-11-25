# Start from PHP 7.0
# Take a look at the PHP container documentation on the Dockerhub for more detailed
# info on running the container: https://hub.docker.com/_/php/
FROM php:7.0-fpm

# Installing git to install dependencies later and necessary libraries for postgres
# and mysql including client tools. You can remove those if you don't need them for your build.
RUN apt-get update && \
    apt-get install -y \
      git \
      libpq-dev\
      pkg-config build-essential libmemcached-dev

# Installing php 7 memcached
RUN git clone -b php7 https://github.com/php-memcached-dev/php-memcached.git /usr/src/php/ext/memcached 
RUN cd /usr/src/php/ext/memcached && phpize && ./configure --disable-memcached-sasl && make all && docker-php-ext-install memcached && docker-php-ext-enable memcached

RUN docker-php-ext-install bcmath

# Install tools and applications through pear. Binaries will be accessible in your PATH.
RUN pear install pear/PHP_CodeSniffer

# Install extensions through pecl and enable them through ini files
RUN pecl install hrtime
RUN echo "extension=hrtime.so" > $PHP_INI_DIR/conf.d/hrtime.ini

# Install Composer and make it available in the PATH
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

# Install mongo extensions through pecl and enable them through ini files
RUN pecl install mongodb
RUN echo "extension=mongodb.so" > $PHP_INI_DIR/conf.d/mongodb.ini

