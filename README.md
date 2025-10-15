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

## API Documentation

### Authentication

#### Register
```
POST /api/auth.php
Body: {"action": "register", "email": "user@example.com", "password": "password123"}
Response: {"token": "...", "user": {...}}
```

#### Login
```
POST /api/auth.php
Body: {"action": "login", "email": "user@example.com", "password": "password123"}
Response: {"token": "...", "user": {...}}
```

#### Verify Token
```
GET /api/auth.php
Headers: Authorization: Bearer {token}
Response: {"valid": true, "user": {...}}
```

### Workouts

#### List Workouts
```
GET /api/workouts.php
Headers: Authorization: Bearer {token}
Response: {"workouts": [...]}
```

#### Create Workout
```
POST /api/workouts.php
Headers: Authorization: Bearer {token}
Body: {"name": "Bench Press", "description": "Chest exercise"}
Response: {"message": "...", "workout": {...}}
```

#### Delete Workout
```
DELETE /api/workouts.php
Headers: Authorization: Bearer {token}
Body: {"id": 1}
Response: {"message": "Workout deleted successfully"}
```

### Sessions

#### List Sessions
```
GET /api/sessions.php
GET /api/sessions.php?workout_id=1  (filter by workout)
Headers: Authorization: Bearer {token}
Response: {"sessions": [...]}
```

#### Create Session
```
POST /api/sessions.php
Headers: Authorization: Bearer {token}
Body: {
  "workout_id": 1,
  "session_date": "2025-10-14",
  "notes": "Good workout",
  "sets": [
    {"set_number": 1, "reps": 10, "weight": 135},
    {"set_number": 2, "reps": 8, "weight": 155}
  ]
}
Response: {"message": "...", "session": {...}}
```

#### Update Session
```
PUT /api/sessions.php
Headers: Authorization: Bearer {token}
Body: {"id": 1, "session_date": "2025-10-15", "notes": "Updated", "sets": [...]}
Response: {"message": "Session updated successfully"}
```

#### Delete Session
```
DELETE /api/sessions.php
Headers: Authorization: Bearer {token}
Body: {"id": 1}
Response: {"message": "Session deleted successfully"}
```

### Weight Tracking

#### List Weight Entries
```
GET /api/weight.php
GET /api/weight.php?start_date=2025-10-01&end_date=2025-10-31
Headers: Authorization: Bearer {token}
Response: {"weights": [...]}
```

#### Add Weight
```
POST /api/weight.php
Headers: Authorization: Bearer {token}
Body: {"weight": 185.5, "weight_date": "2025-10-14", "notes": "Morning weight"}
Response: {"message": "...", "weight": {...}}
```

#### Update Weight
```
PUT /api/weight.php
Headers: Authorization: Bearer {token}
Body: {"id": 1, "weight": 184.0, "notes": "Updated"}
Response: {"message": "Weight entry updated successfully"}
```

#### Delete Weight
```
DELETE /api/weight.php
Headers: Authorization: Bearer {token}
Body: {"id": 1}
Response: {"message": "Weight entry deleted successfully"}
```

## Security Features

- **Password Hashing:** Uses bcrypt with PHP's `password_hash()`
- **SQL Injection Protection:** Prepared statements with PDO
- **Token Authentication:** Secure token-based auth with expiration
- **CORS Headers:** Configurable cross-origin support
- **Input Validation:** Server-side validation for all inputs
- **Authorization:** User-specific dat