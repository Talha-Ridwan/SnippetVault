# Use PHP 8.2 (Matches your modern stack requirement)
FROM php:8.4-fpm

# 1. Install system dependencies required for Laravel and image processing
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# 2. Clear cache to keep the image small
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# 3. Install PHP extensions (MySQL, MBString, etc.)
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 4. Install Redis Extension (CRITICAL for your PDD requirements)
RUN pecl install redis && docker-php-ext-enable redis

# 5. Install Composer (The PHP Package Manager)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Expose Port 8000
EXPOSE 8000

# Start Laravel's development server
# We bind to 0.0.0.0 so the Nginx container can see it
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

