<?php
require_once "../config/cors.php";
require_once "../config/database.php";
require_once "../middleware/auth.php";

$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {
    case "GET":
        $stmt = $pdo->query("SELECT * FROM clients");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case "POST":
        $data = json_decode(file_get_contents("php://input"), true);

        $stmt = $pdo->prepare(
            "INSERT INTO clients (name, phone, email)
             VALUES (:name, :phone, :email)"
        );

        $stmt->execute([
            "name" => $data["name"],
            "phone" => $data["phone"],
            "email" => $data["email"]
        ]);

        echo json_encode(["message" => "Client created"]);
        break;

    default:
        http_response_code(405);
}
