<?php
/**
 * Class SeanceStorage is a special class for a seances imitation
 */

namespace classes;

class SeanceStorage extends Storage
{
    private $seanceId;
    private $seanceFolder;
    private $cookiePath;
    const SEANCE_ID = "SEANCE_ID";
    private $seanceFileName;


    function __construct($cookiePath = '/')
    {
        $this->seanceId = '';
        $this->seanceFileName = '';
        $this->cookiePath = $cookiePath;
        $this->seanceFolder = __DIR__ . '/../../../tmp/seance';
    }


    private function pseudoGuid4(): string
    {
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }


    private function isCorrectSeance()
    {
        return (
            ($this->get('HTTP_USER_AGENT') === $_SERVER['HTTP_USER_AGENT'])
            && ($this->get('REMOTE_ADDR') === $_SERVER['REMOTE_ADDR'])
            && ($this->get('HTTP_X_FORWARDED_FOR') == getenv('HTTP_X_FORWARDED_FOR'))
        );
    }

    private function setSeanceParams()
    {
        $this->set('HTTP_USER_AGENT', $_SERVER['HTTP_USER_AGENT']);
        $this->set('REMOTE_ADDR', $_SERVER['REMOTE_ADDR']);
        $this->set('HTTP_X_FORWARDED_FOR', getenv('HTTP_X_FORWARDED_FOR'));
        $this->flush();
    }


    private function checkSeanceFolder()
    {
        if (!file_exists($this->seanceFolder)) {
            mkdir($this->seanceFolder, 0744, true) or die("Unable to create a folder of seance!");
        }
    }


    function start()
    {
        $this->json = null;
        try {
            if (isset($_COOKIE[self::SEANCE_ID])) {
                $this->seanceFileName = $this->seanceFolder . '/' . $_COOKIE[self::SEANCE_ID];
                if (file_exists($this->seanceFileName) === true) {

                    try {
                        $this->json = json_decode(file_get_contents($this->seanceFileName),true);
                        if (!$this->isCorrectSeance()) {
                            $message = 'COOKIE:' . $_COOKIE[self::SEANCE_ID] . "\n"
                                . 'server:' . $_SERVER['HTTP_USER_AGENT'] . "\n"
                                . 'this  :' . $this->get('HTTP_USER_AGENT') . "\n"
                                . 'server:' . $_SERVER['REMOTE_ADDR'] . "\n"
                                . 'this  :' . $this->get('REMOTE_ADDR') . "\n"
                                . 'server:' . $_SERVER['HTTP_X_FORWARDED_FOR'] . "\n"
                                . 'this  :' . $this->get('HTTP_X_FORWARDED_FOR') . "\n";
                            error_log($message, 3, $this->seanceFolder . '/' . "errors.log");
                            die('It"s a fake seance');
                        }
                    } finally {
                        $this->close();
                    }
                }
            }
        } finally {
            $this->seanceId = $this->pseudoGuid4();
            $this->seanceFileName = $this->seanceFolder . '/' . $this->seanceId;
            $this->checkSeanceFolder();
            setcookie(self::SEANCE_ID, $this->seanceId, 0, $this->cookiePath);
            $this->setSeanceParams();
        }
    }

    function close()
    {
        @unlink($this->seanceFileName);
        setcookie(self::SEANCE_ID, $this->seanceId, time() - 3600);
    }

  function flush()
    {
        file_put_contents($this->seanceFileName,json_encode( $this->json));
    }

}