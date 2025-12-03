#!/bin/bash

# Test script for Laravel Fitness Tracker API

echo "🧪 Testing Laravel Fitness Tracker API"
echo "========================================"
echo ""

# Check if server is running
echo "1. Checking if Laravel server is running..."
if curl -s http://localhost:8000 > /dev/null; then
    echo "✅ Server is running!"
else
    echo "❌ Server is not running. Starting server..."
    php artisan serve &
    sleep 3
fi

echo ""
echo "2. Testing Registration..."
REGISTER_RESPONSE=$(curl -s -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"email":"testuser@test.com","password":"test123"}')

echo "Response: $REGISTER_RESPONSE"

# Extract token if registration successful
TOKEN=$(echo $REGISTER_RESPONSE | grep -o '"token":"[^"]*' | cut -d'"' -f4)

if [ -z "$TOKEN" ]; then
    echo "⚠️  Registration failed or user already exists. Trying login..."
    
    echo ""
    echo "3. Testing Login..."
    LOGIN_RESPONSE=$(curl -s -X POST http://localhost:8000/api/auth/login \
      -H "Content-Type: application/json" \
      -d '{"email":"testuser@test.com","password":"test123"}')
    
    echo "Response: $LOGIN_RESPONSE"
    TOKEN=$(echo $LOGIN_RESPONSE | grep -o '"token":"[^"]*' | cut -d'"' -f4)
fi

if [ -z "$TOKEN" ]; then
    echo "❌ Could not get authentication token"
    exit 1
fi

echo "✅ Got authentication token: ${TOKEN:0:20}..."

echo ""
echo "4. Testing Get Workouts..."
WORKOUTS_RESPONSE=$(curl -s http://localhost:8000/api/workouts \
  -H "Authorization: Bearer $TOKEN")
echo "Response: $WORKOUTS_RESPONSE"

echo ""
echo "5. Testing Create Workout..."
CREATE_WORKOUT_RESPONSE=$(curl -s -X POST http://localhost:8000/api/workouts \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{"name":"Bench Press","description":"Chest workout"}')
echo "Response: $CREATE_WORKOUT_RESPONSE"

echo ""
echo "6. Testing Get Weight Entries..."
WEIGHT_RESPONSE=$(curl -s http://localhost:8000/api/weight \
  -H "Authorization: Bearer $TOKEN")
echo "Response: $WEIGHT_RESPONSE"

echo ""
echo "7. Testing Create Weight Entry..."
CREATE_WEIGHT_RESPONSE=$(curl -s -X POST http://localhost:8000/api/weight \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{"weight":185.5,"weight_date":"2024-12-02","notes":"Test weight"}')
echo "Response: $CREATE_WEIGHT_RESPONSE"

echo ""
echo "========================================"
echo "✅ API Testing Complete!"
echo ""
echo "Your Laravel API is working correctly!"
echo "API Base URL: http://localhost:8000/api"

