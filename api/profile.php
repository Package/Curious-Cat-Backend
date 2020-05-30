<?php
require_once './init.php';

$auth = new Authentication;
$user = $auth->authenticationNeeded()->getCurrentUser();

$profileID = $_GET['id'] ?? $user['id'];
if (!$profileID) {
    exitWithMessage('No Profile ID provided.', 400);
}

$profileService = new ProfileService;

$profileData = new stdClass;
$profileData->info = $profileService->info($profileID, $user);
$profileData->stats = $profileService->stats($profileID);
$profileData->timeline = $profileService->timeline($profileID);
echo json_encode($profileData);