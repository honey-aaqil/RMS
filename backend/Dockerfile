FROM php:8.2-apache

# Enable Apache mod_rewrite for routing
RUN a2enmod rewrite

# Install necessary PHP extensions manually (PDO for database)
RUN apt-get update && apt-get install -y \
    unzip \
    && docker-php-ext-install pdo pdo_mysql mysqli

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory to the default Apache web root
WORKDIR /var/www/html

# Copy all backend files into the container
COPY . .

# Install PHP dependencies via Composer
RUN composer install --no-dev --optimize-autoloader

# Ensure correct permissions
RUN chown -R www-data:www-data /var/www/html

# Expose the standard web port
EXPOSE 80
