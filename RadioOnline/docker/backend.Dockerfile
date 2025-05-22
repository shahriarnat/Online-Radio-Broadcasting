FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    libgettextpo-dev gettext cron supervisor \
    && docker-php-ext-install gettext pdo_mysql mbstring exif pcntl bcmath gd zip

COPY --from=composer:2.8.9 /usr/bin/composer /usr/bin/composer
# Install Composer manually
#RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


WORKDIR /var/www/html

# Now copy the full source
COPY . .

# Copy only composer files first to leverage Docker caching
COPY composer.json composer.lock ./

# Copy configs
COPY ./docker/supervisor/supervisord.conf /etc/supervisor/
COPY ./docker/supervisor/supervisor-backend.conf /etc/supervisor/conf.d/
COPY ./docker/php/custom.conf /usr/local/etc/php/conf.d/

# Set permissions
RUN chmod -R 755 ./storage

# Laravel setup
RUN composer install --optimize-autoloader --no-dev \
    && php artisan octane:install --server=frankenphp --no-interaction \
    && php artisan migrate \
    && php artisan config:clear \
    && php artisan optimize:clear \
    && php artisan route:clear

EXPOSE 8000 9000

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
