# Fitness Tracker REST API
## Complete Backend Solution for Workout & Weight Management

**Presented by:** Philip Bierley 
**API Name:** Fitness Tracker API  
**Total Pages:** 18  
**Topics Covered:** ✅ PHP | ✅ MySQL | ✅ REST APIs | ✅ Security | ✅ Access Methods

---

## Slide 1: Introduction

### What is the Fitness Tracker API?

A **self-hosted REST API** built with PHP and MySQL that provides complete fitness tracking functionality:

- 🔐 **User Authentication** - Secure registration and login
- 💪 **Workout Management** - Create and organize exercise templates
- 📊 **Session Tracking** - Log workouts with sets, reps, and weights
- ⚖️ **Weight Monitoring** - Track body weight over time

### Why This API?

**Problem:** Existing fitness apps are expensive, privacy-invasive, and platform-locked.

**Solution:** A lightweight, open-source API that gives users complete control over their fitness data while maintaining professional security standards.

---

## Slide 2: API Overview

### Architecture

```
┌─────────────┐
│  Frontend   │ ← HTML/JS/Mobile App
│ (Any Client)│
└──────┬──────┘
       │ HTTP/JSON
       ▼
┌─────────────┐
│  REST API   │ ← PHP (13 Endpoints)
│   Layer     │
└──────┬──────┘
       │ PDO
       ▼
┌─────────────┐
│   MySQL     │ ← 6 Tables
│  Database   │
└─────────────┘
```

### Key Features

- **13 RESTful Endpoints** following HTTP standards
- **Token-based Authentication** with 7-day expiration
- **User Data Isolation** - Users only access their own data
- **CORS Support** - Enable cross-origin requests
- **Comprehensive Error Handling** - Proper HTTP status codes

---

## Slide 3: Technology Stack

### Backend Technologies

| Technology | Purpose | Version |
|------------|---------|---------|
| **PHP** | Server-side scripting | 7.4+ |
| **MySQL** | Relational database | 8.0+ |
| **PDO** | Database abstraction | Built-in |
| **Bcrypt** | Password hashing | Built-in |

### Why These Technologies?

✅ **PHP** - Widely supported, easy deployment, mature ecosystem  
✅ **MySQL** - Reliable, ACID-compliant, excellent performance  
✅ **PDO** - Prepared statements prevent SQL injection  
✅ **Bcrypt** - Industry-standard password encryption

---

## TOPIC 1: PHP Implementation

## Slide 4: PHP Architecture

### File Structure

```
fitness-tracker/
├── config.php           # Database config & helper functions
├── api/
│   ├── register.php     # POST - User registration
│   ├── login.php        # POST - User authentication
│   ├── logout.php       # POST - Session termination
│   ├── verify.php       # GET - Token validation
│   ├── workouts.php     # CRUD - Workout management
│   ├── sessions.php     # CRUD - Workout sessions
│   └── weight.php       # CRUD - Weight tracking
└── index.html           # Frontend demo
```

### Design Principles

1. **Separation of Concerns** - Each endpoint is a separate file
2. **Code Reusability** - Shared functions in `config.php`
3. **RESTful Routes** - Dedicated URLs for each action
4. **Stateless Design** - No session dependencies

---

## Slide 5: PHP Core Functions

### Database Connection (config.php)

```php
function getDBConnection() {
    try {
        $conn = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $conn;
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database connection failed']);
        exit();
    }
}
```

**Key Features:**
- Exception mode for error handling
- Associative array fetching
- Real prepared statements (not emulated)

---

## Slide 6: PHP Authentication Logic

### Token Generation & Validation

```php
// Generate secure token
function generateToken() {
    return bin2hex(random_bytes(32)); // 64-char hex string
}

// Verify token from Authorization header
function verifyToken($token) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("
        SELECT at.user_id, u.email 
        FROM auth_tokens at
        JOIN users u ON at.user_id = u.id
        WHERE at.token = ? AND at.expires_at > NOW()
    ");
    $stmt->execute([$token]);
    return $stmt->fetch();
}

// Extract user from request
function getUserFromAuth() {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';
    
    if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
        return verifyToken($matches[1]);
    }
    return false;
}
```

---

## Slide 7: PHP Request Handling

### Example: Create Workout (POST /api/workouts.php)

```php
<?php
require_once '../config.php';

setJSONHeaders();

// Authenticate user
$user = getUserFromAuth();
if (!$user) {
    sendResponse(['error' => 'Unauthorized'], 401);
}

$method = $_SERVER['REQUEST_METHOD'];

// Handle POST request
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = $data['name'] ?? '';
    $description = $data['description'] ?? '';
    
    // Validate input
    if (empty($name)) {
        sendResponse(['error' => 'Workout name is required'], 400);
    }
    
    // Insert into database
    $conn = getDBConnection();
    $stmt = $conn->prepare("
        INSERT INTO workouts (user_id, name, description) 
        VALUES (?, ?, ?)
    ");
    
    try {
        $stmt->execute([$user['user_id'], $name, $description]);
        $workoutId = $conn->lastInsertId();
        
        sendResponse([
            'message' => 'Workout created successfully',
            'workout' => [
                'id' => $workoutId,
                'name' => $name,
                'description' => $description
            ]
        ], 201);
    } catch (PDOException $e) {
        sendResponse(['error' => 'Failed to create workout'], 500);
    }
}
?>
```

---

## TOPIC 2: MySQL Database

## Slide 8: Database Schema Overview

### 6 Interconnected Tables

```sql
users (id, email, password_hash)
  ├─► auth_tokens (token, user_id, expires_at)
  ├─► workouts (name, description, user_id)
  │     └─► workout_sessions (workout_id, date, notes)
  │           └─► session_sets (reps, weight, set_number)
  └─► weight_tracking (weight, date, user_id)
```

### Relationships

- **One-to-Many:** users → workouts, sessions, weights
- **Cascade Deletes:** Removing user deletes all related data
- **Foreign Keys:** Enforce referential integrity

---

## Slide 9: Key Tables Explained

### Users Table

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**Purpose:** Store user accounts with hashed passwords

---

### Auth Tokens Table

```sql
CREATE TABLE auth_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    expires_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

**Purpose:** Manage authentication sessions

---

## Slide 10: Session & Set Tables

### Workout Sessions

```sql
CREATE TABLE workout_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    workout_id INT NOT NULL,
    user_id INT NOT NULL,
    session_date DATE NOT NULL,
    notes TEXT,
    FOREIGN KEY (workout_id) REFERENCES workouts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### Session Sets

```sql
CREATE TABLE session_sets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    set_number INT NOT NULL,
    reps INT NOT NULL,
    weight DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (session_id) REFERENCES workout_sessions(id) ON DELETE CASCADE
);
```

**Design Benefit:** One session can have multiple sets with different reps/weights

---

## Slide 11: Database Features

### Data Integrity

✅ **Foreign Keys** - Enforce relationships between tables  
✅ **Unique Constraints** - Prevent duplicate emails and tokens  
✅ **NOT NULL Constraints** - Ensure critical data is present  
✅ **Cascade Deletes** - Automatic cleanup of related records

### Performance Optimizations

✅ **Indexes on Foreign Keys** - Fast joins and lookups  
✅ **Unique Index on Token** - Instant authentication checks  
✅ **Date Indexes** - Efficient date-range queries  
✅ **Composite Index** - `(user_id, weight_date)` for weight tracking

### Example Query with Indexes

```sql
-- Efficiently retrieves sessions with set counts
SELECT ws.*, w.name, COUNT(ss.id) as total_sets
FROM workout_sessions ws
JOIN workouts w ON ws.workout_id = w.id  -- Uses index
LEFT JOIN session_sets ss ON ws.id = ss.session_id  -- Uses index
WHERE ws.user_id = ?  -- Uses index
GROUP BY ws.id
ORDER BY ws.session_date DESC;  -- Uses index
```

---

## TOPIC 3: REST API Design

## Slide 12: RESTful Principles

### What is REST?

**RE**presentational **S**tate **T**ransfer - An architectural style for web services

### Core Principles Applied

1. **Resource-Based URLs**
   ```
   /api/workouts     (not /api/getWorkouts)
   /api/sessions     (not /api/workout_session_manager)
   /api/weight       (not /api/weightTracking)
   ```

2. **HTTP Methods Define Actions**
   - `GET` - Retrieve data
   - `POST` - Create new resource
   - `PUT` - Update existing resource
   - `DELETE` - Remove resource

3. **Stateless Communication**
   - Each request contains all needed information
   - No server-side session storage
   - Token passed in every authenticated request

4. **JSON Data Format**
   - Structured, human-readable
   - Universal browser/language support

---

## Slide 13: API Endpoints Summary

### All 13 Endpoints

| # | Endpoint | Method | Purpose | Auth |
|---|----------|--------|---------|------|
| 1 | `/api/register.php` | POST | Create account | ✅|
| 2 | `/api/login.php` | POST | Get auth token | ✅ |
| 3 | `/api/logout.php` | POST | Invalidate token | ✅ |
| 4 | `/api/verify.php` | GET | Check token | ✅ |
| 5 | `/api/workouts.php` | GET | List workouts | ✅ |
| 6 | `/api/workouts.php` | POST | Create workout | ✅ |
| 7 | `/api/workouts.php?id=X` | DELETE | Delete workout | ✅ |
| 8 | `/api/sessions.php` | GET | List sessions | ✅ |
| 9 | `/api/sessions.php` | POST | Log workout | ✅ |
| 10 | `/api/sessions.php?id=X` | DELETE | Delete session | ✅ |
| 11 | `/api/weight.php` | GET | List weights | ✅ |
| 12 | `/api/weight.php` | POST | Add weight | ✅ |
| 13 | `/api/weight.php?id=X` | DELETE | Delete weight | ✅ |

---

## Slide 14: HTTP Status Codes

### Proper Status Code Usage

| Code | Meaning | When Used |
|------|---------|-----------|
| **200 OK** | Success | GET, PUT, DELETE succeeded |
| **201 Created** | Resource created | POST created new record |
| **400 Bad Request** | Invalid input | Missing required fields |
| **401 Unauthorized** | Auth failed | Invalid/missing token |
| **404 Not Found** | Resource missing | Requested ID doesn't exist |
| **409 Conflict** | Duplicate data | Email/date already exists |
| **500 Server Error** | Server failure | Database/PHP error |

### Example Responses

```json
// 201 Created
{
  "message": "Workout created successfully",
  "workout": { "id": 5, "name": "Squats" }
}

// 400 Bad Request
{
  "error": "Workout name is required"
}

// 401 Unauthorized
{
  "error": "Unauthorized"
}
```

---

## TOPIC 4: Security Implementation

## Slide 15: Security Features

### 1. Password Security

```php
// Registration - Hash password with bcrypt
$passwordHash = password_hash($password, PASSWORD_BCRYPT);

// Login - Verify hashed password
if (password_verify($password, $user['password_hash'])) {
    // Password correct
}
```

**Benefits:**
- Bcrypt includes salt automatically
- Computationally expensive (prevents brute force)
- Industry-standard algorithm

---

### 2. SQL Injection Prevention

```php
// ❌ DANGEROUS - Direct SQL injection vulnerability
$sql = "SELECT * FROM users WHERE email = '$email'";

// ✅ SAFE - Prepared statements with PDO
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
```

**All queries use prepared statements** - 100% SQL injection protection

---

## Slide 16: Security (Continued)

### 3. Token-Based Authentication

```php
// Token generation uses cryptographically secure randomness
function generateToken() {
    return bin2hex(random_bytes(32)); // 64 hex characters
}

// Token stored with expiration
$expiresAt = date('Y-m-d H:i:s', time() + TOKEN_EXPIRATION);
```

**Security Features:**
- 64-character random tokens (2^256 possibilities)
- 7-day automatic expiration
- Tokens stored in database (can be revoked)
- Logout immediately invalidates token

---

### 4. User Data Isolation

```php
// Every query filters by authenticated user ID
$stmt = $conn->prepare("
    SELECT * FROM workouts 
    WHERE id = ? AND user_id = ?  -- Double check ownership
");
$stmt->execute([$workoutId, $user['user_id']]);
```

**Result:** Users can ONLY access their own data

---

## Slide 17: Security Best Practices

### Additional Security Measures

1. **CORS Configuration**
   ```php
   header('Access-Control-Allow-Origin: *'); // Configure for production
   header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
   header('Access-Control-Allow-Headers: Content-Type, Authorization');
   ```

2. **Input Validation**
   ```php
   // Email validation
   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
       sendResponse(['error' => 'Invalid email format'], 400);
   }
   
   // Password strength
   if (strlen($password) < 6) {
       sendResponse(['error' => 'Password too short'], 400);
   }
   ```

3. **Error Handling**
   ```php
   try {
       // Database operations
   } catch (PDOException $e) {
       // Don't expose internal errors to client
       sendResponse(['error' => 'Operation failed'], 500);
   }
   ```

---

## TOPIC 5: Access Methods

## Slide 18: Using the API with curl

### Register New User

```bash
curl -X POST http://localhost:8000/api/register.php \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "secure123"
  }'
```

**Response:**
```json
{
  "message": "User registered successfully",
  "token": "a3f8d9e2b1c4567890abcdef...",
  "user": {
    "id": 1,
    "email": "user@example.com"
  }
}
```

---

### Login

```bash
curl -X POST http://localhost:8000/api/login.php \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "secure123"
  }'
```

---

## Slide 19: curl - Authenticated Requests

### Create Workout

```bash
TOKEN="a3f8d9e2b1c4567890abcdef..."

curl -X POST http://localhost:8000/api/workouts.php \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "name": "Bench Press",
    "description": "Chest exercise"
  }'
```

### Get All Workouts

```bash
curl -X GET http://localhost:8000/api/workouts.php \
  -H "Authorization: Bearer $TOKEN"
```

### Delete Workout

```bash
curl -X DELETE "http://localhost:8000/api/workouts.php?id=1" \
  -H "Authorization: Bearer $TOKEN"
```

---

## Slide 20: curl - Session Logging

### Log Complete Workout Session

```bash
curl -X POST http://localhost:8000/api/sessions.php \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "workout_id": 1,
    "session_date": "2025-10-18",
    "notes": "Great workout!",
    "sets": [
      {"set_number": 1, "reps": 10, "weight": 135},
      {"set_number": 2, "reps": 8, "weight": 145},
      {"set_number": 3, "reps": 6, "weight": 155}
    ]
  }'
```

**Response:**
```json
{
  "message": "Session created successfully",
  "session": {
    "id": 5,
    "workout_id": 1,
    "session_date": "2025-10-18"
  }
}
```

---

## Slide 21: HTML/JavaScript Access

### Basic HTML Form

```html
<form id="loginForm">
  <input type="email" id="email" placeholder="Email" required>
  <input type="password" id="password" placeholder="Password" required>
  <button type="submit">Login</button>
</form>

<script>
document.getElementById('loginForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  
  const email = document.getElementById('email').value;
  const password = document.getElementById('password').value;
  
  const response = await fetch('/api/login.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email, password })
  });
  
  const data = await response.json();
  
  if (response.status === 200) {
    localStorage.setItem('authToken', data.token);
    alert('Login successful!');
  } else {
    alert('Error: ' + data.error);
  }
});
</script>
```

---

## Slide 22: JavaScript Fetch API

### Reusable API Call Function

```javascript
async function apiCall(endpoint, method = 'GET', data = null) {
  const token = localStorage.getItem('authToken');
  
  const options = {
    method: method,
    headers: {
      'Content-Type': 'application/json',
      'Authorization': token ? `Bearer ${token}` : ''
    }
  };
  
  if (data && method !== 'GET') {
    options.body = JSON.stringify(data);
  }
  
  const response = await fetch(`/api/${endpoint}`, options);
  const result = await response.json();
  
  if (!response.ok) {
    throw new Error(result.error || 'Request failed');
  }
  
  return result;
}

// Usage Examples
await apiCall('workouts.php', 'GET');
await apiCall('workouts.php', 'POST', { name: 'Squats' });
await apiCall('workouts.php?id=1', 'DELETE');
```

---

## Slide 23: JavaScript - Full Workout Flow

### Complete Example: Create and Log Workout

```javascript
async function logWorkout() {
  try {
    // Step 1: Create workout template
    const workout = await apiCall('workouts.php', 'POST', {
      name: 'Deadlift',
      description: 'Back and leg exercise'
    });
    
    console.log('Workout created:', workout.workout.id);
    
    // Step 2: Log workout session
    const session = await apiCall('sessions.php', 'POST', {
      workout_id: workout.workout.id,
      session_date: '2025-10-18',
      notes: 'First time doing deadlifts',
      sets: [
        { set_number: 1, reps: 5, weight: 135 },
        { set_number: 2, reps: 5, weight: 185 },
        { set_number: 3, reps: 3, weight: 225 }
      ]
    });
    
    console.log('Session logged:', session.session.id);
    
    // Step 3: Retrieve session history
    const sessions = await apiCall('sessions.php', 'GET');
    console.log('All sessions:', sessions.sessions);
    
  } catch (error) {
    console.error('Error:', error.message);
  }
}

logWorkout();
```

---

## Slide 24: Deployment & Setup

### Installation Steps

1. **Start MySQL Server**
   ```bash
   sudo service mysql start
   ```

2. **Create Database**
   ```bash
   mysql -u root -p fitness_tracker < schema.sql
   ```

3. **Configure PHP**
   ```php
   // config.php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '123456');
   define('DB_NAME', 'fitness_tracker');
   define('JWT_SECRET', 'your-random-secret-key');
   ```

4. **Start PHP Server**
   ```bash
   php -S localhost:8000
   ```

5. **Test API**
   ```bash
   curl http://localhost:8000/api/verify.php
   ```

---

## Slide 25: Testing Checklist

### Verify Each Component

✅ **Database**
- [ ] MySQL running
- [ ] Database created
- [ ] All 6 tables exist
- [ ] Can connect with credentials

✅ **PHP**
- [ ] Web server running
- [ ] config.php configured
- [ ] No syntax errors in API files

✅ **Authentication**
- [ ] Can register new user
- [ ] Can login and receive token
- [ ] Token validates correctly
- [ ] Logout invalidates token

✅ **API Functionality**
- [ ] Can create workouts
- [ ] Can log sessions with sets
- [ ] Can track weight
- [ ] Can delete records

---

## Slide 26: Common Issues & Solutions

### Troubleshooting

| Issue | Solution |
|-------|----------|
| **Can't connect to MySQL** | Check service status: `sudo service mysql status` |
| **401 Unauthorized** | Verify token in Authorization header |
| **Database connection failed** | Check credentials in config.php |
| **CORS errors** | Enable CORS headers in setJSONHeaders() |
| **Token expired** | Login again to get new token |
| **404 errors** | Verify API endpoint URLs |

### Debug Tips

```php
// Enable error display (development only)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Log queries
$stmt->debugDumpParams();
```

---

## Slide 27: API Best Practices

### For API Consumers

1. **Store Token Securely**
   ```javascript
   // ✅ Good - Use localStorage
   localStorage.setItem('authToken', token);
   
   // ❌ Bad - Don't expose in URL
   fetch('/api/workouts.php?token=' + token)
   ```

2. **Handle Errors Gracefully**
   ```javascript
   try {
     const data = await apiCall('workouts.php');
   } catch (error) {
     // Show user-friendly message
     alert('Failed to load workouts. Please try again.');
   }
   ```

3. **Validate Input Before Sending**
   ```javascript
   if (weight <= 0 || !weight) {
     alert('Please enter valid weight');
     return;
   }
   ```

4. **Use Proper HTTP Methods**
   - GET for reading data
   - POST for creating
   - PUT for updating
   - DELETE for removing

---

## Slide 28: Future Enhancements

### Potential Features

🔄 **V2.0 Features**
- Password reset via email
- Profile picture uploads
- Exercise categories and tags
- Personal records tracking
- Progress charts and analytics

📊 **Advanced Analytics**
- Strength progression graphs
- Weight trend visualization
- Workout frequency stats
- Volume calculations (sets × reps × weight)

🔒 **Enhanced Security**
- Two-factor authentication
- API rate limiting
- IP-based access control
- OAuth2 integration

🌐 **Scalability**
- Redis caching layer
- Database replication
- Load balancing support
- Microservices architecture

---

## Slide 29: Resources & Documentation

### Complete Documentation

📄 **Documentation Files**
- `README.md` - Setup instructions and overview
- `apis.md` - All 13 endpoints with examples
- `mysql.md` - Database schema and relationships
- `4_example.md` - curl and JavaScript examples

### Quick Links

- 📊 **Database Schema**: See `mysql.md` for full structure
- 🔐 **Security Details**: Review `apis.md` security section
- 💻 **Code Examples**: Check `4_example.md` for all curl commands
- 🚀 **Getting Started**: Follow `README.md` installation steps

### Testing Tools

- **Postman** - GUI for API testing
- **curl** - Command-line HTTP client
- **DBeaver** - MySQL database management
- **Browser DevTools** - Network tab for debugging

---

## Slide 30: Summary & Contact

### What We Covered

✅ **PHP** - Modern server-side architecture with PDO  
✅ **MySQL** - 6-table relational database with proper indexing  
✅ **REST APIs** - 13 endpoints following RESTful principles  
✅ **Security** - Bcrypt, prepared statements, token auth, user isolation  
✅ **Access Methods** - curl commands, HTML forms, JavaScript fetch

### Key Takeaways

1. **Self-Hosted Solution** - Complete control over fitness data
2. **Production-Ready Security** - Industry-standard practices
3. **Developer-Friendly** - Clear API design, comprehensive docs
4. **Scalable Architecture** - Easily extensible for new features
5. **Open Source** - Free to use and modify

### Questions?

**Contact:** john.doe@example.com  
**GitHub:** github.com/johndoe/fitness-tracker  
**Documentation:** See `presentation/` folder

---

**Thank you for your attention!**

*This API is ready for production use with proper security configuration.*