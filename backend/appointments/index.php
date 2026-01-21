<?php
require_once "../config/cors.php";
require_once "../middleware/auth.php";
require_once "../config/database.php";

$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {

    /* =========================
       GET
    ==========================*/
    case "GET":

        if ($user["role"] === "patient") {

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
            // secretary / admin / doctor
            $stmt = $pdo->query(
                "SELECT a.id, c.name AS client, a.date, a.time, a.status
                 FROM appointments a
                 JOIN clients c ON a.client_id = c.id"
            );
        }

        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;


    /* =========================
       POST → paciente solicita
    ==========================*/
    case "POST":

        if ($user["role"] !== "patient") {
            http_response_code(403);
            echo json_encode(["error" => "Only patients can request appointments"]);
            exit;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data["client_id"])) {
            http_response_code(400);
            echo json_encode(["error" => "Client required"]);
            exit;
        }

        $stmt = $pdo->prepare(
            "INSERT INTO appointments (client_id, user_id, status)
             VALUES (:client_id, :user_id, 'requested')"
        );

        $stmt->execute([
            "client_id" => $data["client_id"],
            "user_id"   => $user["id"]
        ]);

        echo json_encode(["message" => "Appointment requested"]);
        break;


    /* =========================
       PUT → asignar / cancelar
    ==========================*/
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
            "SELECT status, user_id, date, time
             FROM appointments
             WHERE id = :id"
        );
        $stmt->execute(["id" => $id]);
        $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$appointment) {
            http_response_code(404);
            echo json_encode(["error" => "Appointment not found"]);
            exit;
        }

        /* ===== PATIENTE: cancelar ===== */
        if ($user["role"] === "patient") {

            if ($appointment["user_id"] != $user["id"]) {
                http_response_code(403);
                echo json_encode(["error" => "Forbidden"]);
                exit;
            }

            if ($data["status"] !== "cancelled") {
                http_response_code(400);
                echo json_encode(["error" => "Only cancellation allowed"]);
                exit;
            }

            // Regla 24 hs si ya está asignado
            if ($appointment["status"] === "assigned" && $appointment["date"] && $appointment["time"]) {

                $turno = new DateTime($appointment["date"] . ' ' . $appointment["time"]);
                $now = new DateTime();

                if (($turno->getTimestamp() - $now->getTimestamp()) < 86400) {
                    http_response_code(400);
                    echo json_encode(["error" => "Cannot cancel within 24 hours"]);
                    exit;
                }
            }

            $stmt = $pdo->prepare(
                "UPDATE appointments SET status = 'cancelled' WHERE id = :id"
            );
            $stmt->execute(["id" => $id]);

            echo json_encode(["message" => "Appointment cancelled"]);
            break;
        }

        /* ===== SECRETARY / ADMIN: asignar ===== */
        if (in_array($user["role"], ["secretary", "admin"])) {

            if ($data["status"] !== "assigned") {
                http_response_code(400);
                echo json_encode(["error" => "Invalid status"]);
                exit;
            }

            if (!isset($data["date"], $data["time"])) {
                http_response_code(400);
                echo json_encode(["error" => "Date and time required"]);
                exit;
            }

            // Verificar horario ocupado
            $check = $pdo->prepare(
                "SELECT COUNT(*) FROM appointments
                 WHERE date = :date
                 AND time = :time
                 AND status = 'assigned'
                 AND id != :id"
            );

            $check->execute([
                "date" => $data["date"],
                "time" => $data["time"],
                "id"   => $id
            ]);

            if ($check->fetchColumn() > 0) {
                http_response_code(409);
                echo json_encode(["error" => "Time slot already taken"]);
                exit;
            }

            $stmt = $pdo->prepare(
                "UPDATE appointments
                 SET date = :date, time = :time, status = 'assigned'
                 WHERE id = :id"
            );

            $stmt->execute([
                "date" => $data["date"],
                "time" => $data["time"],
                "id"   => $id
            ]);

            echo json_encode(["message" => "Appointment assigned"]);
            break;
        }

        http_response_code(403);
        echo json_encode(["error" => "Forbidden"]);
        break;


    /* =========================
       DELETE → solo admin
    ==========================*/
    case "DELETE":

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
