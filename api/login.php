<?php
// api/login.php

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
?>