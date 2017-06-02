<?php


namespace Code\Classes;


class JsonFileIO
{
    public function isFolderExist(string $folderName): bool
    {
        if (!file_exists($folderName)) {
            if (mkdir($folderName, 0744, true) === false) {
                return false;
            }
        }
        return true;
    }


    public function isFileExist(string $fileName): bool
    {
        if (file_exists($fileName) === false) {
            return false;
        }
        return true;
    }


    public function getJsonFromFile(string $fileName)
    {
        return json_decode(file_get_contents($fileName), true);
    }

    public function deleteFile(string $fileName): bool
    {
        if (@unlink($fileName) === false) {
            return false;
        }
        return true;
    }

    public function flushJsonToFile( string $fileName, $json): bool
    {
        if (file_put_contents($fileName, json_encode($json)) === false) {
            return false;
        }
        return true;
    }
}