<?php
// api/weight.php

require_once '../config.php';

setJSONHeaders();

$user = getUserFromAuth();
if (!$user) {
    sendResponse(['error' => 'Unauthorized'], 401);
}

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);
$conn = getDBConnection();

// GET - List weight entries
if ($method === 'GET') {
    $weightId = $_GET['id'] ?? null;
    $startDate = $_GET['start_date'] ?? null;
    $endDate = $_GET['end_date'] ?? null;
    
    if ($weightId) {
        // Get single weight entry
        $stmt = $conn->prepare("
            SELECT id, weight, weight_date, notes, created_at, updated_at
            FROM weight_tracking
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([$weightId, $user['user_id']]);
        $weight = $stmt->fetch();
        
        if (!$weight) {
            sendResponse(['error' => 'Weight entry not found'], 404);
        }
        
        sendResponse($weight);
    } else {
        // Get all weight entries with optional date range
        $query = "
            SELECT id, weight, weight_date, notes, created_at, updated_at
            FROM weight_tracking
            WHERE user_id = ?
        ";
        
        $params = [$user['user_id']];
        
        if ($startDate) {
            $query .= " AND weight_date >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $query .= " AND weight_date <= ?";
            $params[] = $endDate;
        }
        
        $query .= " ORDER BY weight_date DESC";
        
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        $weights = $stmt->fetchAll();
        
        sendResponse(['weights' => $weights]);
    }
}

// POST - Add weight entry
if ($method === 'POST') {
    $weight = $data['weight'] ?? null;
    $weightDate = $data['weight_date'] ?? date('Y-m-d');
    $notes = $data['notes'] ?? '';
    
    if (!$weight || $weight <= 0) {
        sendResponse(['error' => 'Valid weight is required'], 400);
    }
    
    $stmt = $conn->prepare("
        INSERT INTO weight_tracking (user_id, weight, weight_date, notes)
        VALUES (?, ?, ?, ?)
    ");
    
    try {
        $stmt->execute([$user['user_id'], $weight, $weightDate, $notes]);
        $weightId = $conn->lastInsertId();
        
        sendResponse([
            'message' => 'Weight entry added successfully',
            'weight' => [
                'id' => $weightId,
                'weight' => $weight,
                'weight_date' => $weightDate,
                'notes' => $notes
            ]
        ], 201);
    } catch (PDOException $e) {
        // Check for duplicate date
        if ($e->getCode() == 23000) {
            sendResponse(['error' => 'Weight entry already exists for this date'], 409);
        }
        sendResponse(['error' => 'Failed to add weight entry'], 500);
    }
}

// PUT - Update weight entry
if ($method === 'PUT') {
    $weightId = $data['id'] ?? null;
    $weight = $data['weight'] ?? null;
    $weightDate = $data['weight_date'] ?? null;
    $notes = $data['notes'] ?? '';
    
    if (!$weightId) {
        sendResponse(['error' => 'Weight ID is required'], 400);
    }
    
    if ($weight !== null && $weight <= 0) {
        sendResponse(['error' => 'Valid weight is required'], 400);
    }
    
    // Verify ownership
    $stmt = $conn->prepare("SELECT id FROM weight_tracking WHERE id = ? AND user_id = ?");
    $stmt->execute([$weightId, $user['user_id']]);
    if (!$stmt->fetch()) {
        sendResponse(['error' => 'Weight entry not found'], 404);
    }
    
    // Build update query dynamically
    $updates = [];
    $params = [];
    
    if ($weight !== null) {
        $updates[] = "weight = ?";
        $params[] = $weight;
    }
    
    if ($weightDate !== null) {
        $updates[] = "weight_date = ?";
        $params[] = $weightDate;
    }
    
    $updates[] = "notes = ?";
    $params[] = $notes;
    
    $params[] = $weightId;
    $params[] = $user['user_id'];
    
    $query = "UPDATE weight_tracking SET " . implode(', ', $updates) . " WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    
    try {
        $stmt->execute($params);
        sendResponse(['message' => 'Weight entry updated successfully']);
    } catch (PDOException $e) {
        // Check for duplicate date
        if ($e->getCode() == 23000) {
            sendResponse(['error' => 'Weight entry already exists for this date'], 409);
        }
        sendResponse(['error' => 'Failed to update weight entry'], 500);
    }
}

// DELETE - Delete weight entry
if ($method === 'DELETE') {
    $weightId = $data['id'] ?? $_GET['id'] ?? null;
    
    if (!$weightId) {
        sendResponse(['error' => 'Weight ID is required'], 400);
    }
    
    // Verify ownership
    $stmt = $conn->prepare("SELECT id FROM weight_tracking WHERE id = ? AND user_id = ?");
    $stmt->execute([$weightId, $user['user_id']]);
    if (!$stmt->fetch()) {
        sendResponse(['error' => 'Weight entry not found'], 404);
    }
    
    $stmt = $conn->prepare("DELETE FROM weight_tracking WHERE id = ? AND user_id = ?");
    
    try {
        $stmt->execute([$weightId, $user['user_id']]);
        sendResponse(['message' => 'Weight entry deleted successfully']);
    } catch (PDOException $e) {
        sendResponse(['error' => 'Failed to delete weight entry'], 500);
    }
}

sendResponse(['error' => 'Invalid request'], 400);
?>