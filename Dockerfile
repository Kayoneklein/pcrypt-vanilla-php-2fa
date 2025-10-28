FROM php:8.2-apache

# Install PDO + MySQL extension
RUN docker-php-ext-install pdo pdo_mysql

# Enable mod_rewrite (if needed)
RUN a2enmod rewrite

RUN sed -i "s/AllowOverride None/AllowOverride All/g" /etc/apache2/apache2.conf

# Copy app files
COPY . /var/www/html/
