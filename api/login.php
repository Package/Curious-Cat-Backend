<?php
require_once './init.php';

$body = json_decode(file_get_contents('php://input'), true);
$userOrEmail = $body['userOrEmail'] ?? false;
$password = $body['password'] ?? false;
if (!$userOrEmail || !$password) {
    exitWithMessage("Please enter username and password.");
}

$userService = new UserService;
try {
    echo $userService->login($userOrEmail, $password);
} catch (InvalidLoginException $e) {
    exitWithMessage($e->getMessage(), 500);
}

