# Fitness Tracker REST API Documentation

## Why This REST API Was Built

### The Problem
Fitness enthusiasts and athletes need a reliable way to track their workout progress, monitor body weight changes, and analyze their performance over time. Currently, many fitness tracking solutions are:
- **Expensive** - Requiring monthly subscriptions or premium features
- **Complex** - Overwhelming users with unnecessary features
- **Privacy-Concerning** - Storing sensitive health data on third-party servers
- **Platform-Locked** - Tied to specific mobile apps or ecosystems

### The Solution
This REST API provides a **simple, self-hosted, and privacy-focused** fitness tracking solution that allows users to:
1. **Own their data** - All workout and weight data is stored on the user's own server
2. **Track workouts systematically** - Create exercises, log sessions with sets/reps/weights, and view historical data
3. **Monitor body weight trends** - Track daily weight measurements with notes
4. **Access data anywhere** - RESTful architecture allows integration with any frontend (web, mobile, desktop)
5. **Maintain privacy** - No third-party data sharing or tracking
6. **Customize freely** - Open-source design allows modifications to fit specific needs

This API solves the core problem of **personal fitness data management** by providing a lightweight, flexible backend that prioritizes user control and privacy while maintaining simplicity and ease of use.

---

## API Endpoints Overview

**Total Page Count:** 11 distinct API endpoints

---

## Authentication Endpoints (4 APIs)

### 1. User Registration
- **Endpoint:** `POST /api/register.php`
- **Purpose:** Create a new user account with email and password
- **Authentication Required:** No
- **Request Body:**
  ```json
  {
    "email": "user@example.com",
    "password": "securePassword123"
  }
  ```
- **Response:**
  ```json
  {
    "message": "User registered successfully",
    "token": "abc123...",
    "user": {
      "id": 1,
      "email": "user@example.com"
    }
  }
  ```
- **What It Does:** 
  - Validates email format and password length (minimum 6 characters)
  - Checks for duplicate email addresses
  - Hashes password using bcrypt for security
  - Creates user account in database
  - Generates authentication token
  - Returns token for immediate login

---

### 2. User Login
- **Endpoint:** `POST /api/login.php`
- **Purpose:** Authenticate existing user and generate access token
- **Authentication Required:** No
- **Request Body:**
  ```json
  {
    "email": "user@example.com",
    "password": "securePassword123"
  }
  ```
- **Response:**
  ```json
  {
    "message": "Login successful",
    "token": "xyz789...",
    "user": {
      "id": 1,
      "email": "user@example.com"
    }
  }
  ```
- **What It Does:**
  - Validates credentials against database
  - Verifies password using bcrypt comparison
  - Generates new authentication token with 7-day expiration
  - Stores token in database for session management
  - Returns token for subsequent API requests

---

### 3. User Logout
- **Endpoint:** `POST /api/logout.php`
- **Purpose:** Invalidate current authentication token
- **Authentication Required:** Yes (Bearer token)
- **Headers:**
  ```
  Authorization: Bearer {token}
  ```
- **Response:**
  ```json
  {
    "message": "Logged out successfully"
  }
  ```
- **What It Does:**
  - Extracts token from Authorization header
  - Removes token from database
  - Invalidates current session
  - Forces user to login again for future requests

---

### 4. Verify Token
- **Endpoint:** `GET /api/verify.php`
- **Purpose:** Validate authentication token and retrieve user information
- **Authentication Required:** Yes (Bearer token)
- **Headers:**
  ```
  Authorization: Bearer {token}
  ```
- **Response:**
  ```json
  {
    "valid": true,
    "user": {
      "id": 1,
      "email": "user@example.com"
    }
  }
  ```
- **What It Does:**
  - Checks if token exists in database
  - Verifies token hasn't expired
  - Returns user information if valid
  - Returns 401 error if invalid or expired

---

## Workout Management Endpoints (3 APIs)

### 5. List All Workouts
- **Endpoint:** `GET /api/workouts.php`
- **Purpose:** Retrieve all workout types for authenticated user
- **Authentication Required:** Yes (Bearer token)
- **Response:**
  ```json
  {
    "workouts": [
      {
        "id": 1,
        "name": "Bench Press",
        "description": "Chest exercise",
        "created_at": "2025-10-14 10:30:00",
        "updated_at": "2025-10-14 10:30:00"
      },
      {
        "id": 2,
        "name": "Squats",
        "description": "Leg exercise",
        "created_at": "2025-10-15 09:00:00",
        "updated_at": "2025-10-15 09:00:00"
      }
    ]
  }
  ```
- **What It Does:**
  - Fetches all workout templates created by user
  - Returns workouts sorted alphabetically by name
  - Shows metadata including creation and update timestamps
  - Provides foundation for creating workout sessions

---

### 6. Create Workout
- **Endpoint:** `POST /api/workouts.php`
- **Purpose:** Create a new workout type/template
- **Authentication Required:** Yes (Bearer token)
- **Request Body:**
  ```json
  {
    "name": "Deadlift",
    "description": "Back and leg compound exercise"
  }
  ```
- **Response:**
  ```json
  {
    "message": "Workout created successfully",
    "workout": {
      "id": 3,
      "name": "Deadlift",
      "description": "Back and leg compound exercise"
    }
  }
  ```
- **What It Does:**
  - Validates workout name is provided
  - Creates new workout template in database
  - Associates workout with authenticated user
  - Returns created workout with generated ID
  - Allows users to define custom exercises

---

### 7. Delete Workout
- **Endpoint:** `DELETE /api/workouts.php?id=1`
- **Purpose:** Remove workout and all associated sessions
- **Authentication Required:** Yes (Bearer token)
- **Query Parameters:**
  - `id` - Workout ID to delete
- **Response:**
  ```json
  {
    "message": "Workout deleted successfully"
  }
  ```
- **What It Does:**
  - Verifies user owns the workout
  - Cascades deletion to all associated sessions and sets
  - Removes workout from database
  - Returns error if workout not found or unauthorized
  - Cleans up all related data automatically

---

## Workout Session Endpoints (3 APIs)

### 8. List Workout Sessions
- **Endpoint:** `GET /api/sessions.php`
- **Purpose:** Retrieve all workout sessions for user
- **Authentication Required:** Yes (Bearer token)
- **Optional Query Parameters:**
  - `workout_id` - Filter sessions by specific workout
  - `id` - Get single session with detailed set information
- **Response (List):**
  ```json
  {
    "sessions": [
      {
        "id": 1,
        "workout_id": 1,
        "workout_name": "Bench Press",
        "session_date": "2025-10-14",
        "notes": "Great workout!",
        "total_sets": 3,
        "total_reps": 24
      }
    ]
  }
  ```
- **Response (Single Session):**
  ```json
  {
    "id": 1,
    "workout_id": 1,
    "workout_name": "Bench Press",
    "session_date": "2025-10-14",
    "notes": "Great workout!",
    "sets": [
      {
        "id": 1,
        "set_number": 1,
        "reps": 10,
        "weight": 135
      },
      {
        "id": 2,
        "set_number": 2,
        "reps": 8,
        "weight": 145
      }
    ]
  }
  ```
- **What It Does:**
  - Lists all workout sessions with summary statistics
  - Filters by workout type if specified
  - Returns detailed set information for single session
  - Orders sessions by date (newest first)
  - Provides workout history overview

---

### 9. Create Workout Session
- **Endpoint:** `POST /api/sessions.php`
- **Purpose:** Log a new workout session with sets, reps, and weights
- **Authentication Required:** Yes (Bearer token)
- **Request Body:**
  ```json
  {
    "workout_id": 1,
    "session_date": "2025-10-18",
    "notes": "Felt strong today",
    "sets": [
      {
        "set_number": 1,
        "reps": 10,
        "weight": 135
      },
      {
        "set_number": 2,
        "reps": 8,
        "weight": 145
      },
      {
        "set_number": 3,
        "reps": 6,
        "weight": 155
      }
    ]
  }
  ```
- **Response:**
  ```json
  {
    "message": "Session created successfully",
    "session": {
      "id": 5,
      "workout_id": 1,
      "session_date": "2025-10-18",
      "notes": "Felt strong today"
    }
  }
  ```
- **What It Does:**
  - Creates workout session record
  - Logs multiple sets with reps and weight
  - Associates session with specific workout type
  - Tracks date for historical analysis
  - Allows optional notes for context
  - Uses database transaction for data integrity

---

### 10. Delete Workout Session
- **Endpoint:** `DELETE /api/sessions.php?id=1`
- **Purpose:** Remove a workout session and all its sets
- **Authentication Required:** Yes (Bearer token)
- **Query Parameters:**
  - `id` - Session ID to delete
- **Response:**
  ```json
  {
    "message": "Session deleted successfully"
  }
  ```
- **What It Does:**
  - Verifies user owns the session
  - Cascades deletion to all sets in session
  - Removes session from database
  - Returns error if session not found
  - Maintains data integrity

---

## Weight Tracking Endpoints (3 APIs)

### 11. List Weight Entries
- **Endpoint:** `GET /api/weight.php`
- **Purpose:** Retrieve body weight measurements
- **Authentication Required:** Yes (Bearer token)
- **Optional Query Parameters:**
  - `start_date` - Filter entries from this date
  - `end_date` - Filter entries until this date
  - `id` - Get single weight entry
- **Response:**
  ```json
  {
    "weights": [
      {
        "id": 1,
        "weight": 185.5,
        "weight_date": "2025-10-18",
        "notes": "Morning weight after breakfast",
        "created_at": "2025-10-18 08:30:00",
        "updated_at": "2025-10-18 08:30:00"
      },
      {
        "id": 2,
        "weight": 184.2,
        "weight_date": "2025-10-17",
        "notes": "",
        "created_at": "2025-10-17 07:45:00",
        "updated_at": "2025-10-17 07:45:00"
      }
    ]
  }
  ```
- **What It Does:**
  - Lists all weight measurements for user
  - Filters by date range if specified
  - Orders by date (newest first)
  - Includes optional notes field
  - Tracks weight trends over time

---

### 12. Add Weight Entry
- **Endpoint:** `POST /api/weight.php`
- **Purpose:** Record new body weight measurement
- **Authentication Required:** Yes (Bearer token)
- **Request Body:**
  ```json
  {
    "weight": 185.5,
    "weight_date": "2025-10-18",
    "notes": "Morning weight"
  }
  ```
- **Response:**
  ```json
  {
    "message": "Weight entry added successfully",
    "weight": {
      "id": 3,
      "weight": 185.5,
      "weight_date": "2025-10-18",
      "notes": "Morning weight"
    }
  }
  ```
- **What It Does:**
  - Validates weight is positive number
  - Enforces one weight entry per date (unique constraint)
  - Stores weight with date and optional notes
  - Returns error if duplicate date exists
  - Allows weight trend tracking

---

### 13. Delete Weight Entry
- **Endpoint:** `DELETE /api/weight.php?id=1`
- **Purpose:** Remove a weight measurement
- **Authentication Required:** Yes (Bearer token)
- **Query Parameters:**
  - `id` - Weight entry ID to delete
- **Response:**
  ```json
  {
    "message": "Weight entry deleted successfully"
  }
  ```
- **What It Does:**
  - Verifies user owns the weight entry
  - Removes weight record from database
  - Returns error if entry not found
  - Allows correction of erroneous entries

---

## API Summary

| # | Endpoint | Method | Purpose | Auth Required |
|---|----------|--------|---------|---------------|
| 1 | `/api/register.php` | POST | Create new user account | No |
| 2 | `/api/login.php` | POST | Authenticate and get token | No |
| 3 | `/api/logout.php` | POST | Invalidate session token | Yes |
| 4 | `/api/verify.php` | GET | Validate token | Yes |
| 5 | `/api/workouts.php` | GET | List all workouts | Yes |
| 6 | `/api/workouts.php` | POST | Create workout template | Yes |
| 7 | `/api/workouts.php?id={id}` | DELETE | Delete workout | Yes |
| 8 | `/api/sessions.php` | GET | List workout sessions | Yes |
| 9 | `/api/sessions.php` | POST | Create workout session | Yes |
| 10 | `/api/sessions.php?id={id}` | DELETE | Delete session | Yes |
| 11 | `/api/weight.php` | GET | List weight entries | Yes |
| 12 | `/api/weight.php` | POST | Add weight entry | Yes |
| 13 | `/api/weight.php?id={id}` | DELETE | Delete weight entry | Yes |

**Total API Count: 13 endpoints** (exceeds minimum requirement of 10)

---

## Security Features

All authenticated endpoints (9 out of 13) include:
- **Token-based authentication** - Bearer token in Authorization header
- **User isolation** - Users can only access their own data
- **SQL injection protection** - Prepared statements with PDO
- **Password hashing** - Bcrypt encryption for passwords
- **Token expiration** - 7-day automatic expiration
- **CORS support** - Configurable cross-origin headers

---

## Error Handling

All endpoints return appropriate HTTP status codes:
- `200 OK` - Successful GET, PUT, DELETE
- `201 Created` - Successful POST
- `400 Bad Request` - Invalid input
- `401 Unauthorized` - Missing or invalid token
- `404 Not Found` - Resource doesn't exist
- `409 Conflict` - Duplicate entry (email, date)
- `500 Internal Server Error` - Server/database error

---

## Conclusion

This REST API provides a **complete fitness tracking solution** with 13 distinct endpoints covering user authentication, workout management, session logging, and weight tracking. The API emphasizes user privacy, data ownership, and simplicity while maintaining professional security standards and RESTful design principles.