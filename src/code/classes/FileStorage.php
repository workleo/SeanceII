<?php
namespace classes;

class FileStorage extends  Storage
{
    private $userName;
    private $userFileName;
    private $usersFolder;


    private function setUserName($userName)
    {
        $this->userName = $userName;
        $this->usersFolder = __DIR__ . '/../../../tmp/users';
        $this->userFileName = $this->usersFolder . '/' . $this->userName;
    }


    private function checkUsersFolder()
    {
        if (!file_exists($this->usersFolder)) {
            mkdir($this->usersFolder, 0744, true) or die("Unable to create a users folder!");
        }
    }


    function start($userName)
    {
        $this->setUserName($userName);

        $this->json = null;
        try {
                if (file_exists($this->userFileName) === true) {
                        $this->json = json_decode(file_get_contents($this->userFileName),true);
                }
        } finally {
            $this->userFileName = $this->usersFolder . '/' . $this->userName;
            $this->checkUsersFolder();
        }
    }


    function flush()
    {
        file_put_contents($this->userFileName, json_encode($this->json));
    }



    function close()
    {
        @unlink($this->userFileName);
    }
}