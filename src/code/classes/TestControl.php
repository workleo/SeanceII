<?php

namespace classes;

class TestControl
{
    /** @var  SessionStorage $seance */
    private $seance;
    private $currentPage;
    private $questionsArr;
    private $pictureSrc;
    private $pageTitle;
    private $userName;
    private $isReload;


    public function seanceExecute()
    {
        $this->seance = new SessionStorage('/SeanceII/src/');
        $this->seance->start();

        if (($this->seance->get('order') !== null) && ($this->seance->get('order') === 'wrong')) {
            {
                $this->isReload = true;
            }
        } else $this->isReload = false;

        $this->userName = $this->seance->get('user_name');

        $this->currentPage = $this->seance->get('current_page');


        if ($this->seance->get('sess_post') == null)
            $this->currentPage = '00';

        $this->analyseOfSource();
        $this->saveSeance();


    }

    public function getUserName()
    {
        return $this->userName;
    }


    public function getPictureSrc()
    {
        return $this->pictureSrc;
    }

    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    public function getQuestionsArr()
    {
        return $this->questionsArr;
    }

    public function getPageTitle()
    {
        return $this->pageTitle;
    }


    public function getSessionPage()
    {
        return $this->seance->get('current_page');
    }

    private function prepareQuestionsArray(string $id_page)
    {
        $fileName = $_SERVER['DOCUMENT_ROOT'] . 'src/res/test_txt/ask' . $id_page . '.txt';
        $json = file_get_contents($fileName);
        $this->questionsArr = json_decode($json, true);
    }


    private function preparePageParams(string $id_page)
    {
        $this->prepareQuestionsArray($id_page);
        $this->pictureSrc = '../res/img/pict' . $id_page . '.jpg';
        $this->pageTitle = "Картинка №" . $id_page;

        $this->registerPage();
        $this->saveSeance();
    }

    private function registerAnswer(string $id_page): bool
    {
        $seancePost = $this->seance->get('sess_post');

        if (($seancePost != null) && (isset($seancePost['option']))) {
            $this->prepareQuestionsArray($id_page);
            $answOptionsArray = $this->seance->get('answers');
            if ($answOptionsArray == null) {
                $answOptionsArray = [];
            }

            $key = '' . $seancePost['option'];
            $key = $this->numberToName($key);
            $newRecord = [$seancePost['option'] => $this->questionsArr[$key]];
            $answOptionsArray[$id_page] = $newRecord;

            if (!(is_array($answOptionsArray))) {
                die('Something went wrong.' . $answOptionsArray . ' is not an array.');
            }

            $this->seance->set('answers', $answOptionsArray);

            return true;
        } else {
            return false;
        }

    }

    private function registerPage()
    {
        $this->seance->set('current_page', $this->currentPage);
    }

    private function preparePage(string $id_page)
    {
        $this->currentPage = $id_page;
        $this->preparePageParams($id_page);
    }


    private function numberToName(int $nr): string
    {
        $name = $nr;
        if (strlen($name) == 1) {
            $name = '0' . $name;
        }
        return $name;
    }

    private function analyseOfSource()
    {
        $page = (int)$this->currentPage;

        if (!$this->isReload) {
            $nextPage = $this->numberToName($page + 1);
        } else {
            $nextPage = $this->currentPage;
        }

        switch (true) {
            case $page == 0:
                $this->preparePage("01");
                break;
            case ($page > 0 && $page < 10) ://01-09
                if ($this->registerAnswer($this->currentPage))
                    $this->preparePage($nextPage);
                break;
            case $page == 10:
                if ($this->registerAnswer($this->currentPage)) {//10
                    $this->currentPage = '11';
                    $this->registerPage();

                }
                break;
        }

    }


    private function saveSeance()
    {
        if (!$this->isReload) {
            $this->seance->set('order', 'wrong');
            $this->seance->flush();
        }
    }
}