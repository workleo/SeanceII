<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use classes\FinalPageControl;

try {

    $final_control = new FinalPageControl();
    $final_control->session_seans();//start_session() & save_session();

    $loader = new Twig_Loader_Filesystem('../page');
    $twig = new Twig_Environment($loader);


    echo $twig->render('final_page.html', array(
        'user_name'=>$final_control->getUserName(),
        'page_title' => $final_control->getPageTitle(),
        'description'=>$final_control->getDescription(),
        'picture_src'=>$final_control->getPictureSrc(),
        'pict_src_arr'=>$final_control->getPictureSrcArr(),
        'answ_arr'=>$final_control->getAnswers(),


        array('auto_reload' => true)
    ));


} catch (Exception $e) {
    die ('ERROR: ' . $e->getMessage());
};
