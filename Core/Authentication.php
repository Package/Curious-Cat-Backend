<?php


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

        $maybeUser = User::validateToken($maybeToken);
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
}