<?php
require_once '../vendor/autoload.php';

/*
 * Set default content type header. This is an API which serves only JSON responses.
 */
header('Content-Type: application/json; charset=utf-8');

/*
 * Bring in our environment variables. This Symfony package will put them into the global $_ENV
 */
(new \Symfony\Component\Dotenv\Dotenv())->load('../.env');

function exitWithMessage(string $message, int $statusCode = 500) : void {
    http_response_code($statusCode);
    echo json_encode(['message' => $message]);
    exit;
}