<?php
require_once "../config/cors.php";
require_once "../config/database.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["name"], $data["email"], $data["password"])) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid data"]);
    exit;
}

$hashedPassword = password_hash($data["password"], PASSWORD_DEFAULT);

$stmt = $pdo->prepare(
    "INSERT INTO users (name, email, password, role)
     VALUES (:name, :email, :password, 'user')"
);

$stmt->execute([
    "name" => $data["name"],
    "email" => $data["email"],
    "password" => $hashedPassword
]);

echo json_encode(["message" => "User registered successfully"]);
