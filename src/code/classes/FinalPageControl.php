<?php

namespace classes;

class FinalPageControl
{
    /** @var  SessionStorage $seance */
    private $seance;
    private $pictureSrc;
    private $pageTitle;
    private $userName;

    public function seanceExecute()
    {
        $this->seance = new SessionStorage('/SeanceII/src/');
        $this->seance->start();
        $this->userName=$this->seance->get('user_name');
        $this->pictureSrc = '../res/img/logo.png';
        $this->pageTitle = 'Результат';
        $this->seance->set('current_page', '12');

        $this->seance->flush();
    }

    public function getUserName()
    {
        return $this->userName;
    }

    public function getPictureSrc()
    {
        return $this->pictureSrc;
    }

    public function getPageTitle()
    {
        return $this->pageTitle;
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
        $fileName =__DIR__.'/../../res/test_txt/end.txt';
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
        $answers = [];
        $fileName = __DIR__ . '/../../res/test_txt/answers.txt';
        if (file_exists($fileName) === true) {
            $json = file_get_contents($fileName);
            $answersFile = unserialize(json_decode($json, true)['answers']);

            $answersOptions = $this->seance->get('answers');
            foreach ($answersOptions as $key => $item) {
                foreach ($item as $id => $val) {

                    $answers['t' . $key] = $answersFile[$key][0];
                    $answers['i' . $key] = $id;
                    $answers['a' . $key] = 'Ваш ответ :' . $val;
                    if ($key !== '05') {
                        $answers['d' . $key] = $answersFile[$key][$id];
                    } else {
                        if ($answers['i01'] === $id) {
                            $answers['d' . $key] = $answersFile[$key][2];
                        } else {
                            $answers['d' . $key] = $answersFile[$key][1];
                        }
                    }
                }
            }
        }
        return $answers;
    }




}