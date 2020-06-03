<?php
require_once './init.php';

$auth = new Authentication;
$user = $auth->required()->user();

$query = $_GET['query'] ?? false;
if (!$query || strlen($query) == 0) {
    Response::error('No query provided.', 400);
}

$searchResults = new stdClass;
$searchResults->query = $query;

$searchService = new QuestionSearchService;
$searchResults->questions = $searchService->search($query);

$searchService = new UserSearchService;
$searchResults->users = $searchService->search($query);

echo json_encode($searchResults);
