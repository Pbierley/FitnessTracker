<?php
// api/verify.php

require_once '../config.php';

setJSONHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendResponse(['error' => 'Method not allowed'], 405);
}

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
?>