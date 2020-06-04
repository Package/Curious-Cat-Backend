<?php

abstract class FileService extends BaseService
{
    /**
     * An array containing any names of the files which have been POSTED to this service.
     *
     * @var array
     */
    protected $filenames = [];

    /**
     * When processing the uploaded files, if writing to disk an array containing a mapping of what each
     * file should be saved as.
     * $uploadFilenames[0] should correspond to $filenames[0] etc..
     *
     * @var array
     */
    protected $uploadFilenames = [];

    /**
     * The path on the server of where to write the uploaded file(s).
     * If this directory does not exist it will be created.
     *
     * @var string
     */
    protected $uploadDirectory = '';

    /**
     * Process the file upload(s).
     *
     * @return bool
     */
    public abstract function upload() : bool;

    /**
     * Is the current extension on the file valid for this service?
     *
     * @param string $extension
     * @return bool
     */
    public abstract function isExtensionValid(string $extension) : bool;

    /**
     * Check whether all files that have been uploaded are valid.
     *
     * @return bool
     */
    public function isFileValid(): bool
    {
        foreach ($this->filenames as $f) {
            $fileData = $_FILES[$f];

            // No file has been uploaded.
            if ($fileData["size"] == 0 || !$fileData["tmp_name"]) {
                return false;
            }

            // Must have a valid extension
            $extension = pathinfo($fileData["name"], PATHINFO_EXTENSION);
            if (!$this->isExtensionValid($extension)) {
                return false;
            }
        }

        /*
         * If we reach down here then all files that have been uploaded are considered 'valid',
         * so the file service can proceed with uploading.
         */
        return true;
    }

    /**
     * @param array $filenames
     */
    public function setFilenames(array $filenames): void
    {
        $this->filenames = $filenames;
    }

    /**
     * @param array $uploadFilenames
     */
    public function setUploadFilenames(array $uploadFilenames): void
    {
        $this->uploadFilenames = $uploadFilenames;
    }

    /**
     * @param string $uploadDirectory
     */
    public function setUploadDirectory(string $uploadDirectory): void
    {
        $this->uploadDirectory = $uploadDirectory;
    }
}