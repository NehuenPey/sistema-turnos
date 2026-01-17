<?php

require_once "jwt.php";

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode($data) {
    return base64_decode(strtr($data, '-_', '+/'));
}

function generate_jwt($payload) {
    $header = base64url_encode(json_encode(["alg" => "HS256", "typ" => "JWT"]));

    $payload["exp"] = time() + JWT_EXP;
    $payload_encoded = base64url_encode(json_encode($payload));

    $signature = hash_hmac(
        "sha256",
        "$header.$payload_encoded",
        JWT_SECRET,
        true
    );

    $signature_encoded = base64url_encode($signature);

    return "$header.$payload_encoded.$signature_encoded";
}

function verify_jwt($token) {
    $parts = explode(".", $token);
    if (count($parts) !== 3) return false;

    [$header, $payload, $signature] = $parts;

    $valid_signature = base64url_encode(
        hash_hmac("sha256", "$header.$payload", JWT_SECRET, true)
    );

    if ($signature !== $valid_signature) return false;

    $data = json_decode(base64url_decode($payload), true);
    if ($data["exp"] < time()) return false;

    return $data;
}
