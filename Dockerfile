FROM php:7.1-apache
RUN docker-php-ext-install mysqli
RUN apt-get update \
    && apt-get install -y libmemcached-dev zlib1g-dev \
    && pecl install memcached-3.0.4 \
    && docker-php-ext-enable memcached
CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
