<?php


class UserService extends BaseService
{
    /**
     * Attempts to register the user with the provided details.
     *
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
        if (!$this->comparePasswords($plainPassword, $confirmPassword)) {
            throw new InvalidRegistrationException("Password and confirm password do not match.");
        }
        if (!$this->checkPasswordComplexity($plainPassword)) {
            throw new InvalidRegistrationException("Password does not meet the minimum requirements (6 characters or more)");
        }
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

        if (!$this->checkUniqueEmail($emailAddress)) {
            throw new InvalidRegistrationException("Email Address already in use. Please enter another and try again.");
        }

        if (!$this->checkUniqueUsername($username)) {
            throw new InvalidRegistrationException("Username already in use. Please enter another and try again.");
        }

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

    /**
     * Gets details about a user. Does not include the password.
     *
     * @param array $user
     * @return mixed
     */
    public function get(array $user)
    {
        $statement = $this->db->prepare("SELECT id, username, email_address, created_at, photo_file FROM users WHERE id = :user LIMIT 1");
        $statement->setFetchMode(PDO::FETCH_CLASS, User::class);
        $statement->execute([$user["id"]]);

        return $statement->fetch();
    }

    /**
     * Sets the profile photo associated with this user.
     *
     * @param array $user
     * @param string|null $path
     */
    public function setPhoto(array $user, $path) : void
    {
        $statement = $this->db->prepare("UPDATE users SET photo_file = :photoFile WHERE id = :id");
        $statement->execute([$path, $user["id"]]);
    }

    /**
     * Allows a user to change their username or email address.
     *
     * @param array $user
     * @param string $username
     * @param string $emailAddress
     * @return mixed
     * @throws InvalidRegistrationException
     */
    public function updateDetails(array $user, string $username, string $emailAddress)
    {
        if (!$this->checkUniqueEmail($emailAddress, $user["id"])) {
            throw new InvalidRegistrationException("Email Address already in use. Please enter another and try again.");
        }

        if (!$this->checkUniqueUsername($username, $user["id"])) {
            throw new InvalidRegistrationException("Username already in use. Please enter another and try again.");
        }

        $statement = $this->db->prepare("
            UPDATE users 
            SET username = COALESCE(:username, username), email_address = COALESCE(:email_address, email_address)
            WHERE id = :userId");
        $statement->execute([$username, $emailAddress, $user["id"]]);

        return $this->get($user);
    }


    /**
     * Allows a user to change their password.
     *
     * @param array $user
     * @param string $password
     * @param string $confirmPassword
     * @return User
     * @throws OperationFailedException
     */
    public function updatePassword(array $user, string $password, string $confirmPassword) : User
    {
        if (!$this->comparePasswords($password, $confirmPassword)) {
            throw new OperationFailedException("Password and confirm password do not match.");
        }
        if (!$this->checkPasswordComplexity($password)) {
            throw new OperationFailedException("Password does not meet the minimum requirements (6 characters or more)");
        }
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $statement = $this->db->prepare("UPDATE users SET password = COALESCE(:password, password) WHERE id = :userId");
        $statement->execute([$hashedPassword, $user["id"]]);

        return $this->get($user);
    }

    /**
     * Validates that the username provided is not already in use.
     *
     * @param string $username
     * @param int $excludeUser
     * @return bool
     */
    private function checkUniqueUsername(string $username, int $excludeUser = 0) : bool
    {
        $statement = $this->db->prepare("SELECT * FROM users WHERE username = :username AND id <> :excludeUser LIMIT 1");
        $statement->execute([$username, $excludeUser]);
        $maybeUser = $statement->fetch(PDO::FETCH_ASSOC);

        return $maybeUser == null;
    }

    /**
     * Validates that the email address provided is not already in use.
     *
     * @param string $emailAddress
     * @param int $excludeUser
     * @return bool
     */
    private function checkUniqueEmail(string $emailAddress, int $excludeUser = 0) : bool
    {
        $statement = $this->db->prepare("SELECT * FROM users WHERE email_address = :email_address AND id <> :excludeUser LIMIT 1");
        $statement->execute([$emailAddress, $excludeUser]);
        $maybeUser = $statement->fetch(PDO::FETCH_ASSOC);

        return $maybeUser == null;
    }

    /**
     * Checks whether the password provided meets the minimum requirements.
     *
     * @param string $password
     * @return bool
     */
    private function checkPasswordComplexity(string $password) : bool
    {
        return strlen($password) >= 6;
    }

    /**
     * Checks whether two passwords are identical. Used when registering or updating user details to ensure
     * the user has not made a typo when setting or changing their password.
     *
     * @param $password
     * @param $confirmPassword
     * @return bool
     */
    private function comparePasswords(string $password, string $confirmPassword) : bool
    {
        if (!$password || !$confirmPassword) {
            return false;
        }

        return $password === $confirmPassword;
    }
}