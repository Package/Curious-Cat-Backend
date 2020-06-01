<?php
require_once './init.php';

$auth = new Authentication;
$user = $auth->authenticationNeeded()->getCurrentUser();

$id = $_GET['id'] ?? false;

$notificationService = new NotificationService;
try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'PUT':
            $notificationService->read($user['id'], $id);
            exitWithMessage("Notifications Read.", 200);
            break;
        default:
            echo json_encode($notificationService->get($user['id']));
    }
} catch (Exception $e) {
    exitWithMessage($e->getMessage(), 500);
}