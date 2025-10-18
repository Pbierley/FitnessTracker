<?php
// api/register.php

require_once '../config.php';

setJSONHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(['error' => 'Method not allowed'], 405);
}

$data = json_decode(file_get_contents('php://input'), true);

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
?>