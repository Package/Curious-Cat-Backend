<?php
require_once './init.php';

$id = $_GET['id'] ?? false;

$body = json_decode(file_get_contents('php://input'), true);
$label = $body['label'] ?? false;
$targetUser = $body['target_user'] ?? false;
$nameHidden = $body['name_hidden'] ?? false;

$auth = new Authentication;
$user = $auth->authenticationNeeded()->getCurrentUser();

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            Question::create($label, $targetUser, $user, $nameHidden);
            exitWithMessage('Question Created.', 201);
            break;
        case 'DELETE':
            Question::delete($id, $user);
            exitWithMessage('Question Deleted.', 200);
            break;
        default:
            $questions = Question::get($id);
            echo json_encode($questions);
    }
} catch (Exception $e) {
    exitWithMessage($e->getMessage(), 500);
}




