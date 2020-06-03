<?php
require_once './init.php';

$auth = new Authentication;
$user = $auth->required()->user();

try {
    $statService = new StatsService;

    $response = new stdClass;
    $response->newest = $statService->newest();
    $response->questions = $statService->topQuestions();
    $response->answers = $statService->topAnswers();
    echo json_encode($response);

} catch (Exception $e) {
    Response::error($e->getMessage());
}



