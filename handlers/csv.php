<?php

class FileHandler
{
    public $userFilename;
    public $uploadedFilePath;
    private $userFilePath = 'unsub_csv';

    public function uploadFile($userFile)
    {
        // Uploads CSV file to server
        // Takes a $_FILE for $userFile
        if ($userFile["list"]["error"] == UPLOAD_ERR_OK) {
            $tmp_name = $userFile["list"]["tmp_name"];
            $this->userFilename = $userFile["list"]["name"];
            $this->uploadedFilePath = $this->userFilePath . "/" . $this->userFilename;
            move_uploaded_file($tmp_name, $this->uploadedFilePath);
        }
    }

    public function getUserDataArrayFromFile()
    {
        $results = array();
        $csvFile = @fopen($this->getUploadedFilePath(), "r");
        while (($buffer = fgets($csvFile, 4096)) !== false)
        {
            $subscriberData = str_replace("\n", "", $buffer);
            $subscriberData = str_replace("\r", "", $subscriberData);
            $results[] = $subscriberData;
        }
        if (!feof($csvFile)) {
            echo "Error: unexpected fgets() fail\r\n";
        }
        fclose($csvFile);
        return $results;
    }

    public function getUserFilename()
    {
        return $this->userFilename;
    }

    public function getUploadedFilePath()
    {
        return $this->uploadedFilePath;
    }
}