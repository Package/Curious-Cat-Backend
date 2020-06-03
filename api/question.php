<?php
require_once './init.php';

$id = $_GET['id'] ?? false;

$body = json_decode(file_get_contents('php://input'), true);
$label = $body['label'] ?? false;
$targetUser = $body['target_user'] ?? false;
$nameHidden = $body['name_hidden'] ?? false;

$auth = new Authentication;
$user = $auth->required()->user();

$questionService = new QuestionService;

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            $questionService->create($label, $targetUser, $user, $nameHidden);
            Response::success('Question Created.', 201);
            break;
        case 'DELETE':
            $questionService->delete($id, $user);
            Response::success('Question Deleted.');
            break;
        default:
            echo json_encode($questionService->get($id));
    }
} catch (Exception $e) {
    Response::error($e->getMessage());
}




