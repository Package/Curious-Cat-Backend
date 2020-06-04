<?php
require_once './init.php';

$user = (new Authentication())->required()->user();
$userService = new UserService;

switch ($_SERVER["REQUEST_METHOD"]) {
    case "PUT":
        handleUpdateRequest($user, $userService);
        break;
    default:
        echo json_encode($userService->get($user));
        break;
}

function handleUpdateRequest(array $user, UserService $userService) : void {
    try {
        $body = json_decode(file_get_contents('php://input'), true);
        $action = $body["action"] ?? false;
        $username = $body['username'] ?? false;
        $emailAddress = $body['email_address'] ?? false;
        $password = $body['password'] ?? false;
        $confirmPassword = $body['confirm_password'] ?? false;

        switch ($action) {
            case "update_details":
                if (!$username || !$emailAddress) {
                    Response::error("Please fill in all fields.", 400);
                }
                echo json_encode($userService->updateDetails($user, $username, $emailAddress));
                break;
            case "update_password":
                if (!$password || !$confirmPassword) {
                    Response::error("Please fill in all fields.", 400);
                }
                echo json_encode($userService->updatePassword($user, $password, $confirmPassword));
                break;
            default:
                throw new OperationFailedException("Unable to process request.");
        }
    } catch (OperationFailedException|InvalidRegistrationException $e) {
        Response::error($e->getMessage(), 400);
    }
}