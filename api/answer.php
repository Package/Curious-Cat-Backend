<?php
require_once './init.php';

$answerID = $_GET['id'] ?? false;
$getQuestion = $_GET['question'] ?? false;

$body = json_decode(file_get_contents('php://input'), true);
$user = validateAccessToken();
$label = $body['label'] ?? false;
$postQuestion = $body['question_id'] ?? false;

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        try {
            Answer::create($postQuestion, $user, $label);
            exitWithMessage("Answer Created.", 201);
        } catch (OperationFailedException|UnauthorizedException $e) {
            exitWithMessage($e->getMessage());
        }
        break;
    case 'DELETE':
        try {
            Answer::delete($answerID, $user);
            exitWithMessage("Answer Deleted.", 200);
        } catch (OperationFailedException|UnauthorizedException  $e) {
            exitWithMessage($e->getMessage());
        }
        break;
    default:
        $answers = Answer::forQuestion($getQuestion);
        echo json_encode($answers);
}



