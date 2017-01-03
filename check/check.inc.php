<?php

$web_path = '../';

require_once($web_path.'path.inc.php');
require_once($include_path.'config.inc.php');
require_once($include_path.'db.inc.php');
require_once($include_path.'func.inc.php');
require_once($include_path.'bo_check.inc.php');
require_once($include_path.'bo_admin.inc.php');
require_once($include_path.'bo_store.inc.php');

session_start();

$check = new BOCheck();

if (!$check->is_admin() && !$check->is_store())
{
    header('Location: '.$web_path.'unauthorized/');
    exit;
}

?>