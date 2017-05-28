<?php
namespace classes;

class FileStorage
{
    private $userName;
    private $fileName;
    protected $json;
    private $usersFolder;


    public function setUserName($userName)
    {
        $this->userName = $userName;
        $this->usersFolder = $_SERVER['DOCUMENT_ROOT'] . '/tmp/users';
        $this->fileName = $this->usersFolder . '/' . $this->userName;
    }


    private function checkUsersFolder()
    {
        if (!file_exists($this->usersFolder)) {
            mkdir($this->usersFolder, 0744, true) or die("Unable to create a users folder!");
        }
    }


    function start()
    {
        $this->json = null;
        try {
                if (file_exists($this->fileName) === true) {
                        $this->json = json_decode(file_get_contents($this->fileName),true);
                }
        } finally {
            $this->fileName = $this->usersFolder . '/' . $this->userName;
            $this->checkUsersFolder();
        }
    }

    function get($key)
    {

        $value = $this->json[$key];
        if ($value === null) return $value;

        if (preg_match("/(a|O|s|b)\:[0-9]*?((\:((\{?(.+)\})|(\"(.+)\"\;)))|(\;))/", $value) == 1) {
            return unserialize($value);
        } else {
            return $value;
        }
    }

    function set($key, $value)
    {
        if ($value === null) {
            $value = '';
        }

        if (is_object($value) || is_array($value)) {
            $this->json[$key] = serialize($value);
        } else {
            $this->json[$key] = $value;
        }
    }

    function remove($key)
    {

        foreach ($this->json as $id => $row) {
            if ($id === $key) {
                unset($this->json[$id]);
                break;
            }
        }
    }

    function flush()
    {
        file_put_contents($this->fileName, json_encode($this->json));
    }



    function close()
    {
        @unlink($this->fileName);
    }
}