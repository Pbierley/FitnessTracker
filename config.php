<?php
// config.php

define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'fitness_tracker');

// JWT Secret Key (change this to a random string)
define('JWT_SECRET', 'your-secret-key-change-this-to-random-string');

// Token expiration (7 days in seconds)
define('TOKEN_EXPIRATION', 60 * 60 * 24 * 7);

// Database connection
function getDBConnection() {
    try {
        $conn = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $conn;
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database connection failed']);
        exit();
    }
}

// Set JSON headers
function setJSONHeaders() {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
}

// Generate authentication token
function generateToken() {
    return bin2hex(random_bytes(32));
}

// Verify authentication token
function verifyToken($token) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("
        SELECT at.user_id, u.email 
        FROM auth_tokens at
        JOIN users u ON at.user_id = u.id
        WHERE at.token = ? AND at.expires_at > NOW()
    ");
    $stmt->execute([$token]);
    return $stmt->fetch();
}

// Get user ID from authorization header
function getUserFromAuth() {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';
    
    if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
        $token = $matches[1];
        return verifyToken($token);
    }
    
    return false;
}

// Send JSON response
function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}
?>