# Fitness Tracker - PHP API & SQL Database

A complete fitness tracking web application with PHP REST APIs and MySQL database.

## Features

- **User Authentication**
  - Register with email/password
  - Login with JWT-style token authentication
  - Secure password hashing (bcrypt)
  - Cookie-based session management

- **Workout Management**
  - Create workouts (e.g., Bench Press, Squats)
  - View all workouts
  - Delete workouts

- **Workout Sessions**
  - Create workout sessions with date tracking
  - Add multiple sets with reps and weights
  - Edit existing sessions
  - Delete sessions
  - View session history

- **Weight Tracking**
  - Add body weight entries by date
  - Edit weight entries
  - Delete weight entries
  - Track weight over time

## Project Structure

```
fitness-tracker/
├── index.html              # Frontend application
├── config.php              # Database configuration
├── api/
│   ├── auth.php           # Authentication endpoints
│   ├── workouts.php       # Workout management
│   ├── sessions.php       # Session tracking
│   └── weight.php         # Weight tracking
└── README.md
```

## Installation

### 1. Database Setup

```bash
# Import the database schema
mysql -u your_username -p < database_schema.sql
```

Or run the SQL directly:
- Create the `fitness_tracker` database
- Run all CREATE TABLE statements from the schema file

### 2. Configuration

Edit `config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'fitness_tracker');
define('JWT_SECRET', 'your-random-secret-key');
```

**Important:** Change `JWT_SECRET` to a random string for security!

### 3. Web Server Setup

#### Option A: Apache
Place files in your web root directory (e.g., `/var/www/html/fitness-tracker/`)

Ensure `.htaccess` exists in the `api` directory:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ $1.php [L]
```

#### Option B: PHP Built-in Server (Development Only)
```bash
php -S localhost:8000
```

Access at: `http://localhost:8000`

### 4. Test the Installation

1. Open `index.html` in your browser
2. Register a new account
3. Create a workout
4. Add a session
5. Track your weight


## Security Features

- **Password Hashing:** Uses bcrypt with PHP's `password_hash()`
- **SQL Injection Protection:** Prepared statements with PDO
- **Token Authentication:** Secure token-based auth with expiration
- **CORS Headers:** Configurable cross-origin support
- **Input Validation:** Server-side validation for all inputs
- **Authorization:** User-specific dat