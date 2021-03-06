<?php
require_once './init.php';

// Get from POSTED body.
$body = json_decode(file_get_contents('php://input'), true);
$username = $body['username'] ?? false;
$emailAddress = $body['email_address'] ?? false;
$password = $body['password'] ?? false;
$confirmPassword = $body['confirm_password'] ?? false;

if (!$username || !$emailAddress || !$password || !$confirmPassword) {
    Response::error("Please fill in all fields.", 400);
}

$userService = new UserService;
try {
    echo $userService->register($username, $emailAddress, $password, $confirmPassword);
} catch (InvalidLoginException|InvalidRegistrationException $e) {
    Response::error($e->getMessage(), 400);
}
