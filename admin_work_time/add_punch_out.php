<?php

$web_path = '../';

require_once($web_path.'path.inc.php');
require_once($include_path.'config.inc.php');
require_once($include_path.'db.inc.php');
require_once($include_path.'func.inc.php');
require_once($include_path.'bo_period.inc.php');
require_once($include_path.'bo_punch.inc.php');
require_once($include_path.'bo_store.inc.php');

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

$time = $_GET['time'];
$pos = strpos($time, ':');
if ($pos === false)
{
    $time .= ':00';
}

$store_id = BOStore::id_by_code($_GET['store_code']);

BOPunch::punch_out(new DateTime($_GET['date']), new DateTime($time), $_GET['employee_id'], $store_id);

?>
