<?php

class Response
{
    /**
     * Respond to the current request with the given message and error code.
     *
     * @param string $message
     * @param int $statusCode
     */
    public static function error(string $message, int $statusCode = 500): void
    {
        self::respond($message, $statusCode);
    }

    /**
     * Respond to the current request with the given message and success code.
     * @param string $message
     * @param int $statusCode
     */
    public static function success(string $message, int $statusCode = 200): void
    {
        self::respond($message, $statusCode);
    }

    /**
     * Respond to the current request with the given message and status code.
     * @param string $message
     * @param int $statusCode
     */
    private static function respond(string $message, int $statusCode = 500): void
    {
        http_response_code($statusCode);
        echo json_encode(['message' => $message]);
        exit;
    }
}