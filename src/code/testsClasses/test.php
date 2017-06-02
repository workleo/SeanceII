<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
use Code\Classes\SeanceStorage;
use Code\Classes\UserStorage;
use Code\TestClasses\TestClass;


$sess=new SeanceStorage();
$sess->start();
echo $_COOKIE[SeanceStorage::SEANCE_ID];//[SeanceStorage::SEANCE_ID];
echo '<br>';

$sess->set('key1','value1');
$sess->set('key2','value2');
$sess->set('key3','value3'.":{.\\\"");
$sess->set('key1','value4');

print $sess->get('key1');echo '<br>';
print $sess->get('key2');echo '<br>';
print $sess->get('key3');echo '<br>';


$sess->remove('key2');
print 'key2 value:'.$sess->get('key2');echo '<br>';
print 'key1 value:'.$sess->get('key1');echo '<br>';


$arr = array(array('PHP4','PHP5','PHP7'), 'MySQL', 'Apache');

$sess->set('keyArr',$arr);
$arr=[];
$arr_tst=$sess->get('keyArr');

if (!is_array($arr_tst)) {
print 'keyArr not Array :'.$sess->get('keyArr');echo '<br>';
} else {
    var_dump($arr_tst);
    print ('arrays ok<br>');
}

$tst=new TestClass();
$tst->setCount(100);
$tst->setName('a class for analyse');

$sess->set('keyClass',$tst);
$tst=null;
$tst_cls=$sess->get('keyClass');
if (!is_object($tst_cls)) {echo 'keyClass coudn"t return an object'; }
else {
   echo $tst_cls->getCount().' '.$tst_cls->getName()."  . <br>The Class ok.<br>";
}

$sess->flush();
print('the test of seance finished<br> Start test Users file<br>');
//$sess->close();

$fu=new UserStorage();
//$fu->setUserName('Test_User');
$fu->start('Test_User');

$tst_cls->setCount(1);
$tst_cls->setName('user');

$fu->set('class',$tst_cls);
$tst=null;
$tst_cls=$fu->get('class');
if (!is_object($tst_cls)) {echo 'keyClass coudn"t return an object<br>'; }
else {
    echo $tst_cls->getCount().' '.$tst_cls->getName()."  . <br>The Class ok.<br>";
}

$fu->flush();
print('the test of users file finished<br>');
