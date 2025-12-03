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
│   ├── register.php       # POST /api/register
│   ├── login.php          # POST /api/login
│   ├── logout.php         # POST /api/logout
│   ├── verify.php         # GET /api/verify
│   ├── workouts.php       # GET/POST/PUT/DELETE /api/workouts
│   ├── sessions.php       # GET/POST/PUT/DELETE /api/sessions
│   └── weight.php         # GET/POST/PUT/DELETE /api/weight
└── README.md
```

## Installation

### 1. MySQL Server Setup

#### Starting the MySQL Server
```bash
# Start MySQL service
sudo service mysql start
```

#### Check MySQL Status
```bash
# Verify MySQL is running
sudo service mysql status
```

#### Stopping the MySQL Server (Don't do this until you're done)
```bash
# Stop MySQL service
sudo systemctl stop mysql
```

#### Connect to Database
```bash
# Connect to MySQL as root user to test it's working
mysql -h 127.0.0.1 -P 3306 -u root -p
# Enter password: 123456
```

### 2. Database Setup

#### Create Database and Import Schema
```bash
# Import the database schema (this will create the database and all tables)
mysql -u root -p fitness_tracker < schema.sql
# Enter password when prompted: 123456
```

Or run manually:
```bash
# Connect to MySQL
mysql -u root -p

# Create database
CREATE DATABASE fitness_tracker;

# Exit and import schema
exit
mysql -u root -p fitness_tracker < schema.sql
```

### 3. Troubleshooting Database Connection

#### Having issues connecting to the database with a SQL tool like DBeaver?

If you're running MySQL in WSL and want to connect from Windows tools like DBeaver:

**Step 1: Change bind address**
```bash
# Edit MySQL configuration
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf

# Find the line:
# bind-address = 127.0.0.1

# Change it to:
# bind-address = 0.0.0.0

# Save and exit (Ctrl+X, then Y, then Enter)
```

**Step 2: Restart MySQL**
```bash
sudo service mysql restart
```

**Step 3: Update user permissions**
```bash
# Connect to MySQL
mysql -u root -p

# Run these commands:
ALTER USER 'root'@'%' IDENTIFIED WITH mysql_native_password BY '123456';
FLUSH PRIVILEGES;
exit
```

**Step 4: Configure DBeaver/Other SQL Tools**
- Host: `localhost` or `127.0.0.1` (or your WSL IP if connecting from Windows)
- Port: `3306`
- Database: `fitness_tracker`
- Username: `root`
- Password: `123456`

### 4. PHP Configuration

Edit `config.php`:

```php
define('DB_HOST', 'localhost');  // or '127.0.0.1'
define('DB_USER', 'root');        // your MySQL username
define('DB_PASS', '123456');      // your MySQL password
define('DB_NAME', 'fitness_tracker');
define('JWT_SECRET', 'your-random-secret-key-change-this');  // CHANGE THIS!
```

**Important:** Change `JWT_SECRET` to a random string for security!

### 5. Web Server Setup

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

### 6. Test the Installation

1. Open `index.html` in your browser
2. Register a new account
3. Create a workout
4. Add a session
5. Track your weight

## API Documentation

### Authentication

All endpoints follow REST conventions with dedicated routes for each action.

#### Register
```
POST /api/register.php
Body: {"email": "user@example.com", "password": "password123"}
Response: {"token": "...", "user": {...}}
```

#### Login
```
POST /api/login.php
Body: {"email": "user@example.com", "password": "password123"}
Response: {"token": "...", "user": {...}}
```

#### Logout
```
POST /api/logout.php
Headers: Authorization: Bearer {token}
Response: {"message": "Logged out successfully"}
```

#### Verify Token
```
GET /api/verify.php
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

#### Get Single Workout
```
GET /api/workouts.php?id=1
Headers: Authorization: Bearer {token}
Response: {"id": 1, "name": "Bench Press", ...}
```

#### Create Workout
```
POST /api/workouts.php
Headers: Authorization: Bearer {token}
Body: {"name": "Bench Press", "description": "Chest exercise"}
Response: {"message": "...", "workout": {...}}
```

#### Update Workout
```
PUT /api/workouts.php
Headers: Authorization: Bearer {token}
Body: {"id": 1, "name": "Updated Name", "description": "Updated description"}
Response: {"message": "Workout updated successfully"}
```

#### Delete Workout
```
DELETE /api/workouts.php?id=1
Headers: Authorization: Bearer {token}
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

#### Get Single Session
```
GET /api/sessions.php?id=1
Headers: Authorization: Bearer {token}
Response: {"id": 1, "workout_id": 1, "sets": [...], ...}
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
DELETE /api/sessions.php?id=1
Headers: Authorization: Bearer {token}
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

#### Get Single Weight Entry
```
GET /api/weight.php?id=1
Headers: Authorization: Bearer {token}
Response: {"id": 1, "weight": 185.5, ...}
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
DELETE /api/weight.php?id=1
Headers: Authorization: Bearer {token}
Response: {"message": "Weight entry deleted successfully"}
```

## Security Features

- **Password Hashing:** Uses bcrypt with PHP's `password_hash()`
- **SQL Injection Protection:** Prepared statements with PDO
- **Token Authentication:** Secure token-based auth with expiration
- **CORS Headers:** Configurable cross-origin support
- **Input Validation:** Server-side validation for all inputs
- **Authorization:** User-specific data access controls

## Database Schema

### Tables
- `users` - User accounts
- `auth_tokens` - Authentication tokens
- `workouts` - Workout types (exercises)
- `workout_sessions` - Individual workout instances
- `session_sets` - Sets within sessions (reps/weight)
- `weight_tracking` - Body weight entries

## Troubleshooting

### MySQL Connection Issues

**MySQL service won't start**
```bash
# Check MySQL error logs
sudo tail -f /var/log/mysql/error.log

# Check if MySQL is already running
sudo service mysql status

# If port 3306 is already in use
sudo netstat -tulpn | grep 3306
```

**Can't connect from PHP**
```bash
# Test connection manually
mysql -h 127.0.0.1 -P 3306 -u root -p

# Check if MySQL is listening on the correct interface
sudo netstat -tulpn | grep mysql

# Verify user permissions
mysql -u root -p
SELECT user, host FROM mysql.user;
```

**WSL to Windows Connection Issues**
```bash
# Get your WSL IP address
ip addr show eth0 | grep inet

# Use this IP in DBeaver instead of localhost
# Example: 172.20.10.5
```

### Common Issues

**Database Connection Fails**
- Verify MySQL credentials in `config.php`
- Check MySQL service is running
- Ensure database `fitness_tracker` exists

**CORS Errors**
- Check `Access-Control-Allow-Origin` header in config.php
- Update to match your domain

**Token Expired**
- Login again to get new token
- Adjust `TOKEN_EXPIRATION` in config.php (default: 7 days)

**API Returns 500 Error**
- Check PHP error logs
- Enable error display: `ini_set('display_errors', 1);`
- Verify file permissions

## Future Enhancements

- Password reset functionality
- Email verification
- Profile picture uploads
- Exercise categories