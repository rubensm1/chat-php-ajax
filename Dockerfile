FROM php:8.1-apache

# Install PDO MySQL extension and other dependencies
RUN apt-get update && apt-get install -y \
    wget \
    && docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Set permissions
RUN chmod 644 /var/www/html/sounds/message.mp3
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80

# Download sound file and start Apache
CMD ["/bin/bash", "-c", "apache2-foreground"]
