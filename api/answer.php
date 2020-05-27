<?php
require_once './init.php';

$user = validateAccessToken();

$questionID = $_GET['id'] ?? false;
if (!$questionID) {
    exitWithMessage('Question ID missing from request.');
}

$answers = Answer::forQuestion($questionID);
echo json_encode($answers );
