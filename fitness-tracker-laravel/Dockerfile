# Use PHP 8.2 with FPM
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_sqlite mbstring exif pcntl bcmath gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev

# Install Node dependencies and build assets (if needed)
RUN npm install && npm run build || true

# Create .env file from example if it exists, otherwise create a basic one
RUN if [ -f .env.example ]; then \
        cp .env.example .env; \
    else \
        echo "APP_NAME=FitnessTracker" > .env && \
        echo "APP_ENV=production" >> .env && \
        echo "APP_DEBUG=false" >> .env && \
        echo "APP_URL=http://localhost:8000" >> .env && \
        echo "DB_CONNECTION=sqlite" >> .env && \
        echo "DB_DATABASE=/var/www/html/database/database.sqlite" >> .env; \
    fi

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Create SQLite database if it doesn't exist
RUN mkdir -p database && touch database/database.sqlite \
    && chown -R www-data:www-data database

# Copy and set up startup script
COPY run.sh /usr/local/bin/run.sh
RUN chmod +x /usr/local/bin/run.sh

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/run.sh"]
CMD ["php-fpm"]

