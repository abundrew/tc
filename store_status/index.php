<?php

require_once('store_status.inc.php');

$now = NTPTime::get_time();

if (isset($_POST['excel']))
{
    BOStatus::status_store_export_to_excel($now, $check->get_store_id());
}

if (isset($_POST['pdf']))
{
    BOStatus::status_store_export_to_fpdf($now, $check->get_store_id());
}

if (isset($_POST['exit']))
{
    header('Location: '.$web_path.'store/');
    exit;
}

page_head('Current Status...', $check->is_admin(), $check->is_store(), $web_path.'help/store_status.html');

echo '<h2>Store: '.BOStore::code_by_id($check->get_store_id()).'</h2>';
echo '<h2>'.$now->format('l jS \of F Y').'&nbsp;&nbsp;&nbsp;Period '.BOPeriod::period_number_by_date($now).'&nbsp;&nbsp;&nbsp;Week '.$now->format('W').'</h2>';

?>

<form name="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<p>

<?php

echo '<table id="date_table" border="0" class="table" cellpadding="0" cellspacing="0">';
echo BOStatus::status_store_html_table($now, $check->get_store_id());
echo '</table><br>';
echo '<input type="submit" name="excel" value="Excel" style="width: 100px; height: 50px;">&nbsp;';
echo '<input type="submit" name="pdf" value="PDF" style="width: 100px; height: 50px;">&nbsp;&nbsp;&nbsp;';
echo '<input type="submit" name="exit" value="Exit" style="width: 100px; height: 50px;">';

?>
</form>

<?php

page_foot();

?>
