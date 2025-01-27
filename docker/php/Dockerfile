FROM php:8.4-fpm

WORKDIR /var/www/html

RUN chown -R www-data:www-data /var/www/html
RUN apt-get update && apt-get install -y git \
                                         zip \
                                         curl \
                                         wget \
                                         unzip \
                                         libicu-dev \
                                         libbz2-dev \
                                         libpng-dev \
                                         libjpeg-dev \
                                         libmcrypt-dev \
                                         libreadline-dev \
                                         libfreetype6-dev \
                                         nano

RUN pecl install xdebug && docker-php-ext-enable xdebug
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN docker-php-ext-install pdo pdo_mysql exif

EXPOSE 9000
CMD ["php-fpm"]