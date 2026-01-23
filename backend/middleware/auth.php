<?php
require_once "../config/jwt_utils.php";

// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Header correcto
if (!isset($_SERVER['HTTP_X_AUTHORIZATION'])) {
    http_response_code(401);
    echo json_encode(["error" => "Token missing"]);
    exit;
}

$token = $_SERVER['HTTP_X_AUTHORIZATION'];
$payload = verify_jwt($token);

if (!$payload) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid or expired token"]);
    exit;
}

// Validación del payload REAL
if (!isset($payload['user_id'], $payload['role'])) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid token payload"]);
    exit;
}

// Usuario normalizado (contrato interno)
$user = [
    "id"   => $payload["user_id"],
    "role" => $payload["role"]
];
