<?php

use Firebase\JWT\JWT;

final class Authentication
{
    private $requireLogin = false;

    /**
     * Does the user need to be authenticated for this request?
     *
     * @param bool $authenticationNeeded
     * @return $this
     */
    public function authenticationNeeded(bool $authenticationNeeded = true) : Authentication
    {
        $this->requireLogin = $authenticationNeeded;
        return $this;
    }

    /**
     * Returns the details of the currently logged in user, or null if no users exists.
     * If authentication is required, an unauthorised user will trigger an UnauthorizedException.
     * @return array|bool
     */
    public function getCurrentUser()
    {
        $maybeToken = $this->parseTokenHeader();
        if ($this->requireLogin && !$maybeToken) {
            exitWithMessage("Unauthorized: You need to login to access this page.", 401);
        }

        $maybeUser = self::validateToken($maybeToken);
        if ($this->requireLogin && !$maybeUser) {
            exitWithMessage("Unauthorized: You do not have permission to access this.", 401);
        }

        return $maybeUser;
    }


    /**
     * Attempts to retrieve the users access token from the request headers.
     * The token should be sent in an Authorization header in the following format:
     *
     * Authorization: Bearer <access_token>
     *
     * @return string
     */
    private function parseTokenHeader()
    {
        $headers = apache_request_headers();
        $authHeader = $headers['Authorization'] ?? false;

        if (!$authHeader || !strlen($authHeader)) {
            return false;
        }

        $tokenExplode = explode('Bearer ', $authHeader);
        if (!$tokenExplode || count($tokenExplode) == 0) {
            return false;
        }

        $accessToken = $tokenExplode[1];

        return trim($accessToken);
    }

    /**
     * Generates a JSON Web Token to serve as an authentication for future requests
     * by this user.
     *
     * @param User $user
     * @return false|string
     */
    public static function generateWebToken(User $user)
    {
        $data = [
            'id' => $user->id,
            'username' => $user->username,
            'email_address' => $user->email_address
        ];

        $jwt = JWT::encode(
            $data, $_ENV['jwt_secret'], 'HS512'
        );

        return json_encode(['message' => 'Login Successful.', 'authorization_token' => $jwt]);
    }

    /**
     * Validates the access token provided.
     *
     * @param string $accessToken
     * @return array|bool
     */
    public static function validateToken(string $accessToken)
    {
        try {
            $payload = JWT::decode($accessToken, $_ENV['jwt_secret'], ['HS512']);

            return [
                'id' => $payload->id,
                'username' => $payload->username,
                'email_address' => $payload->email_address
            ];
        } catch (Exception $e) {}

        return false;
    }
}