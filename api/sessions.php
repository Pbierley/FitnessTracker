<?php
// api/sessions.php

require_once '../config.php';

setJSONHeaders();

$user = getUserFromAuth();
if (!$user) {
    sendResponse(['error' => 'Unauthorized'], 401);
}

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);
$conn = getDBConnection();

// GET - List sessions or get single session
if ($method === 'GET') {
    $sessionId = $_GET['id'] ?? null;
    $workoutId = $_GET['workout_id'] ?? null;
    
    if ($sessionId) {
        // Get single session with sets
        $stmt = $conn->prepare("
            SELECT ws.id, ws.workout_id, ws.session_date, ws.notes, 
                   w.name as workout_name
            FROM workout_sessions ws
            JOIN workouts w ON ws.workout_id = w.id
            WHERE ws.id = ? AND ws.user_id = ?
        ");
        $stmt->execute([$sessionId, $user['user_id']]);
        $session = $stmt->fetch();
        
        if (!$session) {
            sendResponse(['error' => 'Session not found'], 404);
        }
        
        // Get sets for this session
        $stmt = $conn->prepare("
            SELECT id, set_number, reps, weight
            FROM session_sets
            WHERE session_id = ?
            ORDER BY set_number ASC
        ");
        $stmt->execute([$sessionId]);
        $session['sets'] = $stmt->fetchAll();
        
        sendResponse($session);
    } else {
        // Get all sessions (optionally filtered by workout)
        $query = "
            SELECT ws.id, ws.workout_id, ws.session_date, ws.notes,
                   w.name as workout_name,
                   COUNT(ss.id) as total_sets,
                   SUM(ss.reps) as total_reps
            FROM workout_sessions ws
            JOIN workouts w ON ws.workout_id = w.id
            LEFT JOIN session_sets ss ON ws.id = ss.session_id
            WHERE ws.user_id = ?
        ";
        
        $params = [$user['user_id']];
        
        if ($workoutId) {
            $query .= " AND ws.workout_id = ?";
            $params[] = $workoutId;
        }
        
        $query .= " GROUP BY ws.id ORDER BY ws.session_date DESC";
        
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        $sessions = $stmt->fetchAll();
        
        sendResponse(['sessions' => $sessions]);
    }
}

// POST - Create new session
if ($method === 'POST') {
    $workoutId = $data['workout_id'] ?? null;
    $sessionDate = $data['session_date'] ?? date('Y-m-d');
    $notes = $data['notes'] ?? '';
    $sets = $data['sets'] ?? [];
    
    if (!$workoutId) {
        sendResponse(['error' => 'Workout ID is required'], 400);
    }
    
    // Verify workout ownership
    $stmt = $conn->prepare("SELECT id FROM workouts WHERE id = ? AND user_id = ?");
    $stmt->execute([$workoutId, $user['user_id']]);
    if (!$stmt->fetch()) {
        sendResponse(['error' => 'Workout not found'], 404);
    }
    
    try {
        $conn->beginTransaction();
        
        // Create session
        $stmt = $conn->prepare("
            INSERT INTO workout_sessions (workout_id, user_id, session_date, notes)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$workoutId, $user['user_id'], $sessionDate, $notes]);
        $sessionId = $conn->lastInsertId();
        
        // Add sets
        if (!empty($sets)) {
            $stmt = $conn->prepare("
                INSERT INTO session_sets (session_id, set_number, reps, weight)
                VALUES (?, ?, ?, ?)
            ");
            
            foreach ($sets as $idx => $set) {
                $setNumber = $set['set_number'] ?? ($idx + 1);
                $reps = $set['reps'] ?? 0;
                $weight = $set['weight'] ?? 0;
                
                $stmt->execute([$sessionId, $setNumber, $reps, $weight]);
            }
        }
        
        $conn->commit();
        
        sendResponse([
            'message' => 'Session created successfully',
            'session' => [
                'id' => $sessionId,
                'workout_id' => $workoutId,
                'session_date' => $sessionDate,
                'notes' => $notes
            ]
        ], 201);
    } catch (PDOException $e) {
        $conn->rollBack();
        sendResponse(['error' => 'Failed to create session'], 500);
    }
}

// PUT - Update session
if ($method === 'PUT') {
    $sessionId = $data['id'] ?? null;
    $sessionDate = $data['session_date'] ?? null;
    $notes = $data['notes'] ?? '';
    $sets = $data['sets'] ?? null;
    
    if (!$sessionId) {
        sendResponse(['error' => 'Session ID is required'], 400);
    }
    
    // Verify session ownership
    $stmt = $conn->prepare("SELECT id FROM workout_sessions WHERE id = ? AND user_id = ?");
    $stmt->execute([$sessionId, $user['user_id']]);
    if (!$stmt->fetch()) {
        sendResponse(['error' => 'Session not found'], 404);
    }
    
    try {
        $conn->beginTransaction();
        
        // Update session
        if ($sessionDate) {
            $stmt = $conn->prepare("
                UPDATE workout_sessions 
                SET session_date = ?, notes = ?
                WHERE id = ? AND user_id = ?
            ");
            $stmt->execute([$sessionDate, $notes, $sessionId, $user['user_id']]);
        } else {
            $stmt = $conn->prepare("
                UPDATE workout_sessions 
                SET notes = ?
                WHERE id = ? AND user_id = ?
            ");
            $stmt->execute([$notes, $sessionId, $user['user_id']]);
        }
        
        // Update sets if provided
        if ($sets !== null) {
            // Delete existing sets
            $stmt = $conn->prepare("DELETE FROM session_sets WHERE session_id = ?");
            $stmt->execute([$sessionId]);
            
            // Add new sets
            if (!empty($sets)) {
                $stmt = $conn->prepare("
                    INSERT INTO session_sets (session_id, set_number, reps, weight)
                    VALUES (?, ?, ?, ?)
                ");
                
                foreach ($sets as $idx => $set) {
                    $setNumber = $set['set_number'] ?? ($idx + 1);
                    $reps = $set['reps'] ?? 0;
                    $weight = $set['weight'] ?? 0;
                    
                    $stmt->execute([$sessionId, $setNumber, $reps, $weight]);
                }
            }
        }
        
        $conn->commit();
        
        sendResponse(['message' => 'Session updated successfully']);
    } catch (PDOException $e) {
        $conn->rollBack();
        sendResponse(['error' => 'Failed to update session'], 500);
    }
}

// DELETE - Delete session
if ($method === 'DELETE') {
    $sessionId = $data['id'] ?? $_GET['id'] ?? null;
    
    if (!$sessionId) {
        sendResponse(['error' => 'Session ID is required'], 400);
    }
    
    // Verify session ownership
    $stmt = $conn->prepare("SELECT id FROM workout_sessions WHERE id = ? AND user_id = ?");
    $stmt->execute([$sessionId, $user['user_id']]);
    if (!$stmt->fetch()) {
        sendResponse(['error' => 'Session not found'], 404);
    }
    
    $stmt = $conn->prepare("DELETE FROM workout_sessions WHERE id = ? AND user_id = ?");
    
    try {
        $stmt->execute([$sessionId, $user['user_id']]);
        sendResponse(['message' => 'Session deleted successfully']);
    } catch (PDOException $e) {
        sendResponse(['error' => 'Failed to delete session'], 500);
    }
}

sendResponse(['error' => 'Invalid request'], 400);
?>