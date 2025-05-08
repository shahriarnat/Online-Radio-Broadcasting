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
COPY ./docker/supervisor/supervisor-backend.conf /etc/supervisor/conf.d/

RUN composer install --optimize-autoloader --no-dev \
    && php artisan octane:install --no-interaction \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && service supervisor start

# Expose port 8000,9000 and start PHP-FPM server
EXPOSE 8000 9000

# Add Laravel scheduler to cron
RUN echo "* * * * * www php /var/www/html/artisan schedule:run >> /dev/null 2>&1" | crontab -u root -

# Start cron and PHP-FPM
ENTRYPOINT ["supervisorctl", "start", "all"]
