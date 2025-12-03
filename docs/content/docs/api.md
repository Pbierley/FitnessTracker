---
title: "API Reference"
date: 2025-12-03
draft: false
weight: 2
---

# API Reference

Complete documentation for the Fitness Tracker REST API.

## Base URL

```
http://localhost:8000/api
```

## Authentication

The API uses Laravel Sanctum for token-based authentication. Include the token in the `Authorization` header:

```
Authorization: Bearer {your-token}
```

---

## Auth Endpoints

### Register

Create a new user account.

**Request:**
```http
POST /api/auth/register
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "message": "User registered successfully",
  "token": "1|abc123...",
  "user": {
    "id": 1,
    "email": "user@example.com"
  }
}
```

### Login

Authenticate an existing user.

**Request:**
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "message": "Login successful",
  "token": "2|xyz789...",
  "user": {
    "id": 1,
    "email": "user@example.com"
  }
}
```

### Verify Token

Check if the current token is valid.

**Request:**
```http
GET /api/auth/verify
Authorization: Bearer {token}
```

**Response:**
```json
{
  "valid": true,
  "user": {
    "id": 1,
    "email": "user@example.com"
  }
}
```

### Logout

Revoke the current token.

**Request:**
```http
POST /api/auth/logout
Authorization: Bearer {token}
```

**Response:**
```json
{
  "message": "Logged out successfully"
}
```

---

## Workout Endpoints

### List Workouts

Get all workouts for the authenticated user.

**Request:**
```http
GET /api/workouts
Authorization: Bearer {token}
```

**Response:**
```json
{
  "workouts": [
    {
      "id": 1,
      "name": "Bench Press",
      "description": "Chest exercise",
      "created_at": "2025-12-03T10:00:00.000000Z"
    }
  ]
}
```

### Create Workout

**Request:**
```http
POST /api/workouts
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Bench Press",
  "description": "Chest exercise"
}
```

**Response:**
```json
{
  "message": "Workout created successfully",
  "workout": {
    "id": 1,
    "name": "Bench Press",
    "description": "Chest exercise"
  }
}
```

### Get Workout

**Request:**
```http
GET /api/workouts/{id}
Authorization: Bearer {token}
```

### Update Workout

**Request:**
```http
PUT /api/workouts/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Incline Bench Press",
  "description": "Upper chest exercise"
}
```

### Delete Workout

**Request:**
```http
DELETE /api/workouts/{id}
Authorization: Bearer {token}
```

---

## Session Endpoints

### List Sessions

Get all workout sessions.

**Request:**
```http
GET /api/sessions
Authorization: Bearer {token}
```

**Response:**
```json
{
  "sessions": [
    {
      "id": 1,
      "workout_id": 1,
      "session_date": "2025-12-03",
      "notes": "Good workout",
      "workout_name": "Bench Press",
      "total_sets": 3,
      "total_reps": 30,
      "sets": [
        {"set_number": 1, "reps": 10, "weight": "135.00"},
        {"set_number": 2, "reps": 10, "weight": "145.00"},
        {"set_number": 3, "reps": 10, "weight": "155.00"}
      ]
    }
  ]
}
```

### Create Session

**Request:**
```http
POST /api/sessions
Authorization: Bearer {token}
Content-Type: application/json

{
  "workout_id": 1,
  "session_date": "2025-12-03",
  "notes": "Good workout",
  "sets": [
    {"set_number": 1, "reps": 10, "weight": 135},
    {"set_number": 2, "reps": 10, "weight": 145},
    {"set_number": 3, "reps": 10, "weight": 155}
  ]
}
```

### Update Session

**Request:**
```http
PUT /api/sessions/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "session_date": "2025-12-03",
  "notes": "Updated notes",
  "sets": [...]
}
```

### Delete Session

**Request:**
```http
DELETE /api/sessions/{id}
Authorization: Bearer {token}
```

---

## Weight Tracking Endpoints

### List Weight Entries

**Request:**
```http
GET /api/weight
Authorization: Bearer {token}
```

**Response:**
```json
{
  "weights": [
    {
      "id": 1,
      "weight": "185.50",
      "weight_date": "2025-12-03",
      "notes": "Morning weight"
    }
  ]
}
```

### Add Weight Entry

**Request:**
```http
POST /api/weight
Authorization: Bearer {token}
Content-Type: application/json

{
  "weight": 185.5,
  "weight_date": "2025-12-03",
  "notes": "Morning weight"
}
```

### Update Weight Entry

**Request:**
```http
PUT /api/weight/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "weight": 184.0,
  "notes": "Updated"
}
```

### Delete Weight Entry

**Request:**
```http
DELETE /api/weight/{id}
Authorization: Bearer {token}
```

---

## Error Responses

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 404 Not Found
```json
{
  "message": "No query results for model [App\\Models\\Workout] 999"
}
```

### 422 Validation Error
```json
{
  "message": "The email field is required.",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

