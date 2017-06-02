<?php

namespace Code\Classes;

class UserStorage extends Storage
{
    private $userName;
    private $userFileName;
    private $usersFolder;
    /** @var JsonFileIO $jsonFileIO */
    private $jsonFileIO;


    public function __construct()
    {
        $this->jsonFileIO = new JsonFileIO();
    }


    private function setUserName($userName)
    {
        $this->userName = $userName;
        $this->usersFolder = __DIR__ . '/../../../tmp/users';
        $this->userFileName = $this->usersFolder . '/' . $this->userName;

    }


    private function checkUsersFolder()
    {
        if (!$this->jsonFileIO->isFolderExist($this->usersFolder)) {
            die("Unable to create a users folder!");
        }
    }


    function start($userName)
    {
        $this->setUserName($userName);

        $this->json = null;
        try {
            if ($this->jsonFileIO->isFileExist($this->userFileName) === true) {
                $this->json = $this->jsonFileIO->getJsonFromFile($this->userFileName);
            }

        } finally {
            $this->userFileName = $this->usersFolder . '/' . $this->userName;
            $this->checkUsersFolder();
        }
    }


    function flush()
    {
        $this->jsonFileIO->flushJsonToFile($this->userFileName, $this->json);
    }


    function close()
    {
        $this->jsonFileIO->deleteFile($this->userFileName);
    }
}