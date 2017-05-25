<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use classes\SessionStorage;
use classes\FileStorage;

/** @var  SessionStorage $session */
$session = new SessionStorage('/SeanceII/src/');

/** @var  FileStorage $user_storage */
$user_storage = new FileStorage();

$isNeedToRestore=false;
$session->start();
$current_page = $session->get('current_page');

if ($_POST != null)
    if (isset($_POST['start_test'])) {
        //renew a user login
        $user_name = htmlentities($_POST['user_name'], ENT_QUOTES | ENT_IGNORE, "UTF-8");
        $user_id=$session->get('user_name');
        if (($user_id!==null) && ($user_id!=$user_name)){

            die_nicely('Не больше одного пользователя для одного броузера!');
        }

        $session->set('user_name', $user_name);

        $user_storage->setUserName($user_name);
        $user_storage->start();
        $cp = $user_storage->get('current_page');// for check the user statement

        if ($cp !== null) {
            $current_page = $cp;// to old user step of the old test
            $isNeedToRestore=true;//we have to renew this user steps
        }
        else {

            $user_storage->set('user_name', $user_name);// a new user
        }

    } else {
        // next steps of test
        $user_name = $session->get('user_name');
        if ($user_name === null) { // it's imposible
            die_nicely('Начните с начала!');
        }

        $user_storage->setUserName($user_name);
        $user_storage->start();
        $cp = $user_storage->get('current_page');// for check the user statement

        if (($cp !== null) && ($cp > $current_page)) {
            $current_page = $cp;// there were old steps of test
            $isNeedToRestore=true;//we have to renew this user steps
        } else {
            $session->set('sess_post', $_POST);// set a new step
            if (isset($_POST['final_page'])) {
                $current_page = '12';// go to reset this user and test. Test has been finished.
            }
        }

    }

$session->set('current_page', $current_page);    //that is a real next page for this user
if ($isNeedToRestore===true){
    $session->set('sess_post', $user_storage->get('sess_post'));
    $session->set('answers', $user_storage->get('answers'));
}
$session->set('order','correct');
$session->flush();

if ($isNeedToRestore===false) {
    $user_storage->set('current_page', $current_page);
    $user_storage->set('sess_post', $session->get('sess_post'));
    $user_storage->set('answers', $session->get('answers'));
    $user_storage->flush();
}


switch ($current_page) {
    case "11":
        header('Refresh: 0;url=../code/final_page.php');
        break;
    case "12":
        $session->close();// clear an old session
        $user_storage->close();// delete user at all
        header('Refresh: 0;url=../code/index.php');
        break;
    default:
        header('Refresh: 0;url=../code/test_page.php');
        break;
}

function die_nicely($msg) {
 echo <<<END
<div id="critical_error" style="color: red;border: solid thin">$msg</div>
END;
    exit;
}