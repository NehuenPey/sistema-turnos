<?php
require_once "../config/cors.php";
require_once "../middleware/auth.php";
require_once "../config/database.php";

// $user viene del auth.php
$userId = $user["id"];

$stmt = $pdo->prepare("
    SELECT a.id, a.date, a.time, a.status
    FROM appointments a
    WHERE a.user_id = :user_id
    ORDER BY a.date, a.time
");

$stmt->execute([
    "user_id" => $userId
]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
