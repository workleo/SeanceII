<?php
/**
 * Class SessionStorage is a special class for a sessions imitation
 */

namespace classes;

class SessionStorage extends FileStorage
{
    private $sessionId;
    private $session_folder;
    private $cookie_path;
    const SESSION_ID = "SEANCE_ID";

    private $fileName;


    function __construct($cookie_path = '/')
    {
        $this->sessionId = '';
        $this->fileName = '';
        $this->$cookie_path = $cookie_path;
        $this->session_folder = $_SERVER['DOCUMENT_ROOT'] . 'tmp/seance';
    }


    private function pseudoGuid4(): string
    {
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }


    private function isCorrectSession()
    {
        if (
            ($this->get('HTTP_USER_AGENT') === $_SERVER['HTTP_USER_AGENT'])
            && ($this->get('REMOTE_ADDR') === $_SERVER['REMOTE_ADDR'])
            && ($this->get('HTTP_X_FORWARDED_FOR') == getenv('HTTP_X_FORWARDED_FOR'))// $_SERVER['HTTP_X_FORWARDED_FOR'])
        )
            return true;
        else
            return false;
    }

    private function setSessionParams()
    {
        $this->set('HTTP_USER_AGENT', $_SERVER['HTTP_USER_AGENT']);
        $this->set('REMOTE_ADDR', $_SERVER['REMOTE_ADDR']);
        $this->set('HTTP_X_FORWARDED_FOR', getenv('HTTP_X_FORWARDED_FOR'));//$_SERVER['HTTP_X_FORWARDED_FOR']);
        $this->flush();
    }


    private function checkSessionFolder()
    {
        if (!file_exists($this->session_folder)) {
            mkdir($this->session_folder, 0744, true) or die("Unable to create a folder of session!");
        }
    }


    function start()
    {
        $this->json = null;
        try {
            if (isset($_COOKIE[self::SESSION_ID])) {
                $this->fileName = $this->session_folder . '/' . $_COOKIE[self::SESSION_ID];
                if (file_exists($this->fileName) === true) {

                    try {
                        $this->json = file_get_contents($this->fileName);
                        if (!$this->isCorrectSession()) {

                            print 'COOKIE:' . $_COOKIE[self::SESSION_ID] . '<br>';
                            print 'server:' . $_SERVER['HTTP_USER_AGENT'] . '<br>';
                            print 'this  :' . $this->get('HTTP_USER_AGENT') . '<br>';
                            print 'server:' . $_SERVER['REMOTE_ADDR'] . '<br>';
                            print 'this  :' . $this->get('REMOTE_ADDR') . '<br>';
                            print 'server:' . $_SERVER['HTTP_X_FORWARDED_FOR'] . '<br>';
                            print 'this  :' . $this->get('HTTP_X_FORWARDED_FOR') . '<br>';

                            die('It"s a fake session');
                        }
                    } finally {
                        $this->close();
                    }
                }
            }
        } finally {
            $this->sessionId = $this->pseudoGuid4();
            $this->fileName = $this->session_folder . '/' . $this->sessionId;
            $this->checkSessionFolder();
            setcookie(self::SESSION_ID, $this->sessionId, 0, $this->cookie_path);
            $this->setSessionParams();
        }
    }

    function close()
    {
        @unlink($this->fileName);
        setcookie(self::SESSION_ID, $this->sessionId, time() - 3600);
    }

    function flush()
    {
        file_put_contents($this->fileName, $this->json);
    }

}