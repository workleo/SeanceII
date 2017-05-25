<?php

namespace classes;

class TestControl
{
    /** @var  SessionStorage $session */
    private $session;
    private $current_page;
    private $questions_arr;
    private $picture_src;
    private $page_title;
    private $userName;
    private $isReload;


    public function session_seance()
    {
        $this->session = new SessionStorage('/SeanceII/src/');
        $this->session->start();

        if (($this->session->get('order') !== null) && ($this->session->get('order') === 'wrong')) {
            $this->isReload = true;
        } else $this->isReload = false;

        $this->userName = $this->session->get('user_name');

        $this->current_page = $this->session->get('current_page');


        if ($this->session->get('sess_post') == null)
            $this->current_page = '00';

        $this->analyse_of_source();
        $this->save_session();


    }

    public function getUserName()
    {
        return $this->userName;
    }


    public function getPictureSrc()
    {
        return $this->picture_src;
    }

    public function getCurrentPage()
    {
        return $this->current_page;
    }

    public function getQuestionsArr()
    {
        return $this->questions_arr;
    }

    public function getPageTitle()
    {
        return $this->page_title;
    }


    public function getSessionPage()
    {
        return $this->session->get('current_page');
    }

    private function prepare_questions_arr(string $id_page)
    {
        $fileName = $_SERVER['DOCUMENT_ROOT'] . 'src/res/test_txt/ask' . $id_page . '.txt';
        $json = file_get_contents($fileName);
        $this->questions_arr = json_decode($json, true);
    }


    private function prepare_page_params(string $id_page)
    {
        $this->prepare_questions_arr($id_page);
        $this->picture_src = '../res/img/pict' . $id_page . '.jpg';
        $this->page_title = "Картинка №" . $id_page;

        $this->register_page();
        $this->save_session();
    }

    private function register_answer(string $id_page): bool
    {
        $sess_post = $this->session->get('sess_post');

        if (($sess_post != null) && (isset($sess_post['option']))) {
            $this->prepare_questions_arr($id_page);
            $answ_options = $this->session->get('answers');

            $key = '' . $sess_post['option'];
            $key = $this->number2name($key);

            $answ_options[$id_page] = array($sess_post['option'] => $this->questions_arr[$key]);
            $this->session->set('answers', $answ_options);

            return true;
        } else {
            return false;
        }

    }

    private function register_page()
    {
        $this->session->set('current_page', $this->current_page);
    }

    private function prepare_page(string $id_page)
    {
        $this->current_page = $id_page;
        $this->prepare_page_params($id_page);
    }


    private function number2name(int $nr): string
    {
        $name = $nr;
        if (strlen($name) == 1) {
            $name = '0' . $name;
        }
        return $name;
    }

    private function analyse_of_source()
    {
        $page = (int)$this->current_page;

        if (!$this->isReload) {
            $next_page = $this->number2name($page + 1);
        } else{
            $next_page = $this->current_page;
        }

        switch (true) {
            case $page == 0:
                $this->prepare_page("01");
                break;
            case ($page > 0 && $page < 10) ://01-09
                if ($this->register_answer($this->current_page))
                    $this->prepare_page($next_page);
                break;
            case $page == 10:
                if ($this->register_answer($this->current_page)) {//10
                    $this->current_page = '11';
                    $this->register_page();

                }
                break;
        }

    }


    private function save_session()
    {
        if (!$this->isReload) {
            $this->session->set('order', 'wrong');
            $this->session->flush();
        }
    }
}