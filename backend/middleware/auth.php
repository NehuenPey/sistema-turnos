<?php
require_once "../config/jwt_utils.php";

// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Leer X-Authorization
if (!isset($_SERVER['HTTP_X_AUTHORIZATION'])) {
    http_response_code(401);
    echo json_encode(["error" => "Token missing"]);
    exit;
}

$token = $_SERVER['HTTP_X_AUTHORIZATION'];
$user  = verify_jwt($token);

if (!$user) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid or expired token"]);
    exit;
}

// Usuario disponible
$user = [
    'id'   => $user['user_id'],
    'role' => $user['role']
];
