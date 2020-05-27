<?php
require_once './init.php';

// Get from POSTED body.
$body = json_decode(file_get_contents('php://input'), true);

$username = $body['username'] ?? false;
$emailAddress = $body['email_address'] ?? false;
$password = $body['password'] ?? false;
$confirmPassword = $body['confirm_password'] ?? false;

if (!$username || !$emailAddress || !$password || !$confirmPassword) {
    exitWithMessage("Provide username, email_address, password and confirm_password in POST request");
}

try {
    $registerStatus = User::register($username, $emailAddress, $password, $confirmPassword);
    echo $registerStatus;
} catch (InvalidLoginException|InvalidRegistrationException $e) {
    exitWithMessage($e->getMessage());
}
