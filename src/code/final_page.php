<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use classes\FinalPageControl;

try {

    $finalPageControl = new FinalPageControl();
    $finalPageControl->seanceExecute();

    $loader = new Twig_Loader_Filesystem('../page');
    $twig = new Twig_Environment($loader);


    echo $twig->render('final_page.html', array(
        'user_name'=>$finalPageControl->getUserName(),
        'page_title' => $finalPageControl->getPageTitle(),
        'description'=>$finalPageControl->getDescription(),
        'picture_src'=>$finalPageControl->getPictureSrc(),
        'pict_src_arr'=>$finalPageControl->getPictureSrcArr(),
        'answ_arr'=>$finalPageControl->getAnswers(),


        array('auto_reload' => true)
    ));


} catch (Exception $e) {
    die ('ERROR: ' . $e->getMessage());
};
