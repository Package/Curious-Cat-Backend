<?php
require_once './init.php';

$auth = new Authentication;
$user = $auth->authenticationNeeded()->getCurrentUser();

$id = $_GET['id'] ?? $user['id'];
$type = $_GET['type'] ?? 'following';
if (!$id) {
    exitWithMessage("No ID provided in request.", 401);
}

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            Following::follow($id, $user['id']);
            exitWithMessage("User Followed.", 201);
            break;
        case 'DELETE':
            Following::unfollow($id, $user['id']);
            exitWithMessage("User Unfollowed.", 200);
            break;
        default:
            echo json_encode(Following::get($id, $type));
    }
} catch (Exception $e) {
    exitWithMessage($e->getMessage(), 500);
}