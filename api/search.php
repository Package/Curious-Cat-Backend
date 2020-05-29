<?php
require_once './init.php';

$auth = new Authentication;
$user = $auth->authenticationNeeded()->getCurrentUser();

$query = $_GET['query'] ?? false;
if (!$query || strlen($query) == 0) {
    exitWithMessage('No query provided.', 400);
}

$searchResults = new stdClass;
$searchResults->users = UserSearchResults::search($query);
$searchResults->questions = QuestionSearchResults::search($query);
echo json_encode($searchResults);
