<?php
// CORS headers
require_once "../config/cors.php";
header("Access-Control-Allow-Origin: http://localhost:4200"); // tu Angular
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Responde OK a las preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once "../config/database.php";
require_once "../config/jwt_utils.php";

// resto de tu código...
$data = json_decode(file_get_contents("php://input"), true);

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute(["email" => $data["email"]]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($data["password"], $user["password"])) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid credentials"]);
    exit;
}

$token = generate_jwt([
    "user_id" => $user["id"],
    "role" => $user["role"]
]);

echo json_encode([
    "token" => $token
]);
?>
