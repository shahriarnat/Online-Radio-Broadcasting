FROM php:8.4-fpm

RUN umask 022

USER root

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    libgettextpo-dev gettext cron supervisor \
    && docker-php-ext-install gettext pdo_mysql mbstring exif pcntl bcmath gd zip

#COPY --from=composer:2.8.9 /usr/bin/composer /usr/bin/composer
# Install Composer manually
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Now copy the full source
COPY . /var/www/html

# Copy configs
COPY ./docker/php/custom.conf /usr/local/etc/php/conf.d/custom.conf
COPY ./docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
COPY ./docker/supervisor/supervisor-backend.conf /etc/supervisor/conf.d/supervisor-backend.conf

# Set permissions
RUN chmod -R 755 ./storage

EXPOSE 8000 9000

# Laravel setup
CMD composer update --optimize-autoloader --no-dev \
&& php artisan octane:install --server=frankenphp --no-interaction \
&& yes | php artisan key:generate \
&& php artisan config:clear \
&& php artisan optimize:clear \
&& /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
