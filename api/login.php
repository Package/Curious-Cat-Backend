<?php
require_once './init.php';

$body = json_decode(file_get_contents('php://input'), true);
$userOrEmail = $body['userOrEmail'] ?? false;
$password = $body['password'] ?? false;
if (!$userOrEmail || !$password) {
    Response::error("Please enter username and password.", 400);
}

$userService = new UserService;
try {
    echo $userService->login($userOrEmail, $password);
} catch (InvalidLoginException $e) {
    Response::error($e->getMessage());
}

