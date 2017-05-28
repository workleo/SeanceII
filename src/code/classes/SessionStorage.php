<?php
/**
 * Class SessionStorage is a special class for a sessions imitation
 */

namespace classes;

class SessionStorage extends FileStorage
{
    private $sessionId;
    private $sessionFolder;
    private $cookiePath;
    const SESSION_ID = "SEANCE_ID";

    private $fileName;


    function __construct($cookiePath = '/')
    {
        $this->sessionId = '';
        $this->fileName = '';
        $this->cookiePath = $cookiePath;
        $this->sessionFolder = $_SERVER['DOCUMENT_ROOT'] . '/tmp/seance';
    }


    private function pseudoGuid4(): string
    {
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }


    private function isCorrectSession()
    {
        return (
            ($this->get('HTTP_USER_AGENT') === $_SERVER['HTTP_USER_AGENT'])
            && ($this->get('REMOTE_ADDR') === $_SERVER['REMOTE_ADDR'])
            && ($this->get('HTTP_X_FORWARDED_FOR') == getenv('HTTP_X_FORWARDED_FOR'))
        );
    }

    private function setSessionParams()
    {
        $this->set('HTTP_USER_AGENT', $_SERVER['HTTP_USER_AGENT']);
        $this->set('REMOTE_ADDR', $_SERVER['REMOTE_ADDR']);
        $this->set('HTTP_X_FORWARDED_FOR', getenv('HTTP_X_FORWARDED_FOR'));
        $this->flush();
    }


    private function checkSessionFolder()
    {
        if (!file_exists($this->sessionFolder)) {
            mkdir($this->sessionFolder, 0744, true) or die("Unable to create a folder of session!");
        }
    }


    function start()
    {
        $this->json = null;
        try {
            if (isset($_COOKIE[self::SESSION_ID])) {
                $this->fileName = $this->sessionFolder . '/' . $_COOKIE[self::SESSION_ID];
                if (file_exists($this->fileName) === true) {

                    try {
                        $this->json = json_decode(file_get_contents($this->fileName),true);
                        if (!$this->isCorrectSession()) {
                            $message = 'COOKIE:' . $_COOKIE[self::SESSION_ID] . "\n"
                                . 'server:' . $_SERVER['HTTP_USER_AGENT'] . "\n"
                                . 'this  :' . $this->get('HTTP_USER_AGENT') . "\n"
                                . 'server:' . $_SERVER['REMOTE_ADDR'] . "\n"
                                . 'this  :' . $this->get('REMOTE_ADDR') . "\n"
                                . 'server:' . $_SERVER['HTTP_X_FORWARDED_FOR'] . "\n"
                                . 'this  :' . $this->get('HTTP_X_FORWARDED_FOR') . "\n";
                            error_log($message, 3, $this->sessionFolder . '/' . "errors.log");
                            die('It"s a fake session');
                        }
                    } finally {
                        $this->close();
                    }
                }
            }
        } finally {
            $this->sessionId = $this->pseudoGuid4();
            $this->fileName = $this->sessionFolder . '/' . $this->sessionId;
            $this->checkSessionFolder();
            setcookie(self::SESSION_ID, $this->sessionId, 0, $this->cookiePath);
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
        file_put_contents($this->fileName,json_encode( $this->json));
    }

}