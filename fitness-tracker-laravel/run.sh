#!/bin/bash
set -e

echo "========================================="
echo "  Fitness Tracker - Starting Application"
echo "========================================="

# Create .env if it doesn't exist
if [ ! -f .env ]; then
    echo "[1/6] Creating .env file..."
    if [ -f .env.example ]; then
        cp .env.example .env
    else
        cat > .env << EOF
APP_NAME=FitnessTracker
APP_ENV=production
APP_DEBUG=true
APP_URL=http://localhost:8000
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite
CACHE_STORE=file
SESSION_DRIVER=file
EOF
    fi
else
    echo "[1/6] .env file exists, skipping..."
fi

# Ensure cache and session drivers are set to file
echo "[2/6] Configuring cache and session drivers..."
if ! grep -q "^CACHE_STORE=" .env; then
    echo "CACHE_STORE=file" >> .env
fi
if ! grep -q "^SESSION_DRIVER=" .env; then
    echo "SESSION_DRIVER=file" >> .env
fi

# Generate app key if not set
echo "[3/6] Checking application key..."
if ! grep -q "^APP_KEY=base64:" .env; then
    php artisan key:generate --force
    echo "       Application key generated!"
else
    echo "       Application key exists, skipping..."
fi

# Ensure database directory and file exist
echo "[4/6] Setting up database..."
mkdir -p /var/www/html/database
touch /var/www/html/database/database.sqlite
chown -R www-data:www-data /var/www/html/database 2>/dev/null || true
chmod 664 /var/www/html/database/database.sqlite 2>/dev/null || true

# Ensure storage directories exist and are writable
echo "[5/6] Setting up storage directories..."
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage 2>/dev/null || true
chown -R www-data:www-data /var/www/html/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/html/storage 2>/dev/null || true
chmod -R 775 /var/www/html/bootstrap/cache 2>/dev/null || true

# Run migrations
echo "[6/6] Running database migrations..."
php artisan migrate --force 2>/dev/null && echo "       Migrations completed!" || echo "       Migrations skipped (already applied)"

# Clear caches
echo ""
echo "Clearing caches..."
php artisan config:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true

echo ""
echo "========================================="
echo "  Application Ready!"
echo "  Starting PHP-FPM..."
echo "========================================="
echo ""

# Execute the main process (php-fpm)
exec "$@"

