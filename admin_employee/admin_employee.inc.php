<?php

$web_path = '../';

require_once($web_path.'path.inc.php');
require_once($include_path.'config.inc.php');
require_once($include_path.'db.inc.php');
require_once($include_path.'func.inc.php');
require_once($include_path.'bo_admin.inc.php');
require_once($include_path.'bo_check.inc.php');
require_once($include_path.'bo_store.inc.php');
require_once($include_path.'bo_employee.inc.php');
require_once($include_path.'bo_job.inc.php');
require_once($include_path.'excel.inc.php');

require_once($web_path.'fpdf/fpdf.php');

session_start();

$check = new BOCheck();

$admin_id = auth_admin_id();

?>
