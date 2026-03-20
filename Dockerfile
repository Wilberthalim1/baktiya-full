FROM php:8.3-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    curl zip unzip git libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring xml ctype fileinfo zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy composer files first for layer caching
COPY composer.json composer.lock ./
RUN composer install --ignore-platform-reqs --no-dev --optimize-autoloader --no-scripts

# Copy rest of application
COPY . .

# Run composer scripts after full copy
RUN composer dump-autoload --optimize

# Set permissions
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8080

CMD php artisan migrate --force && php artisan db:seed --force && php -S 0.0.0.0:$PORT -t public
