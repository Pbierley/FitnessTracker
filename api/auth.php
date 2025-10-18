<?php
// api/auth.php

require_once '../config.php';

setJSONHeaders();

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

// Register new user
if ($method === 'POST' && isset($data['action']) && $data['action'] === 'register') {
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        sendResponse(['error' => 'Email and password are required'], 400);
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendResponse(['error' => 'Invalid email format'], 400);
    }
    
    if (strlen($password) < 6) {
        sendResponse(['error' => 'Password must be at least 6 characters'], 400);
    }
    
    $conn = getDBConnection();
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        sendResponse(['error' => 'Email already registered'], 409);
    }
    
    // Hash password and create user
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO users (email, password_hash) VALUES (?, ?)");
    
    try {
        $stmt->execute([$email, $passwordHash]);
        $userId = $conn->lastInsertId();
        
        // Generate token
        $token = generateToken();
        $expiresAt = date('Y-m-d H:i:s', time() + TOKEN_EXPIRATION);
        
        $stmt = $conn->prepare("INSERT INTO auth_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $token, $expiresAt]);
        
        sendResponse([
            'message' => 'User registered successfully',
            'token' => $token,
            'user' => [
                'id' => $userId,
                'email' => $email
            ]
        ], 201);
    } catch (PDOException $e) {
        sendResponse(['error' => 'Registration failed'], 500);
    }
}

// Login
if ($method === 'POST' && isset($data['action']) && $data['action'] === 'login') {
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        sendResponse(['error' => 'Email and password are required'], 400);
    }
    
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT id, email, password_hash FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($password, $user['password_hash'])) {
        sendResponse(['error' => 'Invalid email or password'], 401);
    }
    
    // Generate new token
    $token = generateToken();
    $expiresAt = date('Y-m-d H:i:s', time() + TOKEN_EXPIRATION);
    
    $stmt = $conn->prepare("INSERT INTO auth_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$user['id'], $token, $expiresAt]);
    
    sendResponse([
        'message' => 'Login successful',
        'token' => $token,
        'user' => [
            'id' => $user['id'],
            'email' => $user['email']
        ]
    ]);
}

// Logout
if ($method === 'POST' && isset($data['action']) && $data['action'] === 'logout') {
    $user = getUserFromAuth();
    
    if (!$user) {
        sendResponse(['error' => 'Unauthorized'], 401);
    }
    
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';
    preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches);
    $token = $matches[1];
    
    $conn = getDBConnection();
    $stmt = $conn->prepare("DELETE FROM auth_tokens WHERE token = ?");
    $stmt->execute([$token]);
    
    sendResponse(['message' => 'Logged out successfully']);
}

// Verify token
if ($method === 'GET') {
    $user = getUserFromAuth();
    
    if (!$user) {
        sendResponse(['error' => 'Unauthorized'], 401);
    }
    
    sendResponse([
        'valid' => true,
        'user' => [
            'id' => $user['user_id'],
            'email' => $user['email']
        ]
    ]);
}

sendResponse(['error' => 'Invalid request'], 400);
?>