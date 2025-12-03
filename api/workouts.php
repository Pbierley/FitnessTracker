<?php
// api/workouts.php

require_once '../config.php';

setJSONHeaders();

$user = getUserFromAuth();
if (!$user) {
    sendResponse(['error' => 'Unauthorized'], 401);
}

$method = $_SERVER['REQUEST_METHOD'];
$conn = getDBConnection();

// GET /api/workouts - List all workouts
// GET /api/workouts?id=1 - Get single workout
if ($method === 'GET') {
    $workoutId = $_GET['id'] ?? null;
    
    if ($workoutId) {
        $stmt = $conn->prepare("
            SELECT id, name, description, created_at, updated_at 
            FROM workouts 
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([$workoutId, $user['user_id']]);
        $workout = $stmt->fetch();
        
        if (!$workout) {
            sendResponse(['error' => 'Workout not found'], 404);
        }
        
        sendResponse($workout);
    } else {
        $stmt = $conn->prepare("
            SELECT id, name, description, created_at, updated_at 
            FROM workouts 
            WHERE user_id = ? 
            ORDER BY name ASC
        ");
        $stmt->execute([$user['user_id']]);
        $workouts = $stmt->fetchAll();
        
        sendResponse(['workouts' => $workouts]);
    }
}

// POST /api/workouts - Create new workout
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = $data['name'] ?? '';
    $description = $data['description'] ?? '';
    
    if (empty($name)) {
        sendResponse(['error' => 'Workout name is required'], 400);
    }
    
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

// PUT /api/workouts - Update workout
if ($method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $workoutId = $data['id'] ?? null;
    $name = $data['name'] ?? '';
    $description = $data['description'] ?? '';
    
    if (!$workoutId) {
        sendResponse(['error' => 'Workout ID is required'], 400);
    }
    
    if (empty($name)) {
        sendResponse(['error' => 'Workout name is required'], 400);
    }
    
    // Verify ownership
    $stmt = $conn->prepare("SELECT id FROM workouts WHERE id = ? AND user_id = ?");
    $stmt->execute([$workoutId, $user['user_id']]);
    if (!$stmt->fetch()) {
        sendResponse(['error' => 'Workout not found'], 404);
    }
    
    $stmt = $conn->prepare("
        UPDATE workouts 
        SET name = ?, description = ? 
        WHERE id = ? AND user_id = ?
    ");
    
    try {
        $stmt->execute([$name, $description, $workoutId, $user['user_id']]);
        sendResponse(['message' => 'Workout updated successfully']);
    } catch (PDOException $e) {
        sendResponse(['error' => 'Failed to update workout'], 500);
    }
}

// DELETE /api/workouts?id=1 - Delete workout
if ($method === 'DELETE') {
    $workoutId = $_GET['id'] ?? null;
    
    if (!$workoutId) {
        sendResponse(['error' => 'Workout ID is required'], 400);
    }
    
    // Verify ownership
    $stmt = $conn->prepare("SELECT id FROM workouts WHERE id = ? AND user_id = ?");
    $stmt->execute([$workoutId, $user['user_id']]);
    if (!$stmt->fetch()) {
        sendResponse(['error' => 'Workout not found'], 404);
    }
    
    $stmt = $conn->prepare("DELETE FROM workouts WHERE id = ? AND user_id = ?");
    
    try {
        $stmt->execute([$workoutId, $user['user_id']]);
        sendResponse(['message' => 'Workout deleted successfully']);
    } catch (PDOException $e) {
        sendResponse(['error' => 'Failed to delete workout'], 500);
    }
}

sendResponse(['error' => 'Invalid request'], 400);
?>