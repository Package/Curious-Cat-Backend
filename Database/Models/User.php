<?php

use \Firebase\JWT\JWT;

class User
{
    public $id;
    public $username;
    public $email_address;
    public $created_at;
    public $password;

    /**
     * @param string $username
     * @param string $emailAddress
     * @param string $plainPassword
     * @param string $confirmPassword
     * @return bool|false|string
     * @throws InvalidRegistrationException
     * @throws InvalidLoginException
     */
    public static function register(string $username, string $emailAddress, string $plainPassword, string $confirmPassword)
    {
        if ($plainPassword !== $confirmPassword) {
            throw new InvalidRegistrationException("Password and confirm password do not match.");
        }

        $db = Database::connect();

        // Validate the username and email are not already in use.
        $statement = $db->prepare("SELECT * FROM users WHERE username = :username OR email_address = :email_address LIMIT 1");
        $statement->bindParam(":username", $username, PDO::PARAM_STR);
        $statement->bindParam(":email_address", $emailAddress, PDO::PARAM_STR);
        $statement->execute();
        $maybeUser = $statement->fetch(PDO::FETCH_ASSOC);
        if ($maybeUser != null) {
            $inUseMessage = $maybeUser['username'] === $username ? 'Username' : 'Email Address';
            throw new InvalidRegistrationException("{$inUseMessage} already in use. Please enter another and try again.");
        }

        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

        // Register the new user.
        $statement = $db->prepare("INSERT INTO users (username, email_address, password, created_at)
                                            VALUES(:username, :email_address, :password, NOW())");
        $statement->bindParam(":username", $username, PDO::PARAM_STR);
        $statement->bindParam(":email_address", $emailAddress, PDO::PARAM_STR);
        $statement->bindParam(":password", $hashedPassword, PDO::PARAM_STR);
        $statement->execute();

        // Log them in straight away.
        return self::login($username, $plainPassword);
    }

    /**
     * Attempts to log the user in with the provided credentials.
     *
     * @param string $usernameOrEmail
     * @param string $plainPassword
     * @return bool
     * @throws InvalidLoginException
     */
    public static function login(string $usernameOrEmail, string $plainPassword)
    {
        $db = Database::connect();

        $statement = $db->prepare("SELECT * FROM users WHERE username = :user_or_email OR email_address = :user_or_email LIMIT 1");
        $statement->bindParam(":user_or_email", $usernameOrEmail, PDO::PARAM_STR);
        $statement->setFetchMode(PDO::FETCH_CLASS, User::class);
        $statement->execute();

        $maybeUser = $statement->fetch();
        if (!$maybeUser) {
            // No user found with provided username/email
            throw new InvalidLoginException("Username or password is incorrect.");
        }

        // Compare password
        if (!password_verify($plainPassword, $maybeUser->password)) {
            throw new InvalidLoginException("Username or password is incorrect.");
        }

        // If we get down here then the username & password match so login the user.
        return self::generateWebToken($maybeUser);
    }

    /**
     * Generates a JSON Web Token to serve as an authentication for future requests
     * by this user.
     *
     * @param User $user
     * @return false|string
     */
    private static function generateWebToken(User $user)
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