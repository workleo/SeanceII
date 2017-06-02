<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use Code\Classes\TestControl;


$testControl = new TestControl();
$testControl->seanceExecute(); {

    if ($testControl->getCurrentPage() == '11') {
        header('Refresh: 0;url=../code/check_page.php');
    } else {

        try {
            $loader = new Twig_Loader_Filesystem('../page');
            $twig = new Twig_Environment($loader);


            echo $twig->render('test_page.html', array(
                'page_title' => $testControl->getPageTitle(),
                'items' => $testControl->getQuestionsArr(),
                'picture_src' => $testControl->getPictureSrc(),
                'user_name' => $testControl->getUserName(),
                'time' => date("H:i:s"),
                array('auto_reload' => true)
            ));


        } catch
        (Exception $e) {
            die ('ERROR: ' . $e->getMessage());
        };
    }
}