# API Examples and Usage Documentation

This document provides complete **curl commands** and **HTML/JavaScript** examples for all 13 API endpoints, including captured outputs with status codes.

---

## Table of Contents
1. [Authentication APIs (4)](#authentication-apis)
2. [Workout Management APIs (3)](#workout-management-apis)
3. [Workout Session APIs (3)](#workout-session-apis)
4. [Weight Tracking APIs (3)](#weight-tracking-apis)

---

## Authentication APIs

### 1. User Registration

#### curl Command
```bash
curl -X POST http://localhost:8000/api/register.php \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john.doe@example.com",
    "password": "secure123"
  }'
```

#### Response (Status: 201 Created)
```json
{
  "message": "User registered successfully",
  "token": "a3f8d9e2b1c4567890abcdef12345678901234567890abcdef1234567890abcd",
  "user": {
    "id": 1,
    "email": "john.doe@example.com"
  }
}
```

#### JavaScript Example
```javascript
async function registerUser() {
  const response = await fetch('http://localhost:8000/api/register.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      email: 'john.doe@example.com',
      password: 'secure123'
    })
  });
  
  const data = await response.json();
  console.log('Status:', response.status); // 201
  console.log('Token:', data.token);
  
  // Store token for future requests
  localStorage.setItem('authToken', data.token);
  return data;
}
```

#### HTML Form Example
```html
<form id="registerForm">
  <input type="email" id="email" placeholder="Email" required>
  <input type="password" id="password" placeholder="Password" required>
  <button type="submit">Register</button>
</form>

<script>
document.getElementById('registerForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  
  const email = document.getElementById('email').value;
  const password = document.getElementById('password').value;
  
  const response = await fetch('/api/register.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email, password })
  });
  
  const result = await response.json();
  
  if (response.status === 201) {
    alert('Registration successful!');
    localStorage.setItem('authToken', result.token);
  } else {
    alert('Error: ' + result.error);
  }
});
</script>
```

#### Error Response (Status: 409 Conflict)
```json
{
  "error": "Email already registered"
}
```

---

### 2. User Login

#### curl Command
```bash
curl -X POST http://localhost:8000/api/login.php \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john.doe@example.com",
    "password": "secure123"
  }'
```

#### Response (Status: 200 OK)
```json
{
  "message": "Login successful",
  "token": "b7e3c5f1a9d2468013579bcdefa2468013579bcdefa2468013579bcdefa24680",
  "user": {
    "id": 1,
    "email": "john.doe@example.com"
  }
}
```

#### JavaScript Example
```javascript
async function loginUser(email, password) {
  try {
    const response = await fetch('http://localhost:8000/api/login.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ email, password })
    });
    
    const data = await response.json();
    
    if (response.status === 200) {
      // Store token in localStorage
      localStorage.setItem('authToken', data.token);
      localStorage.setItem('userEmail', data.user.email);
      console.log('Login successful!');
      return data;
    } else {
      throw new Error(data.error);
    }
  } catch (error) {
    console.error('Login failed:', error.message);
  }
}

// Usage
loginUser('john.doe@example.com', 'secure123');
```

#### Error Response (Status: 401 Unauthorized)
```json
{
  "error": "Invalid email or password"
}
```

---

### 3. User Logout

#### curl Command
```bash
curl -X POST http://localhost:8000/api/logout.php \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer b7e3c5f1a9d2468013579bcdefa2468013579bcdefa2468013579bcdefa24680"
```

#### Response (Status: 200 OK)
```json
{
  "message": "Logged out successfully"
}
```

#### JavaScript Example
```javascript
async function logoutUser() {
  const token = localStorage.getItem('authToken');
  
  const response = await fetch('http://localhost:8000/api/logout.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    }
  });
  
  const data = await response.json();
  console.log('Status:', response.status); // 200
  
  // Clear stored token
  localStorage.removeItem('authToken');
  localStorage.removeItem('userEmail');
  
  console.log('Logged out successfully');
  return data;
}
```

#### Error Response (Status: 401 Unauthorized)
```json
{
  "error": "Unauthorized"
}
```

---

### 4. Verify Token

#### curl Command
```bash
curl -X GET http://localhost:8000/api/verify.php \
  -H "Authorization: Bearer b7e3c5f1a9d2468013579bcdefa2468013579bcdefa2468013579bcdefa24680"
```

#### Response (Status: 200 OK)
```json
{
  "valid": true,
  "user": {
    "id": 1,
    "email": "john.doe@example.com"
  }
}
```

#### JavaScript Example
```javascript
async function verifyToken() {
  const token = localStorage.getItem('authToken');
  
  if (!token) {
    console.log('No token found');
    return false;
  }
  
  const response = await fetch('http://localhost:8000/api/verify.php', {
    method: 'GET',
    headers: {
      'Authorization': `Bearer ${token}`
    }
  });
  
  const data = await response.json();
  
  if (response.status === 200 && data.valid) {
    console.log('Token is valid');
    console.log('User:', data.user.email);
    return true;
  } else {
    console.log('Token is invalid or expired');
    localStorage.removeItem('authToken');
    return false;
  }
}

// Check token on page load
document.addEventListener('DOMContentLoaded', () => {
  verifyToken();
});
```

#### Error Response (Status: 401 Unauthorized)
```json
{
  "error": "Unauthorized"
}
```

---

## Workout Management APIs

### 5. List All Workouts

#### curl Command
```bash
curl -X GET http://localhost:8000/api/workouts.php \
  -H "Authorization: Bearer b7e3c5f1a9d2468013579bcdefa2468013579bcdefa2468013579bcdefa24680"
```

#### Response (Status: 200 OK)
```json
{
  "workouts": [
    {
      "id": 1,
      "name": "Bench Press",
      "description": "Chest compound exercise",
      "created_at": "2025-10-14 10:30:00",
      "updated_at": "2025-10-14 10:30:00"
    },
    {
      "id": 2,
      "name": "Squats",
      "description": "Leg compound exercise",
      "created_at": "2025-10-15 09:00:00",
      "updated_at": "2025-10-15 09:00:00"
    },
    {
      "id": 3,
      "name": "Deadlift",
      "description": "Back and leg exercise",
      "created_at": "2025-10-16 08:45:00",
      "updated_at": "2025-10-16 08:45:00"
    }
  ]
}
```

#### JavaScript Example
```javascript
async function getWorkouts() {
  const token = localStorage.getItem('authToken');
  
  const response = await fetch('http://localhost:8000/api/workouts.php', {
    method: 'GET',
    headers: {
      'Authorization': `Bearer ${token}`
    }
  });
  
  const data = await response.json();
  console.log('Status:', response.status); // 200
  
  // Display workouts in HTML
  const workoutList = document.getElementById('workoutList');
  workoutList.innerHTML = '';
  
  data.workouts.forEach(workout => {
    const div = document.createElement('div');
    div.innerHTML = `
      <h3>${workout.name}</h3>
      <p>${workout.description}</p>
    `;
    workoutList.appendChild(div);
  });
  
  return data.workouts;
}
```

#### Get Single Workout
```bash
curl -X GET "http://localhost:8000/api/workouts.php?id=1" \
  -H "Authorization: Bearer b7e3c5f1a9d2468013579bcdefa2468013579bcdefa2468013579bcdefa24680"
```

#### Single Workout Response (Status: 200 OK)
```json
{
  "id": 1,
  "name": "Bench Press",
  "description": "Chest compound exercise",
  "created_at": "2025-10-14 10:30:00",
  "updated_at": "2025-10-14 10:30:00"
}
```

---

### 6. Create Workout

#### curl Command
```bash
curl -X POST http://localhost:8000/api/workouts.php \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer b7e3c5f1a9d2468013579bcdefa2468013579bcdefa2468013579bcdefa24680" \
  -d '{
    "name": "Pull-ups",
    "description": "Upper back and biceps exercise"
  }'
```

#### Response (Status: 201 Created)
```json
{
  "message": "Workout created successfully",
  "workout": {
    "id": 4,
    "name": "Pull-ups",
    "description": "Upper back and biceps exercise"
  }
}
```

#### JavaScript Example
```javascript
async function createWorkout(name, description) {
  const token = localStorage.getItem('authToken');
  
  const response = await fetch('http://localhost:8000/api/workouts.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    },
    body: JSON.stringify({ name, description })
  });
  
  const data = await response.json();
  console.log('Status:', response.status); // 201
  
  if (response.status === 201) {
    console.log('Workout created:', data.workout);
    alert(`Created: ${data.workout.name}`);
  } else {
    alert('Error: ' + data.error);
  }
  
  return data;
}

// Usage
createWorkout('Pull-ups', 'Upper back and biceps exercise');
```

#### HTML Form Example
```html
<form id="workoutForm">
  <input type="text" id="workoutName" placeholder="Workout Name" required>
  <textarea id="workoutDesc" placeholder="Description"></textarea>
  <button type="submit">Create Workout</button>
</form>

<script>
document.getElementById('workoutForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  
  const name = document.getElementById('workoutName').value;
  const description = document.getElementById('workoutDesc').value;
  const token = localStorage.getItem('authToken');
  
  const response = await fetch('/api/workouts.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    },
    body: JSON.stringify({ name, description })
  });
  
  const result = await response.json();
  
  if (response.status === 201) {
    alert('Workout created!');
    document.getElementById('workoutForm').reset();
  }
});
</script>
```

#### Error Response (Status: 400 Bad Request)
```json
{
  "error": "Workout name is required"
}
```

---

### 7. Delete Workout

#### curl Command
```bash
curl -X DELETE "http://localhost:8000/api/workouts.php?id=4" \
  -H "Authorization: Bearer b7e3c5f1a9d2468013579bcdefa2468013579bcdefa2468013579bcdefa24680"
```

#### Response (Status: 200 OK)
```json
{
  "message": "Workout deleted successfully"
}
```

#### JavaScript Example
```javascript
async function deleteWorkout(workoutId) {
  const token = localStorage.getItem('authToken');
  
  if (!confirm('Delete this workout and all its sessions?')) {
    return;
  }
  
  const response = await fetch(`http://localhost:8000/api/workouts.php?id=${workoutId}`, {
    method: 'DELETE',
    headers: {
      'Authorization': `Bearer ${token}`
    }
  });
  
  const data = await response.json();
  console.log('Status:', response.status); // 200
  
  if (response.status === 200) {
    console.log('Workout deleted successfully');
    // Refresh workout list
    getWorkouts();
  } else {
    alert('Error: ' + data.error);
  }
  
  return data;
}

// Usage
deleteWorkout(4);
```

#### Error Response (Status: 404 Not Found)
```json
{
  "error": "Workout not found"
}
```

---

## Workout Session APIs

### 8. List Workout Sessions

#### curl Command (All Sessions)
```bash
curl -X GET http://localhost:8000/api/sessions.php \
  -H "Authorization: Bearer b7e3c5f1a9d2468013579bcdefa2468013579bcdefa2468013579bcdefa24680"
```

#### Response (Status: 200 OK)
```json
{
  "sessions": [
    {
      "id": 1,
      "workout_id": 1,
      "workout_name": "Bench Press",
      "session_date": "2025-10-18",
      "notes": "Felt strong today",
      "total_sets": 3,
      "total_reps": 24
    },
    {
      "id": 2,
      "workout_id": 2,
      "workout_name": "Squats",
      "session_date": "2025-10-17",
      "notes": "",
      "total_sets": 4,
      "total_reps": 32
    }
  ]
}
```

#### Filter by Workout
```bash
curl -X GET "http://localhost:8000/api/sessions.php?workout_id=1" \
  -H "Authorization: Bearer b7e3c5f1a9d2468013579bcdefa2468013579bcdefa2468013579bcdefa24680"
```

#### Get Single Session with Sets
```bash
curl -X GET "http://localhost:8000/api/sessions.php?id=1" \
  -H "Authorization: Bearer b7e3c5f1a9d2468013579bcdefa2468013579bcdefa2468013579bcdefa24680"
```

#### Single Session Response (Status: 200 OK)
```json
{
  "id": 1,
  "workout_id": 1,
  "workout_name": "Bench Press",
  "session_date": "2025-10-18",
  "notes": "Felt strong today",
  "sets": [
    {
      "id": 1,
      "set_number": 1,
      "reps": 10,
      "weight": 135.00
    },
    {
      "id": 2,
      "set_number": 2,
      "reps": 8,
      "weight": 145.00
    },
    {
      "id": 3,
      "set_number": 3,
      "reps": 6,
      "weight": 155.00
    }
  ]
}
```

#### JavaScript Example
```javascript
async function getSessions(workoutId = null) {
  const token = localStorage.getItem('authToken');
  
  let url = 'http://localhost:8000/api/sessions.php';
  if (workoutId) {
    url += `?workout_id=${workoutId}`;
  }
  
  const response = await fetch(url, {
    method: 'GET',
    headers: {
      'Authorization': `Bearer ${token}`
    }
  });
  
  const data = await response.json();
  console.log('Status:', response.status); // 200
  console.log('Sessions:', data.sessions);
  
  return data.sessions;
}

// Get all sessions
getSessions();

// Get sessions for specific workout
getSessions(1);
```

---

### 9. Create Workout Session

#### curl Command
```bash
curl -X POST http://localhost:8000/api/sessions.php \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer b7e3c5f1a9d2468013579bcdefa2468013579bcdefa2468013579bcdefa24680" \
  -d '{
    "workout_id": 1,
    "session_date": "2025-10-18",
    "notes": "Personal record on last set!",
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
  }'
```

#### Response (Status: 201 Created)
```json
{
  "message": "Session created successfully",
  "session": {
    "id": 3,
    "workout_id": 1,
    "session_date": "2025-10-18",
    "notes": "Personal record on last set!"
  }
}
```

#### JavaScript Example
```javascript
async function createSession(workoutId, sessionDate, notes, sets) {
  const token = localStorage.getItem('authToken');
  
  const response = await fetch('http://localhost:8000/api/sessions.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    },
    body: JSON.stringify({
      workout_id: workoutId,
      session_date: sessionDate,
      notes: notes,
      sets: sets
    })
  });
  
  const data = await response.json();
  console.log('Status:', response.status); // 201
  
  if (response.status === 201) {
    console.log('Session created:', data.session);
    alert('Workout session logged!');
  }
  
  return data;
}

// Usage
const sets = [
  { set_number: 1, reps: 10, weight: 135 },
  { set_number: 2, reps: 8, weight: 145 },
  { set_number: 3, reps: 6, weight: 155 }
];

createSession(1, '2025-10-18', 'Great workout!', sets);
```

#### HTML Form Example
```html
<form id="sessionForm">
  <select id="workoutSelect" required>
    <option value="">Select Workout</option>
    <option value="1">Bench Press</option>
    <option value="2">Squats</option>
  </select>
  
  <input type="date" id="sessionDate" required>
  <textarea id="sessionNotes" placeholder="Session notes"></textarea>
  
  <div id="setsContainer">
    <h4>Sets</h4>
    <div class="set">
      <input type="number" placeholder="Set #" value="1">
      <input type="number" placeholder="Reps" min="1">
      <input type="number" placeholder="Weight" step="0.5">
    </div>
  </div>
  
  <button type="button" onclick="addSet()">Add Set</button>
  <button type="submit">Log Session</button>
</form>

<script>
let setCount = 1;

function addSet() {
  setCount++;
  const setHtml = `
    <div class="set">
      <input type="number" placeholder="Set #" value="${setCount}">
      <input type="number" placeholder="Reps" min="1">
      <input type="number" placeholder="Weight" step="0.5">
    </div>
  `;
  document.getElementById('setsContainer').insertAdjacentHTML('beforeend', setHtml);
}

document.getElementById('sessionForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  
  const workoutId = document.getElementById('workoutSelect').value;
  const sessionDate = document.getElementById('sessionDate').value;
  const notes = document.getElementById('sessionNotes').value;
  
  // Collect all sets
  const sets = [];
  document.querySelectorAll('.set').forEach(setDiv => {
    const inputs = setDiv.querySelectorAll('input');
    sets.push({
      set_number: parseInt(inputs[0].value),
      reps: parseInt(inputs[1].value),
      weight: parseFloat(inputs[2].value)
    });
  });
  
  const token = localStorage.getItem('authToken');
  
  const response = await fetch('/api/sessions.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    },
    body: JSON.stringify({
      workout_id: workoutId,
      session_date: sessionDate,
      notes: notes,
      sets: sets
    })
  });
  
  const result = await response.json();
  
  if (response.status === 201) {
    alert('Session logged successfully!');
    document.getElementById('sessionForm').reset();
  }
});
</script>
```

#### Error Response (Status: 400 Bad Request)
```json
{
  "error": "Workout ID is required"
}
```

---

### 10. Delete Workout Session

#### curl Command
```bash
curl -X DELETE "http://localhost:8000/api/sessions.php?id=3" \
  -H "Authorization: Bearer b7e3c5f1a9d2468013579bcdefa2468013579bcdefa2468013579bcdefa24680"
```

#### Response (Status: 200 OK)
```json
{
  "message": "Session deleted successfully"
}
```

#### JavaScript Example
```javascript
async function deleteSession(sessionId) {
  const token = localStorage.getItem('authToken');
  
  if (!confirm('Delete this workout session?')) {
    return;
  }
  
  const response = await fetch(`http://localhost:8000/api/sessions.php?id=${sessionId}`, {
    method: 'DELETE',
    headers: {
      'Authorization': `Bearer ${token}`
    }
  });
  
  const data = await response.json();
  console.log('Status:', response.status); // 200
  
  if (response.status === 200) {
    console.log('Session deleted');
    getSessions(); // Refresh list
  }
  
  return data;
}

// Usage
deleteSession(3);
```

---

## Weight Tracking APIs

### 11. List Weight Entries

#### curl Command (All Entries)
```bash
curl -X GET http://localhost:8000/api/weight.php \
  -H "Authorization: Bearer b7e3c5f1a9d2468013579bcdefa2468013579bcdefa2468013579bcdefa24680"
```

#### Response (Status: 200 OK)
```json
{
  "weights": [
    {
      "id": 1,
      "weight": 185.5,
      "weight_date": "2025-10-18",
      "notes": "Morning weight",
      "created_at": "2025-10-18 07:30:00",
      "updated_at": "2025-10-18 07:30:00"
    },
    {
      "id": 2,
      "weight": 184.2,
      "weight_date": "2025-10-17",
      "notes": "After workout",
      "created_at": "2025-10-17 18:00:00",
      "updated_at": "2025-10-17 18:00:00"
    },
    {
      "id": 3,
      "weight": 186.0,
      "weight_date": "2025-10-16",
      "notes": "",
      "created_at": "2025-10-16 08:00:00",
      "updated_at": "2025-10-16 08:00:00"
    }
  ]
}
```

#### Filter by Date Range
```bash
curl -X GET "http://localhost:8000/api/weight.php?start_date=2025-10-01&end_date=2025-10-31" \
  -H "Authorization: Bearer b7e3c5f1a9d2468013579bcdefa2468013579bcdefa2468013579bcdefa24680"
```

#### JavaScript Example
```javascript
async function getWeights(startDate = null, endDate = null) {
  const token = localStorage.getItem('authToken');
  
  let url = 'http://localhost:8000/api/weight.php';
  const params = new URLSearchParams();
  
  if (startDate) params.append('start_date', startDate);
  if (endDate) params.append('end_date', endDate);
  
  if (params.toString()) {
    url += '?' + params.toString();
  }
  
  const response = await fetch(url, {
    method: 'GET',
    headers: {
      'Authorization': `Bearer ${token}`
    }
  });
  
  const data = await response.json();
  console.log('Status:', response.status); // 200
  console.log('Weight entries:', data.weights);
  
  // Display in chart or table
  displayWeightChart(data.weights);
  
  return data.weights;
}

function displayWeightChart(weights) {
  // Example: Display weights in a simple list
  const container = document.getElementById('weightList');
  container.innerHTML = '';
  
  weights.forEach(entry => {
    const div = document.createElement('div');
    div.innerHTML = `
      <strong>${entry.weight} lbs</strong> - ${entry.weight_date}
      ${entry.notes ? `<br><em>${entry.notes}</em>` : ''}
    `;
    container.appendChild(div);
  });
}

// Get all weights
getWeights();

// Get weights for October 2025
getWeights('2025-10-01', '2025-10-31');
```

---

### 12. Add Weight Entry

#### curl Command
```bash
curl -X POST http://localhost:8000/api/weight.php \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer b7e3c5f1a9d2468013579bcdefa2468013579bcdefa2468013579bcdefa24680" \
  -d '{
    "weight": 183.5,
    "weight_date": "2025-10-19",
    "notes": "Morning weight before breakfast"
  }'
```

#### Response (Status: 201 Created)
```json
{
  "message": "Weight entry added successfully",
  "weight": {
    "id": 4,
    "weight": 183.5,
    "weight_date": "2025-10-19",
    "notes": "Morning weight before breakfast"
  }
}
```

#### JavaScript Example
```javascript
async function addWeight(weight, date, notes = '') {
  const token = localStorage.getItem('authToken');
  
  const response = await fetch('http://localhost:8000/api/weight.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    },
    body: JSON.stringify({
      weight: weight,
      weight_date: date,
      notes: notes
    })
  });
  
  const data = await response.json();
  console.log('Status:', response.status); // 201
  
  if (response.status === 201) {
    console.log('Weight added:', data.weight);
    alert(`Weight logged: ${data.weight.weight} lbs`);
  } else {
    alert('Error: ' + data.error);
  }
  
  return data;
}

// Usage
addWeight(183.5, '2025-10-19', 'Morning weight');
```

#### HTML Form Example
```html
<form id="weightForm">
  <input type="number" id="weightValue" placeholder="Weight (lbs)" step="0.1" required>
  <input type="date" id="weightDate" required>
  <textarea id="weightNotes" placeholder="Notes (optional)"></textarea>
  <button type="submit">Log Weight</button>
</form>

<div id="weightList"></div>

<script>
// Set today's date as default
document.getElementById('weightDate').valueAsDate = new Date();

document.getElementById('weightForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  
  const weight = parseFloat(document.getElementById('weightValue').value);
  const date = document.getElementById('weightDate').value;
  const notes = document.getElementById('weightNotes').value;
  const token = localStorage.getItem('authToken');
  
  const response = await fetch('/api/weight.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    },
    body: JSON.stringify({
      weight: weight,
      weight_date: date,
      notes: notes
    })
  });
  
  const result = await response.json();
  
  if (response.status ===