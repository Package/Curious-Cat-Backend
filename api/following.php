<?php
require_once './init.php';

$auth = new Authentication;
$user = $auth->authenticationNeeded()->getCurrentUser();

$id = $_GET['id'] ?? $user['id'];
$type = $_GET['type'] ?? 'following';
if (!$id) {
    exitWithMessage("No ID provided in request.", 401);
}

$followService = new FollowService;

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            $followService->follow($id, $user);
            exitWithMessage("User Followed.", 201);
            break;
        case 'DELETE':
            $followService->unfollow($id, $user);
            exitWithMessage("User Unfollowed.", 200);
            break;
        default:
            echo json_encode($followService->get($id, $type));
    }
} catch (Exception $e) {
    exitWithMessage($e->getMessage(), 500);
}