# Dockerfile

# 1) PHP 8.3 + Apache
FROM php:8.3-apache

# 2) System deps + PHP extensions
RUN apt-get update \
  && apt-get install -y libzip-dev zip unzip libpng-dev libonig-dev \
  && docker-php-ext-install pdo pdo_mysql zip mbstring gd \
  && a2enmod rewrite

# 3) Set working directory
WORKDIR /var/www/html

# 4) Copy source code
COPY . /var/www/html

# 5) Ensure storage & bootstrap/cache are writable
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Tell Apache to serve /var/www/html/public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' \
    /etc/apache2/sites-available/000-default.conf

# Allow .htaccess overrides in the new docroot
RUN { \
      echo '<Directory /var/www/html/public>'; \
      echo '    Options Indexes FollowSymLinks'; \
      echo '    AllowOverride All'; \
      echo '    Require all granted'; \
      echo '</Directory>'; \
    } >> /etc/apache2/apache2.conf

# Enable URL rewriting
RUN a2enmod rewrite

