FROM php:8.4-fpm

WORKDIR /var/www/html

ARG user
ARG uid
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user && chown -R $user:$uid /home/$user && chown -R $user:$uid /var/www/html//
COPY --chown=$user:$uid . /var/www/html/

RUN apt-get update && apt-get install -y git \
                                         zip \
                                         curl \
                                         wget \
                                         unzip \
                                         libicu-dev \
                                         libbz2-dev \
                                         libpng-dev \
                                         libonig-dev \
                                         libjpeg-dev \
                                         libmcrypt-dev \
                                         libreadline-dev \
                                         libfreetype6-dev \
                                         nano

RUN pecl install xdebug && docker-php-ext-enable xdebug
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN docker-php-ext-install pdo pdo_mysql exif mbstring

USER $user
EXPOSE 9000
CMD ["php-fpm"]