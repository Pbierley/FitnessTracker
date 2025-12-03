---
title: "Docker Setup"
date: 2025-12-03
draft: false
weight: 3
---

# Docker Setup

Run Fitness Tracker using Docker for easy deployment and consistent environments.

## Prerequisites

- Docker Desktop installed
- Docker Compose (included with Docker Desktop)

## Quick Start

```bash
cd fitness-tracker-laravel
docker compose up --build
```

Access the application at **http://localhost:8000**

## Docker Files

The project includes these Docker configuration files:

### Dockerfile

```dockerfile
FROM php:8.2-fpm

WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev \
    zip unzip sqlite3 libsqlite3-dev nodejs npm

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_sqlite mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .
RUN composer install --optimize-autoloader --no-dev

EXPOSE 9000
ENTRYPOINT ["/usr/local/bin/setup.sh"]
CMD ["php-fpm"]
```

### docker-compose.yml

```yaml
version: '3.8'

services:
  app:
    build: .
    container_name: fitness-tracker-app
    volumes:
      - ./:/var/www/html
    environment:
      - APP_ENV=local
      - DB_CONNECTION=sqlite
      - CACHE_STORE=file
      - SESSION_DRIVER=file

  nginx:
    image: nginx:alpine
    container_name: fitness-tracker-nginx
    ports:
      - "8000:80"
    volumes:
      - ./:/var/www/html
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
    depends_on:
      - app
```

## Commands

### Start containers
```bash
docker compose up
```

### Start in background
```bash
docker compose up -d
```

### Rebuild containers
```bash
docker compose up --build
```

### Stop containers
```bash
docker compose down
```

### View logs
```bash
docker compose logs -f
```

### Access app container
```bash
docker compose exec app bash
```

### Run artisan commands
```bash
docker compose exec app php artisan migrate
docker compose exec app php artisan tinker
```

## Environment Variables

The following environment variables can be configured in `docker-compose.yml`:

| Variable | Description | Default |
|----------|-------------|---------|
| `APP_ENV` | Application environment | `local` |
| `APP_DEBUG` | Enable debug mode | `true` |
| `APP_URL` | Application URL | `http://localhost:8000` |
| `DB_CONNECTION` | Database driver | `sqlite` |
| `CACHE_STORE` | Cache driver | `file` |
| `SESSION_DRIVER` | Session driver | `file` |

## Troubleshooting

### Port already in use
```bash
# Change the port in docker-compose.yml
ports:
  - "8080:80"  # Use port 8080 instead
```

### Permission errors
```bash
# Fix storage permissions
docker compose exec app chmod -R 775 storage bootstrap/cache
```

### Database issues
```bash
# Reset database
docker compose exec app rm database/database.sqlite
docker compose exec app touch database/database.sqlite
docker compose exec app php artisan migrate
```

### 419 Page Expired error
This is a CSRF issue. Make sure `SANCTUM_STATEFUL_DOMAINS` includes your domain:
```yaml
environment:
  - SANCTUM_STATEFUL_DOMAINS=localhost,localhost:8000
```

