FROM php:8.4-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    supervisor \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy Supervisor config
COPY supervisor.conf /etc/supervisor/conf.d/supervisord.conf

# Fix PHP-FPM listen address
RUN sed -i 's/listen = 127.0.0.1:9000/listen = 9000/g' /usr/local/etc/php-fpm.d/www.conf

# Expose port and start supervisor
EXPOSE 9000
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
