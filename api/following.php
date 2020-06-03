<?php
require_once './init.php';

$auth = new Authentication;
$user = $auth->required()->user();

$id = $_GET['id'] ?? $user['id'];
$type = $_GET['type'] ?? 'following';
if (!$id) {
    Response::error("No ID provided in request.", 401);
}

$followService = new FollowService;

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            $followService->follow($id, $user);
            Response::success("User Followed.", 201);
            break;
        case 'DELETE':
            $followService->unfollow($id, $user);
            Response::success("User Unfollowed.");
            break;
        default:
            echo json_encode($followService->get($id, $type));
    }
} catch (Exception $e) {
    Response::error($e->getMessage());
}