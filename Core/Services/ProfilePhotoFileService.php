<?php

class ProfilePhotoFileService extends FileService
{
    /**
     * Process the uploads.
     *
     * @return bool
     */
    public function upload(): bool
    {
        // If the target output directory doesn't exist already, create it.
        if (!file_exists($this->uploadDirectory)) {
            mkdir($this->uploadDirectory);
        }

        $successfulUpload = true;

        // Process each file, recording if any were un-successful
        for ($x = 0; $x < count($this->filenames); $x++) {
            $currentFile = $_FILES[$this->filenames[$x]]["tmp_name"];
            $destination = $this->uploadDirectory . $this->uploadFilenames[$x];

            if (!move_uploaded_file($currentFile, $destination)) {
                $successfulUpload = false;
            }
        }

        return $successfulUpload;
    }

    /**
     * Is this type of file valid for this service?
     *
     * @param string $extension
     * @return bool
     */
    public function isExtensionValid(string $extension): bool
    {
        return in_array($extension, ['jpg', 'png', 'jpeg', 'gif']);
    }
}