<?php
require_once './init.php';

// Get from POSTED body.
$body = json_decode(file_get_contents('php://input'), true);

$userOrEmail = $body['userOrEmail'] ?? false;
$password = $body['password'] ?? false;
if (!$userOrEmail || !$password) {
    exitWithMessage("Provide userOrEmail and password in POST request");
}

try {
    $loginStatus = User::login($userOrEmail, $password);
    echo $loginStatus;
} catch (InvalidLoginException $e) {
    exitWithMessage($e->getMessage());
}

