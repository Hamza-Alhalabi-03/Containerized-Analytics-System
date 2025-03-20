FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    git \
    zip \
    unzip

# Install and enable MongoDB extension
RUN pecl install mongodb \
    && echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/mongodb.ini \
    && docker-php-ext-enable mongodb

# Verify the MongoDB extension is installed
RUN php -m | grep -q mongodb

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configure Apache
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY composer.json ./

# Install dependencies
RUN composer install --no-scripts --no-autoloader

# Copy PHP files
COPY . .

# Generate optimized autoloader
RUN composer dump-autoload --optimize

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Create a test.php file to verify MongoDB extension
RUN echo "<?php phpinfo(); ?>" > /var/www/html/phpinfo.php

# Expose port 80
EXPOSE 80