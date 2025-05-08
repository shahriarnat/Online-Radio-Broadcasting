# Use the official PHP image with FPM
FROM php:8.4-fpm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
	libgettextpo-dev \
	gettext \
    cron \
    supervisor \
    && docker-php-ext-install gettext pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

COPY . .
COPY ./docker/supervisor/supervisord.conf /etc/supervisor/
COPY ./docker/supervisor/supervisor-backend.conf /etc/supervisor/conf.d/

RUN chmod -R 755 ./storage

RUN composer install --optimize-autoloader --no-dev \
    && php artisan octane:install --server=frankenphp --no-interaction \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

RUN service supervisor start

# Expose port 8000,9000 and start PHP-FPM server
EXPOSE 8000 9000

# Start cron and PHP-FPM
CMD /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
