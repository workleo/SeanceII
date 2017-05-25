<?php

namespace classes;

class FinalPageControl
{
    /** @var  SessionStorage $session */
    private $session;
    private $picture_src;
    private $page_title;
    private $userName;

    public function session_seans()
    {
        $this->session = new SessionStorage('/SeanceII/src/');
        $this->session->start();
        $this->userName=$this->session->get('user_name');
        $this->picture_src = '../res/img/logo.png';
        $this->page_title = 'Результат';
        $this->session->set('current_page', '12');

        $this->session->flush();
    }

    public function getUserName()
    {
        return $this->userName;
    }

    public function getPictureSrc()
    {
        return $this->picture_src;
    }

    public function getPageTitle()
    {
        return $this->page_title;
    }


    public function getPictureSrcArr()
    {
        return array(
            'p01' => '../res/img/pict01.jpg',
            'p02' => '../res/img/pict02.jpg',
            'p03' => '../res/img/pict03.jpg',
            'p04' => '../res/img/pict04.jpg',
            'p05' => '../res/img/pict05.jpg',
            'p06' => '../res/img/pict06.jpg',
            'p07' => '../res/img/pict07.jpg',
            'p08' => '../res/img/pict08.jpg',
            'p09' => '../res/img/pict09.jpg',
            'p10' => '../res/img/pict10.jpg'
        );
    }

    public function getDescription(): string
    {
        $description = '';
        $fileName = $_SERVER['DOCUMENT_ROOT'] . '/src/res/test_txt/end.txt';
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

    public function getAnswers()
    {
        $answ_arr = [];
        $fileName = $_SERVER['DOCUMENT_ROOT'] . '/src/res/test_txt/answers.txt';
        if (file_exists($fileName) === true) {
            $json = file_get_contents($fileName);
            $file_answ = unserialize(json_decode($json, true)['answers']);

            $answ_options = $this->session->get('answers');
            foreach ($answ_options as $key => $item) {
                foreach ($item as $id => $val) {

                    $answ_arr['t' . $key] = $file_answ[$key][0];
                    $answ_arr['i' . $key] = $id;
                    $answ_arr['a' . $key] = 'Ваш ответ :' . $val;
                    if ($key !== '05') {
                        $answ_arr['d' . $key] = $file_answ[$key][$id];
                    } else {
                        if ($answ_arr['i01'] === $id) {
                            $answ_arr['d' . $key] = $file_answ[$key][2];
                        } else {
                            $answ_arr['d' . $key] = $file_answ[$key][1];
                        }
                    }
                }
            }
        }
        return $answ_arr;
    }




}