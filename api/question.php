<?php
require_once './init.php';

$body = json_decode(file_get_contents('php://input'), true);

$id = $_GET['id'] ?? false;
$label = $body['label'] ?? false;
$targetUser = $body['target_user'] ?? false;
$nameHidden = $body['name_hidden'] ?? false;
$user = validateAccessToken();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        try {
            Question::create($label, $targetUser, $user, $nameHidden);
            exitWithMessage('Question Created.', 201);
        } catch (OperationFailedException $e) {
            exitWithMessage($e->getMessage());
        }
        echo 'Post request';
        break;
    case 'DELETE':
        try {
            Question::delete($id, $user);
            exitWithMessage('Question Deleted.', 200);
        } catch (OperationFailedException $e) {
            exitWithMessage($e->getMessage());
        }
        break;
    default:
        $questions = Question::get($id);
        echo json_encode($questions);
        break;
}



