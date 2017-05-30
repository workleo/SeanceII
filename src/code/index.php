<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use classes\IndexPageControl;

try {

    $indexPageControl = new IndexPageControl();

    $loader = new Twig_Loader_Filesystem('../page');
    $twig = new Twig_Environment($loader);

    echo $twig->render('index.html', array(
        'indexControls'=>$indexPageControl,
        'edem' => '../res/img/ESCHER_paradise.jpg',
        array('auto_reload' => true)
    ));


} catch (Exception $e) {
    die ('ERROR: ' . $e->getMessage());
};

