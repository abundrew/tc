<?php

$web_path = '../';

require_once($web_path.'path.inc.php');
require_once($include_path.'config.inc.php');
require_once($include_path.'db.inc.php');
require_once($include_path.'func.inc.php');
require_once($include_path.'bo_check.inc.php');
require_once($include_path.'bo_store.inc.php');
require_once($include_path.'bo_work_time.inc.php');
require_once($include_path.'bo_punch.inc.php');
require_once($include_path.'bo_period.inc.php');
require_once($include_path.'ntp_time.inc.php');

session_start();

$check = new BOCheck();

$manager_id = auth_manager_id();

?>