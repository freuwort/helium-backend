# Use PHP with Apache as the base image
FROM php:8.2-apache AS web

# Install Additional System Dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    libmagickwand-dev \
    ffmpeg \
    xpdf \
    poppler-utils \
    inkscape \
    libpq-dev

RUN pecl install imagick;
RUN docker-php-ext-enable imagick;

RUN pecl install redis;
RUN docker-php-ext-enable redis;

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite for URL rewriting
RUN a2enmod rewrite

# Install PHP extensions
RUN docker-php-ext-install zip gd pdo_mysql pdo_pgsql

# Copy custom php.ini
COPY ./php.ini /usr/local/etc/php/


# Configure Apache DocumentRoot to point to Laravel's public directory and update Apache configuration files
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copy the application code
COPY . /var/www/html

# Set the working directory
WORKDIR /var/www/html

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install project dependencies
RUN composer install

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/.scribe
RUN chmod +x /var/www/html/run.sh

ENTRYPOINT ["/var/www/html/run.sh"]