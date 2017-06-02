<?php

namespace Code\Classes;

class FinalPageControl
{
    /** @var  SeanceStorage $seance */
    private $seance;
    private $pictureSrc;
    private $pageTitle;
    private $userName;

    public function seanceExecute()
    {
        $this->seance = new SeanceStorage('/SeanceII/src/');
        $this->seance->start();
        $this->userName = $this->seance->get('user_name');

        $this->seance->set('current_page', '12');
        $this->seance->flush();

        if ($this->userName === null) {
            $this->seance->close();
            header('Refresh: 0;url=../code/index.php');
        } else {
            $this->pictureSrc = '../res/img/logo.png';
            $this->pageTitle = 'Результат';
            $this->seance->flush();
        }
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
            'p1' => '../res/img/pict1.jpg',
            'p2' => '../res/img/pict2.jpg',
            'p3' => '../res/img/pict3.jpg',
            'p4' => '../res/img/pict4.jpg',
            'p5' => '../res/img/pict5.jpg',
            'p6' => '../res/img/pict6.jpg',
            'p7' => '../res/img/pict7.jpg',
            'p8' => '../res/img/pict8.jpg',
            'p9' => '../res/img/pict9.jpg',
            'p10' => '../res/img/pict10.jpg'
        );
    }

    public function getDescription(): string
    {
        $description = '';
        $fileName = __DIR__ . '/../../res/test_txt/end.txt';
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
                    if ($key !== 5) {
                        $answers['d' . $key] = $answersFile[$key][$id];
                    } else {
                        if ($answers['i1'] === $id) {
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