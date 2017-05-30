<?php

namespace classes;

class TestControl
{
    /** @var  SeanceStorage $seance */
    private $seance;
    private $currentPage;
    private $questionsArr;
    private $pictureSrc;
    private $pageTitle;
    private $userName;
    private $isReload;


    public function seanceExecute()
    {
        $this->seance = new SeanceStorage('/SeanceII/src/');
        $this->seance->start();

        if (($this->seance->get('order') !== null) && ($this->seance->get('order') === 'wrong')) {
            {
                $this->isReload = true;
            }
        } else $this->isReload = false;

        $this->userName = $this->seance->get('user_name');

        $this->currentPage = $this->seance->get('current_page');


        if ($this->seance->get('postOption') == null)
            $this->currentPage = 0;

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


    public function getSeancePage()
    {
        return $this->seance->get('current_page');
    }

    private function prepareQuestionsArray(string $idPage)
    {
        $fileName = __DIR__. '/../../res/test_txt/ask' . $idPage . '.txt';
        $json = file_get_contents($fileName);
        $this->questionsArr = json_decode($json, true);
    }


    private function preparePageParams(string $idPage)
    {
        $this->prepareQuestionsArray($idPage);
        $this->pictureSrc = '../res/img/pict' . $idPage . '.jpg';
        $this->pageTitle = "Картинка №" . $idPage;

        $this->registerPage();
        $this->saveSeance();
    }

    private function registerAnswer(string $idPage): bool
    {
        $key  = $this->seance->get('postOption');

        if ($key  != null)  {
            $this->prepareQuestionsArray($idPage);
            $answOptionsArray = $this->seance->get('answers');
            if ($answOptionsArray == null) {
                $answOptionsArray = [];
            }

            $newRecord = [$key  => $this->questionsArr[$key]];
            $answOptionsArray[$idPage] = $newRecord;

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

    private function preparePage(string $idPage)
    {
        $this->currentPage = $idPage;
        $this->preparePageParams($idPage);
    }



    private function analyseOfSource()
    {
        $page = (int)$this->currentPage;

        if (!$this->isReload) {
            $nextPage = $page + 1;
        } else {
            $nextPage = $this->currentPage;
        }

        switch (true) {
            case $page == 0:
                $this->preparePage(1);
                break;
            case ( $page < 10) :
                if ($this->registerAnswer($this->currentPage))
                    $this->preparePage($nextPage);
                break;
            case $page == 10:
                if ($this->registerAnswer($this->currentPage)) {
                    $this->currentPage = 11;
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