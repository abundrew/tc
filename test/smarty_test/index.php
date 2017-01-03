<?php

// load Smarty library
require(dirname(__FILE__).'/MySmarty.class.php');

$smarty = new MySmarty;

$smarty->assign('name','Ned');
$smarty->assign('sentence', 'what is the matter?');

$smarty->display('hello.tpl');
?>
