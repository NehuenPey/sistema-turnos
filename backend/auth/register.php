<?php
require_once "../config/cors.php";
require_once "../config/database.php";

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data["email"], $data["password"])) {
        http_response_code(400);
        echo json_encode(["error" => "Email y password requeridos"]);
        exit;
    }

    $email = trim($data["email"]);
    $password = password_hash($data["password"], PASSWORD_BCRYPT);
    $role = 'user';

    // 👉 nombre por defecto (podés cambiarlo después)
    $name = explode('@', $email)[0];

    // Verificar si existe
    $check = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $check->execute(["email" => $email]);

    if ($check->fetch()) {
        http_response_code(409);
        echo json_encode(["error" => "El usuario ya existe"]);
        exit;
    }

    // Insertar usuario
    $stmt = $pdo->prepare(
        "INSERT INTO users (name, email, password, role)
         VALUES (:name, :email, :password, :role)"
    );

    $stmt->execute([
        "name" => $name,
        "email" => $email,
        "password" => $password,
        "role" => $role
    ]);

    echo json_encode(["message" => "Usuario registrado"]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "Error interno",
        "detail" => $e->getMessage()
    ]);
}
