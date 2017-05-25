<?php

namespace classes;


class IndexPageControl
{


    /*
       ** @var  SessionStorage $session *
    private $session;
    private $submitValue;


    public function session_seans()
    {
        $this->session = new SessionStorage('/SeanceII/src/');
        $this->submitValue = 'Пройти тест?';
        $this->session->start();
        $this->save_session();
    }


    private function save_session()
    {
        $this->session->set('current_page', "0");
        $this->session->flush();
    }
*/

    public function getTitle(): string
    {
        return "НАЧАЛО ТЕСТА";
    }

    public function getSubmitValue(): string
    {
        return 'Пройти тест?';//$this->submitValue;
    }

    public function getDescription(): string
    {
        $description = '';
        $fileName = $_SERVER['DOCUMENT_ROOT'] . '/src/res/test_txt/start.txt';
        if (file_exists($fileName) === true) {
            $json = file_get_contents($fileName);
            $description = json_decode($json, true)['description'];

            if ($description == null) {
                $description = '';
            } else {
                $pattern = ['/\n/', '/\t/'];
                $replacement = ["<br>", "&nbsp;&nbsp;&nbsp;&nbsp;"];
                $description = (preg_replace($pattern, $replacement, $description));
            }
        }
        return $description;
    }
}