<?php
require_once './init.php';

$answerID = $_GET['id'] ?? false;
$getQuestion = $_GET['question'] ?? false;

$body = json_decode(file_get_contents('php://input'), true);
$label = $body['label'] ?? false;
$postQuestion = $body['question_id'] ?? false;

$auth = new Authentication;
$user = $auth->authenticationNeeded()->getCurrentUser();

$answerService = new AnswerService;

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            $answerService->create($postQuestion, $user, $label);
            exitWithMessage("Answer Created.", 201);
            break;
        case 'DELETE':
            $answerService->delete($answerID, $user);
            exitWithMessage("Answer Deleted.", 200);
            break;
        default:
            $answers = $answerService->forQuestion($getQuestion);
            echo json_encode($answers);
    }
} catch (Exception $e) {
    exitWithMessage($e->getMessage(), 500);
}



