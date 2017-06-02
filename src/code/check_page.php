<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use Code\Classes\SeanceStorage;
use Code\Classes\UserStorage;

/** @var  SeanceStorage $seance */
$seance = new SeanceStorage('/SeanceII/src/');

/** @var  UserStorage $userStorage */
$userStorage = new UserStorage();

$isNeedToRestore = false;
$seance->start();
$currentPage = $seance->get('current_page');

if ($_POST != null) {
    if (isset($_POST['start_test'])) {
        $userName = htmlentities($_POST['user_name'], ENT_QUOTES | ENT_IGNORE, "UTF-8");
        $userFromSeance = $seance->get('user_name');
        if (($userFromSeance !== null) && ($userFromSeance != $userName)) {

            dieNicely('Не больше одного пользователя для одного броузера!');
        }

        $seance->set('user_name', $userName);
        $userStorage->start($userName);
        $cp = $userStorage->get('current_page');

        if ($cp !== null) {
            $currentPage = $cp;
            $isNeedToRestore = true;
        } else {

            $userStorage->set('user_name', $userName);
        }

    } else {
        $userName = $seance->get('user_name');
        if ($userName === null) {
            dieNicely('Начните с начала!');
        }

        $userStorage->start($userName);
        $cp = $userStorage->get('current_page');

        if (($cp !== null) && ($cp > $currentPage)) {
            $currentPage = $cp;
            $isNeedToRestore = true;
        } else {
            $seance->set('postOption', $_POST['option']);
            if (isset($_POST['final_page'])) {
                $currentPage = '12';
            }
        }
    }

    $seance->set('current_page', $currentPage);
    if ($isNeedToRestore === true) {
        $seance->set('postOption', $userStorage->get('postOption'));
        $seance->set('answers', $userStorage->get('answers'));
    }
    $seance->set('order', 'correct');
    $seance->flush();

    if ($isNeedToRestore === false) {
        $userStorage->set('current_page', $currentPage);
        $userStorage->set('postOption', $seance->get('postOption'));
        $userStorage->set('answers', $seance->get('answers'));
        $userStorage->flush();
    }

} else {
    $userName = $seance->get('user_name');
    $userStorage->start($userName);
}

switch ($currentPage) {
    case 11:
        header('Refresh: 0;url=../code/final_page.php');
        break;
    case 12:
        if ($_POST !== null) {
            $seance->close();
            $userStorage->close();
        }
        header('Refresh: 0;url=../code/index.php');
        break;
    default:
        header('Refresh: 0;url=../code/test_page.php');
        break;
}


function dieNicely($msg)
{
    echo <<<END
<div id="critical_error" style="color: red;border: solid thin"><h2>$msg</h2></div>
END;
    exit;
}