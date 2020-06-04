<?php
require_once './init.php';

$user = (new Authentication())->required()->user();

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
    switch ($_SERVER["REQUEST_METHOD"]) {
        case "PUT":
            echo json_encode($userService->update($user, $username, $emailAddress, $password, $confirmPassword));
            break;
        default:
            echo json_encode($userService->get($user));
            break;
    }
} catch (OperationFailedException|InvalidRegistrationException $e) {
    Response::error($e->getMessage(), 400);
}
