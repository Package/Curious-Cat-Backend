<?php
require_once './init.php';

$auth = new Authentication;
$user = $auth->authenticationNeeded()->getCurrentUser();

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'PUT':
            Notification::read($user['id']);
            exitWithMessage("Notifications Read.", 200);
            break;
        default:
            echo json_encode(Notification::get($user['id']));
    }
} catch (Exception $e) {
    exitWithMessage($e->getMessage(), 500);
}