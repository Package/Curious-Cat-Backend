<?php
require_once './init.php';

$body = json_decode(file_get_contents('php://input'), true);
$user = validateAccessToken();

$profileID = $_GET['id'];
if (!$profileID) {
    exitWithMessage('No Profile ID provided.', 400);
}

$profileData = new stdClass;
$profileData->info = Profile::info($profileID, $user);
$profileData->stats = Profile::stats($profileID);
$profileData->timeline = Profile::timeline($profileID, $user);

echo json_encode($profileData);