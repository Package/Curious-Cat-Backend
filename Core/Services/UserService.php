<?php


class UserService extends BaseService
{
    /**
     * @param string $username
     * @param string $emailAddress
     * @param string $plainPassword
     * @param string $confirmPassword
     * @return bool|false|string
     * @throws InvalidRegistrationException
     * @throws InvalidLoginException
     */
    public function register(string $username, string $emailAddress, string $plainPassword, string $confirmPassword)
    {
        if ($plainPassword !== $confirmPassword) {
            throw new InvalidRegistrationException("Password and confirm password do not match.");
        }

        // Validate the username and email are not already in use.
        $statement = $this->db->prepare("SELECT * FROM users WHERE username = :username OR email_address = :email_address LIMIT 1");
        $statement->execute([$username, $emailAddress]);
        $maybeUser = $statement->fetch(PDO::FETCH_ASSOC);
        if ($maybeUser != null) {
            $inUseMessage = $maybeUser['username'] === $username ? 'Username' : 'Email Address';
            throw new InvalidRegistrationException("{$inUseMessage} already in use. Please enter another and try again.");
        }

        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

        // Register the new user.
        $statement = $this->db->prepare("INSERT INTO users (username, email_address, password, created_at) VALUES(:username, :email_address, :password, NOW())");
        $statement->execute([$username, $emailAddress, $hashedPassword]);

        // Log them in straight away.
        return $this->login($username, $plainPassword);
    }

    /**
     * Attempts to log the user in with the provided credentials.
     *
     * @param string $usernameOrEmail
     * @param string $plainPassword
     * @return bool
     * @throws InvalidLoginException
     */
    public function login(string $usernameOrEmail, string $plainPassword)
    {
        $statement = $this->db->prepare("SELECT * FROM users WHERE username = :user_or_email OR email_address = :user_or_email LIMIT 1");
        $statement->setFetchMode(PDO::FETCH_CLASS, User::class);
        $statement->execute([$usernameOrEmail]);

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
        return Authentication::generateWebToken($maybeUser);
    }
}