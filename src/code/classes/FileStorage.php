<?php
namespace classes;

class FileStorage
{
    private $userName;
    private $fileName;
    protected $json;
    private $users_folder;


    public function setUserName($userName)
    {
        $this->userName = $userName;
        $this->users_folder = $_SERVER['DOCUMENT_ROOT'] . 'tmp/users';
        $this->fileName = $this->users_folder . '/' . $this->userName;
    }


    private function checkUsersFolder()
    {
        if (!file_exists($this->users_folder)) {
            mkdir($this->users_folder, 0744, true) or die("Unable to create a users folder!");
        }
    }



    function start()
    {
        $this->json = null;
        try {
                if (file_exists($this->fileName) === true) {
                    try {
                        $this->json = file_get_contents($this->fileName);
                    } finally {
                        $this->close();
                    }
                }
        } finally {
            $this->fileName = $this->users_folder . '/' . $this->userName;
            $this->checkUsersFolder();
        }
    }


    function get($key)
    {
        $temp = json_decode($this->json, true);
        $value = $temp[$key];
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
        $temp = json_decode($this->json, true);
        if (is_object($value) || is_array($value)) {
            $temp[$key] = serialize($value);
        } else {
            $temp[$key] = $value;
        }
        $this->json = json_encode($temp);
    }

    function remove($key)
    {
        $find = false;
        $temp = json_decode($this->json, true);
        foreach ($temp as $id => $row) {
            if ($id === $key) {
                unset($temp[$id]);
                $find = true;
                break;
            }
        }
        if ($find) $this->json = json_encode($temp);
    }

    function flush()
    {
        file_put_contents($this->fileName, $this->json);
    }

    function close()
    {
        @unlink($this->fileName);
    }
}