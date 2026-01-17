<?php
require_once "../config/cors.php";
require_once "../middleware/auth.php";
require_once "../config/database.php";

$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {

    case "GET":
        $stmt = $pdo->query(
            "SELECT a.id, c.name AS client, a.date, a.time, a.status
             FROM appointments a
             JOIN clients c ON a.client_id = c.id"
        );
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case "POST":
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data["client_id"], $data["date"], $data["time"])) {
            http_response_code(400);
            echo json_encode(["error" => "Invalid data"]);
            exit;
        }

        $stmt = $pdo->prepare(
            "INSERT INTO appointments (client_id, date, time, status)
             VALUES (:client_id, :date, :time, 'pending')"
        );

        $stmt->execute([
            "client_id" => $data["client_id"],
            "date" => $data["date"],
            "time" => $data["time"]
        ]);

        echo json_encode(["message" => "Appointment created"]);
        break;

    case "PUT":
        // 🔒 SOLO ADMIN
        if ($user["role"] !== "admin") {
            http_response_code(403);
            echo json_encode(["error" => "Forbidden"]);
            exit;
        }

        parse_str($_SERVER["QUERY_STRING"], $params);
        $id = $params["id"] ?? null;

        $data = json_decode(file_get_contents("php://input"), true);

        if (!$id || !isset($data["status"])) {
            http_response_code(400);
            echo json_encode(["error" => "Invalid data"]);
            exit;
        }

        $stmt = $pdo->prepare(
            "UPDATE appointments
             SET status = :status
             WHERE id = :id"
        );

        $stmt->execute([
            "status" => $data["status"],
            "id" => $id
        ]);

        echo json_encode(["message" => "Appointment updated"]);
        break;

    case "DELETE":
        //SOLO ADMIN
        if ($user["role"] !== "admin") {
            http_response_code(403);
            echo json_encode(["error" => "Forbidden"]);
            exit;
        }

        parse_str($_SERVER["QUERY_STRING"], $params);
        $id = $params["id"] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "ID required"]);
            exit;
        }

        $stmt = $pdo->prepare(
            "DELETE FROM appointments WHERE id = :id"
        );
        $stmt->execute(["id" => $id]);

        echo json_encode(["message" => "Appointment deleted"]);
        break;

    default:
        http_response_code(405);
}
