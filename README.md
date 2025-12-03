# Fitness Tracker

A fitness tracking web application built with Laravel and SQLite.

## Features

- **User Authentication** - Register, login, and secure token-based auth
- **Workout Management** - Create and manage workout types
- **Session Tracking** - Log workout sessions with sets, reps, and weights
- **Weight Tracking** - Track body weight over time

## Quick Start

### Local Development

```bash
cd fitness-tracker-laravel
bash run.sh
```

Access at: **http://localhost:8000**

### Docker

```bash
cd fitness-tracker-laravel
docker compose up --build
```

Access at: **http://localhost:8000**

## Project Structure

```
fitness-tracker-laravel/
├── app/
│   ├── Http/Controllers/Api/    # API Controllers
│   └── Models/                  # Eloquent Models
├── database/
│   ├── migrations/              # Database migrations
│   └── database.sqlite          # SQLite database
├── public/
│   └── index.html               # Frontend application
├── routes/
│   └── api.php                  # API routes
├── run.sh                       # Local development script
├── setup.sh                     # Docker setup script
├── Dockerfile
└── docker-compose.yml
```

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/register` | Register new user |
| POST | `/api/auth/login` | Login user |
| GET | `/api/auth/verify` | Verify token |
| GET | `/api/workouts` | List workouts |
| POST | `/api/workouts` | Create workout |
| DELETE | `/api/workouts/{id}` | Delete workout |
| GET | `/api/sessions` | List sessions |
| POST | `/api/sessions` | Create session |
| DELETE | `/api/sessions/{id}` | Delete session |
| GET | `/api/weight` | List weight entries |
| POST | `/api/weight` | Add weight entry |
| DELETE | `/api/weight/{id}` | Delete weight entry |

## Documentation Site (Hugo)

This project uses Hugo for documentation.

### Setup Hugo

```bash
# Install Hugo (macOS)
brew install hugo

# Install Hugo (Windows)
choco install hugo-extended

# Install Hugo (Linux)
sudo apt install hugo
```

### Create Documentation

```bash
# Initialize Hugo site in docs folder
hugo new site docs
cd docs

# Add a theme
git submodule add https://github.com/theNewDynamic/gohugo-theme-ananke themes/ananke
echo "theme = 'ananke'" >> hugo.toml

# Create content
hugo new content posts/getting-started.md

# Run development server
hugo server -D
```

### Build for GitHub Pages

```bash
cd docs
hugo --minify
```

The built site will be in `docs/public/`.

### GitHub Pages Deployment

Add `.github/workflows/hugo.yml`:

```yaml
name: Deploy Hugo site to Pages

on:
  push:
    branches: ["main"]
  workflow_dispatch:

permissions:
  contents: read
  pages: write
  id-token: write

concurrency:
  group: "pages"
  cancel-in-progress: false

defaults:
  run:
    shell: bash

jobs:
  build:
    runs-on: ubuntu-latest
    env:
      HUGO_VERSION: 0.128.0
    steps:
      - name: Install Hugo CLI
        run: |
          wget -O ${{ runner.temp }}/hugo.deb https://github.com/gohugoio/hugo/releases/download/v${HUGO_VERSION}/hugo_extended_${HUGO_VERSION}_linux-amd64.deb \
          && sudo dpkg -i ${{ runner.temp }}/hugo.deb
      - name: Checkout
        uses: actions/checkout@v4
        with:
          submodules: recursive
      - name: Setup Pages
        id: pages
        uses: actions/configure-pages@v5
      - name: Build with Hugo
        env:
          HUGO_CACHEDIR: ${{ runner.temp }}/hugo_cache
          HUGO_ENVIRONMENT: production
        run: |
          cd docs
          hugo --minify --baseURL "${{ steps.pages.outputs.base_url }}/"
      - name: Upload artifact
        uses: actions/upload-pages-artifact@v3
        with:
          path: ./docs/public

  deploy:
    environment:
      name: github-pages
      url: ${{ steps.deployment.outputs.page_url }}
    runs-on: ubuntu-latest
    needs: build
    steps:
      - name: Deploy to GitHub Pages
        id: deployment
        uses: actions/deploy-pages@v4
```

## Tech Stack

- **Backend**: Laravel 12, PHP 8.2
- **Database**: SQLite
- **Frontend**: Vanilla JavaScript
- **Containerization**: Docker
- **Documentation**: Hugo

## License

MIT
