<?php

$web_path = '../';

require_once($web_path.'path.inc.php');
require_once($include_path.'config.inc.php');
require_once($include_path.'db.inc.php');
require_once($include_path.'func.inc.php');
require_once($include_path.'bo_employee.inc.php');

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

echo BOEmployee::option_array($_GET['store_id'], $_GET['employee_id']);

?>
