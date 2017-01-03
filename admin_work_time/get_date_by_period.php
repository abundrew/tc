<?php

$web_path = '../';

require_once($web_path.'path.inc.php');
require_once($include_path.'config.inc.php');
require_once($include_path.'db.inc.php');
require_once($include_path.'func.inc.php');
require_once($include_path.'bo_period.inc.php');
require_once($include_path.'bo_punch.inc.php');
require_once($include_path.'bo_work_time.inc.php');

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

$p = new BOPeriod($_GET['period_id']);

echo BOWorkTime::date_html_table($p->started, NULL, $_GET['employee_id'], 1);

?>
