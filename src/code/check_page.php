<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use classes\SessionStorage;
use classes\FileStorage;

/** @var  SessionStorage $seance */
$seance = new SessionStorage('/SeanceII/src/');

/** @var  FileStorage $userStorage */
$userStorage = new FileStorage();

$isNeedToRestore=false;
$seance->start();
$currentPage = $seance->get('current_page');

if ($_POST != null)
    if (isset($_POST['start_test'])) {
        $userName = htmlentities($_POST['user_name'], ENT_QUOTES | ENT_IGNORE, "UTF-8");
        $userFromSeance=$seance->get('user_name');
        if (($userFromSeance!==null) && ($userFromSeance!=$userName)){

            dieNicely('Не больше одного пользователя для одного броузера!');
        }

        $seance->set('user_name', $userName);

        $userStorage->setUserName($userName);
        $userStorage->start();
        $cp = $userStorage->get('current_page');

        if ($cp !== null) {
            $currentPage = $cp;
            $isNeedToRestore=true;
        }
        else {

            $userStorage->set('user_name', $userName);
        }

    } else {
        $userName = $seance->get('user_name');
        if ($userName === null) {
            dieNicely('Начните с начала!');
        }

        $userStorage->setUserName($userName);
        $userStorage->start();
        $cp = $userStorage->get('current_page');

        if (($cp !== null) && ($cp > $currentPage)) {
            $currentPage = $cp;
            $isNeedToRestore=true;
        } else {
            $seance->set('sess_post', $_POST);
            if (isset($_POST['final_page'])) {
                $currentPage = '12';
            }
        }

    }

$seance->set('current_page', $currentPage);
if ($isNeedToRestore===true){
    $seance->set('sess_post', $userStorage->get('sess_post'));
    $seance->set('answers', $userStorage->get('answers'));
}
$seance->set('order','correct');
$seance->flush();

if ($isNeedToRestore===false) {
    $userStorage->set('current_page', $currentPage);
    $userStorage->set('sess_post', $seance->get('sess_post'));
    $userStorage->set('answers', $seance->get('answers'));
    $userStorage->flush();
}


switch ($currentPage) {
    case "11":
        header('Refresh: 0;url=../code/final_page.php');
        break;
    case "12":
        $seance->close();
        $userStorage->close();
        header('Refresh: 0;url=../code/index.php');
        break;
    default:
        header('Refresh: 0;url=../code/test_page.php');
        break;
}

function dieNicely($msg) {
 echo <<<END
<div id="critical_error" style="color: red;border: solid thin"><h2>$msg</h2></div>
END;
    exit;
}