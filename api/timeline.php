<?php
require_once './init.php';

$auth = new Authentication;
$user = $auth->required()->user();

$timelineService = new TimelineService;
echo json_encode($timelineService->timeline($user));