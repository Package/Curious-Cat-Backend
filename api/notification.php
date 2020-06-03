<?php
require_once './init.php';

$auth = new Authentication;
$user = $auth->required()->user();

$id = $_GET['id'] ?? false;

$notificationService = new NotificationService;
try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'PUT':
            $notificationService->read($user['id'], $id);
            Response::success("Notifications Read.");
            break;
        default:
            echo json_encode($notificationService->get($user['id']));
    }
} catch (Exception $e) {
    Response::error($e->getMessage());
}