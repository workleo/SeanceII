<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use classes\TestControl;


$test_control = new TestControl();
$test_control->session_seance(); {//start_session() & save_session();

    if ($test_control->getCurrentPage() == '11') {
        header('Refresh: 0;url=../code/check_page.php');
    } else {

        try {
            $loader = new Twig_Loader_Filesystem('../page');
            $twig = new Twig_Environment($loader);


            echo $twig->render('test_page.html', array(
                'page_title' => $test_control->getPageTitle(),
                'items' => $test_control->getQuestionsArr(),
                'picture_src' => $test_control->getPictureSrc(),
                'user_name' => $test_control->getUserName(),
                'time' => date("H:i:s"),
                array('auto_reload' => true)
            ));


        } catch
        (Exception $e) {
            die ('ERROR: ' . $e->getMessage());
        };
    }
}