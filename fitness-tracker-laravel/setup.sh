#!/bin/bash
# Docker setup script - runs automatically when container starts

set -e

echo "========================================="
echo "  Fitness Tracker - Docker Setup"
echo "========================================="

APP_DIR="/var/www/html"
cd "$APP_DIR"

# Step 1: Create .env if it doesn't exist
echo "[1/6] Checking .env file..."
if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        cp .env.example .env
        echo "       Created .env from .env.example"
    else
        cat > .env << EOF
APP_NAME=FitnessTracker
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite
CACHE_STORE=file
SESSION_DRIVER=file
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:8000,127.0.0.1,127.0.0.1:8000
EOF
        echo "       Created default .env"
    fi
else
    echo "       .env file exists"
fi

# Step 2: Ensure cache and session drivers are set to file
echo "[2/6] Configuring cache and session drivers..."
if ! grep -q "^CACHE_STORE=" .env; then
    echo "CACHE_STORE=file" >> .env
fi
if ! grep -q "^SESSION_DRIVER=" .env; then
    echo "SESSION_DRIVER=file" >> .env
fi
echo "       Done"

# Step 3: Generate app key if not set
echo "[3/6] Checking application key..."
if ! grep -q "^APP_KEY=base64:" .env; then
    php artisan key:generate --force
    echo "       Application key generated!"
else
    echo "       Application key exists"
fi

# Step 4: Ensure database directory and file exist
echo "[4/6] Setting up database..."
mkdir -p "$APP_DIR/database"
touch "$APP_DIR/database/database.sqlite"
chown -R www-data:www-data "$APP_DIR/database"
chmod 664 "$APP_DIR/database/database.sqlite"
echo "       Done"

# Step 5: Ensure storage directories exist and are writable
echo "[5/6] Setting up storage directories..."
mkdir -p "$APP_DIR/storage/logs"
mkdir -p "$APP_DIR/storage/framework/cache"
mkdir -p "$APP_DIR/storage/framework/sessions"
mkdir -p "$APP_DIR/storage/framework/views"
mkdir -p "$APP_DIR/bootstrap/cache"
chown -R www-data:www-data "$APP_DIR/storage"
chown -R www-data:www-data "$APP_DIR/bootstrap/cache"
chmod -R 775 "$APP_DIR/storage"
chmod -R 775 "$APP_DIR/bootstrap/cache"
echo "       Done"

# Step 6: Run migrations
echo "[6/6] Running database migrations..."
php artisan migrate --force 2>/dev/null && echo "       Migrations completed!" || echo "       Migrations skipped (already applied)"

# Clear caches
echo ""
echo "Clearing caches..."
php artisan config:clear || true
php artisan view:clear || true

echo ""
echo "========================================="
echo "  Docker Setup Complete!"
echo "  Starting PHP-FPM..."
echo "========================================="
echo ""

# Execute the main process (php-fpm)
exec "$@"

