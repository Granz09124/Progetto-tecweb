# Use the official PHP 8.4.11 image with Apache
FROM php:8.4.11-apache

# Set the working directory
WORKDIR /var/www/html

# Copy the project files to the container
COPY .. /var/www/html

RUN docker-php-ext-install mysqli

# Enable mod_rewrite for Apache (useful for dynamic sites)
RUN a2enmod rewrite

# Expose port 80
EXPOSE 80
