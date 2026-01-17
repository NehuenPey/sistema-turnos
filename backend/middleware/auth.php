<?php
require_once "../config/jwt_utils.php";

$authHeader = null;

// Header personalizado
if (isset($_SERVER['HTTP_X_AUTHORIZATION'])) {
    $authHeader = $_SERVER['HTTP_X_AUTHORIZATION'];
}

if (!$authHeader) {
    http_response_code(401);
    echo json_encode(["error" => "Token missing"]);
    exit;
}

$token = str_replace("Bearer ", "", $authHeader);
$user = verify_jwt($token);

if (!$user) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid or expired token"]);
    exit;
}

$_REQUEST['user'] = [
    'id' => $user['user_id'],
    'role' => $user['role']
];
