<?php
require_once '../vendor/autoload.php';

header('Content-Type: application/json; charset=utf-8');

use \Symfony\Component\Dotenv\Dotenv;
$dotEnv = new Dotenv();
$dotEnv->load('../.env');

function exitWithMessage(string $message, int $statusCode = 500) : void {
    http_response_code($statusCode);
    echo json_encode(['message' => $message]);
    exit;
}

function validateAccessToken() {
    $headers = apache_request_headers();
    $authHeader = $headers['Authorization'] ?? false;

    if (!$authHeader || !strlen($authHeader)) {
        exitWithMessage("Unauthorized. Please login.", 401);
    }

    $tokenExplode = explode('Bearer ', $authHeader);
    $accessToken = $tokenExplode[1];

    try {
        $maybeUser = User::validateToken($accessToken);
        if (!$maybeUser) {
            exitWithMessage("Invalid token. Please login.", 401);
        }

        return $maybeUser;
    } catch (Exception $e) {
        exitWithMessage("Invalid token. Please login.", 401);
    }

    return false;
}