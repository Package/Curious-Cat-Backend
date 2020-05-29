<?php
require_once './init.php';

$auth = new Authentication;
$user = $auth->authenticationNeeded()->getCurrentUser();

$profileID = $_GET['id'] ?? $user['id'];
if (!$profileID) {
    exitWithMessage('No Profile ID provided.', 400);
}

$profileData = new stdClass;
$profileData->info = Profile::info($profileID, $user);
$profileData->stats = Profile::stats($profileID);
$profileData->timeline = Profile::timeline($profileID);
echo json_encode($profileData);