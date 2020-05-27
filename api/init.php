<?php
require_once '../vendor/autoload.php';
require_once '../Database/Database.php';
require_once '../Database/Models/Answer.php';
require_once '../Database/Models/Question.php';
require_once '../Database/Models/User.php';
require_once '../Database/Errors/InvalidLoginException.php';
require_once '../Database/Errors/InvalidRegistrationException.php';
require_once '../Database/Errors/OperationFailedException.php';

header('Content-type:application/json;charset=utf-8');

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