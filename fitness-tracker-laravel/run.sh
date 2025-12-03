#!/bin/bash
# Local development script - run this to start the app locally

echo "========================================="
echo "  Fitness Tracker - Local Development"
echo "========================================="

# Get the directory where this script is located
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR" || exit 1

echo "Working directory: $SCRIPT_DIR"
echo ""

# Check if .env exists
if [ ! -f .env ]; then
    echo "[Setup] Creating .env from .env.example..."
    if [ -f .env.example ]; then
        cp .env.example .env
    else
        echo "ERROR: No .env.example found!"
        exit 1
    fi
fi

# Check if vendor directory exists
if [ ! -d "vendor" ]; then
    echo "[Setup] Installing dependencies..."
    composer install
fi

# Generate key if needed
if ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
    echo "[Setup] Generating application key..."
    php artisan key:generate
fi

# Run migrations
echo "[Setup] Running migrations..."
php artisan migrate --force 2>&1 | grep -E "(Migrating|Migrated|Nothing|INFO)" || true

# Clear caches
echo "[Setup] Clearing caches..."
php artisan config:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true

echo ""
echo "========================================="
echo "  Starting server at http://127.0.0.1:8000"
echo "  Press Ctrl+C to stop"
echo "========================================="
echo ""

php artisan serve
