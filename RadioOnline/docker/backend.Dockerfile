# Use the official PHP image with FPM
FROM php:8.2-fpm

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
    cron \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Add user for Laravel
RUN groupadd -g 1000 www && useradd -u 1000 -ms /bin/bash -g www www

# Change ownership of the working directory
RUN chown -R www:www /var/www/html

# Switch to the new user
USER www

# Expose port 9000 and start PHP-FPM server
EXPOSE 9000

# Add Laravel scheduler to cron
RUN echo "* * * * * www php /var/www/html/artisan schedule:run >> /dev/null 2>&1" | crontab -u www -

# Start cron and PHP-FPM
CMD ["sh", "-c", "cron && php-fpm"]
