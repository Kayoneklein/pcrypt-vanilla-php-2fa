FROM php:8.2-fpm

# Install PHP extensions
# RUN a2enmod rewrite
RUN docker-php-ext-install pdo pdo_mysql
# RUN sed -i "s/AllowOverride None/AllowOverride All/g" /etc/apache2/apache2.conf
# Set working directory
WORKDIR /var/www/html

# Copy source code
COPY . /var/www/html

EXPOSE 9000
CMD ["php-fpm"]


# FROM php:8.2-apache

# RUN a2enmod rewrite

# RUN docker-php-ext-install pdo pdo_mysql
# RUN sed -i "s/AllowOverride None/AllowOverride All/g" /etc/apache2/apache2.conf

# # Copy code into the container
# COPY . /var/www/html/
