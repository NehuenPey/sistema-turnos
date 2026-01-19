<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Authorization");
header("Access-Control-Allow-Credentials: true");

// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// <?php
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Headers: Content-Type, Authorization");
// header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
// header("Content-Type: application/json; charset=UTF-8");

// if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
//     http_response_code(200);
//     echo json_encode(["ok" => true]);
//     exit;
// }
