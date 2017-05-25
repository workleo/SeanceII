<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use classes\IndexPageControl;

try {

    $ic = new IndexPageControl();
   // $ic->session_seans();//start_session();save_session();


    $loader = new Twig_Loader_Filesystem('../page');
    $twig = new Twig_Environment($loader);

    echo $twig->render('index.html', array(
        'page_title' => $ic->getTitle(),
        'description' => $ic->getDescription(),
        'submit_value' => $ic->getSubmitValue(),
        'edem' => '../res/img/ESCHER_paradise.jpg',
        array('auto_reload' => true)
    ));


} catch (Exception $e) {
    die ('ERROR: ' . $e->getMessage());
};

