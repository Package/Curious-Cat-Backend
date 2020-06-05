<?php
require_once './init.php';

$user = (new Authentication())->required()->user();
$userService = new UserService;

switch ($_SERVER["REQUEST_METHOD"]) {
    case "POST":
        handleProfileUpload($user);
        $userService->setPhoto($user, "/assets/static/images/user_content/{$user['id']}/profile.jpg");
        Response::success('Profile Photo Uploaded.');
        break;
    case "DELETE":
        handlePhotoDelete($user);
        $userService->setPhoto($user, null);
        Response::success('Profile Photo Removed.');
        break;
    default:
        break;
}

/**
 * Handles uploading the file(s) to the server.
 *
 * @param array $user
 */
function handleProfileUpload(array $user) : void {
    $fileService = new ProfilePhotoFileService;
    $fileService->setFilenames(["profile_photo"]);
    $fileService->setUploadDirectory("../assets/static/images/user_content/{$user['id']}/");
    $fileService->setUploadFilenames(["profile.jpg"]);

    if (!$fileService->isFileValid()) {
        Response::error("No photo selected, or it is not in the correct format (jpg, jpeg, png, gif).", 400);
    }

    if (!$fileService->upload()) {
        Response::error("Could not upload photo.");
    }
}

/**
 * Handles deleting the file from the server.
 * @param array $user
 */
function handlePhotoDelete(array $user) : void {
    $pathAndFilename = "../assets/static/images/user_content/{$user['id']}/profile.jpg";

    if (file_exists($pathAndFilename)) {
        unlink($pathAndFilename);
    }
}