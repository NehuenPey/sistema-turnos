<?php
require_once "../config/cors.php";
require_once "../middleware/auth.php";
require_once "../config/database.php";

$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {

    case "GET":

        // USER: solo sus turnos
        if ($user["role"] === "user") {

            $stmt = $pdo->prepare(
                "SELECT a.id, c.name AS client, a.date, a.time, a.status
                 FROM appointments a
                 JOIN clients c ON a.client_id = c.id
                 WHERE a.user_id = :user_id"
            );

            $stmt->execute([
                "user_id" => $user["id"]
            ]);
        } else {
            // ADMIN: todos los turnos
            $stmt = $pdo->query(
                "SELECT a.id, c.name AS client, a.date, a.time, a.status
                 FROM appointments a
                 JOIN clients c ON a.client_id = c.id"
            );
        }

        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case "POST":

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data["client_id"], $data["date"], $data["time"])) {
            http_response_code(400);
            echo json_encode(["error" => "Invalid data"]);
            exit;
        }

        // Turnos en el pasado
        $now = new DateTime();
        $appointmentDateTime = new DateTime($data["date"] . ' ' . $data["time"]);

        if ($appointmentDateTime < $now) {
            http_response_code(400);
            echo json_encode(["error" => "Cannot create appointments in the past"]);
            exit;
        }

        // Horario ocupado
        $check = $pdo->prepare(
            "SELECT COUNT(*) FROM appointments
             WHERE date = :date
             AND time = :time
             AND status IN ('pending', 'confirmed')"
        );
        $check->execute([
            "date" => $data["date"],
            "time" => $data["time"]
        ]);

        if ($check->fetchColumn() > 0) {
            http_response_code(409);
            echo json_encode(["error" => "Time slot already taken"]);
            exit;
        }

        // Crear turno
        $stmt = $pdo->prepare(
            "INSERT INTO appointments (client_id, user_id, date, time, status)
             VALUES (:client_id, :user_id, :date, :time, 'pending')"
        );

        $stmt->execute([
            "client_id" => $data["client_id"],
            "user_id"   => $user["id"],
            "date"      => $data["date"],
            "time"      => $data["time"]
        ]);

        echo json_encode(["message" => "Appointment created"]);
        break;

    case "PUT":

        parse_str($_SERVER["QUERY_STRING"], $params);
        $id = $params["id"] ?? null;
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$id || !isset($data["status"])) {
            http_response_code(400);
            echo json_encode(["error" => "Invalid data"]);
            exit;
        }

        // Buscar turno
        $stmt = $pdo->prepare(
            "SELECT status, user_id FROM appointments WHERE id = :id"
        );
        $stmt->execute(["id" => $id]);
        $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$appointment) {
            http_response_code(404);
            echo json_encode(["error" => "Appointment not found"]);
            exit;
        }

        // USER
        if ($user["role"] === "user") {

            // Solo sus turnos
            if ($appointment["user_id"] != $user["id"]) {
                http_response_code(403);
                echo json_encode(["error" => "Forbidden"]);
                exit;
            }

            // Solo cancelar pending
            if ($appointment["status"] !== "pending" || $data["status"] !== "cancelled") {
                http_response_code(400);
                echo json_encode(["error" => "You can only cancel pending appointments"]);
                exit;
            }
        }

        // ADMIN (mantiene reglas)
        if ($user["role"] === "admin") {

            if ($appointment["status"] === "cancelled") {
                http_response_code(400);
                echo json_encode(["error" => "Already cancelled"]);
                exit;
            }

            if ($appointment["status"] === "confirmed" && $data["status"] === "cancelled") {
                http_response_code(400);
                echo json_encode(["error" => "Confirmed appointments cannot be cancelled"]);
                exit;
            }
        }

        // Actualizar estado
        $update = $pdo->prepare(
            "UPDATE appointments SET status = :status WHERE id = :id"
        );
        $update->execute([
            "status" => $data["status"],
            "id" => $id
        ]);

        echo json_encode(["message" => "Appointment updated"]);
        break;

    case "DELETE":

        // SOLO ADMIN
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
