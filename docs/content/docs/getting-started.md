---
title: "Getting Started"
date: 2025-12-03
draft: false
weight: 1
---

# Getting Started

This guide will help you set up and run the Fitness Tracker application.

## Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js and npm (optional, for frontend assets)
- Docker (optional, for containerized deployment)

## Installation

### Option 1: Local Development

1. **Clone the repository**
   ```bash
   git clone https://github.com/Pbierley/FitnessTracker.git
   cd FitnessTracker/fitness-tracker-laravel
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Run the setup script**
   ```bash
   bash run.sh
   ```

   This will:
   - Create `.env` file from `.env.example`
   - Generate application key
   - Run database migrations
   - Start the development server

4. **Access the application**
   
   Open http://localhost:8000 in your browser.

### Option 2: Docker

1. **Clone the repository**
   ```bash
   git clone https://github.com/Pbierley/FitnessTracker.git
   cd FitnessTracker/fitness-tracker-laravel
   ```

2. **Build and run containers**
   ```bash
   docker compose up --build
   ```

3. **Access the application**
   
   Open http://localhost:8000 in your browser.

## First Steps

1. **Register an account** - Click "Create Account" and enter your email and password
2. **Create a workout** - Add workout types like "Bench Press" or "Squats"
3. **Log a session** - Record your sets, reps, and weights
4. **Track your weight** - Add body weight entries to track progress

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

