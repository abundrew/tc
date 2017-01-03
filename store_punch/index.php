<?php

require_once('store_punch.inc.php');

if (isset($_POST['cancel']))
{
    header('Location: '.$web_path.'store/');
    exit;
}

$now = NTPTime::get_time();
$employee_id = BOEmployee::id_by_code($_POST['employee_code']);

if (isset($_POST['punch_in']))
{
    $punch_in_done = BOPunch::punch_in($now, $now, $employee_id, $check->get_store_id(), $manager_id);
}

if (isset($_POST['punch_out']))
{
    $punch_out_done = BOPunch::punch_out($now, $now, $employee_id, $check->get_store_id(), $manager_id);
}

if (isset($_POST['exit']))
{
    $employee_id = 0;
}

page_head('Punch IN/OUT...', false, false, $web_path.'help/store_punch.html');

echo '<h2>Store: '.BOStore::code_by_id($check->get_store_id()).'</h2>';
echo '<h2>'.$now->format('l jS \of F Y').'&nbsp;&nbsp;&nbsp;Period '.BOPeriod::period_number_by_date($now).'&nbsp;&nbsp;&nbsp;Week '.$now->format('W').'</h2>';

?>

<form name="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<p>

<?php

if ($employee_id > 0)
{
    if ($punch_in_done)
    {
	echo '<br><h2><font color="green">'.htmlentities(BOEmployee::name_by_id($employee_id)).' : IN - '.$now->format('H:i:s').'</font></h2>';
    }
    else
	if ($punch_out_done)
	{
	    echo '<br><h2><font color="red">'.htmlentities(BOEmployee::name_by_id($employee_id)).' : OUT - '.$now->format('H:i:s').'</font></h2>';
	}
	else
	{
	    echo '<br><h2>'.htmlentities(BOEmployee::name_by_id($employee_id)).'</h2>';
	}

    echo '<table id="date_table" border="0" class="table" cellpadding="0" cellspacing="0">';
    echo BOWorkTime::date_html_table($now, $now, $employee_id);
    echo '</table><br>';

    if ($punch_in_done || $punch_out_done)
    {
	$can_punch_in = false;
	$can_punch_out = false;
    }
    else
    {
	$can_punch_in = BOPunch::punched_out($now, $now, $employee_id);
	$can_punch_out = BOPunch::punched_in($now, $now, $employee_id, $store_id) && ($store_id === $check->get_store_id());
    }

    echo '<input type="submit" name="punch_in" value="IN" '.($can_punch_in ? '' : 'disabled="true" ').'style="width: 120px; height: 50px; '.($can_punch_in ? 'border-color: green;' : '').'">&nbsp;';
    echo '<input type="submit" name="punch_out" value="OUT" '.($can_punch_out ? '' : 'disabled="true" ').'style="width: 120px; height: 50px; '.($can_punch_out ? 'border-color: red;' : '').'">&nbsp;&nbsp;&nbsp;';

    echo '<input type="submit" name="exit" value="Exit" style="width: 120px; height: 50px;">';
    if (!($punch_in_done || $punch_out_done))
    {
	echo '<input type="hidden" id="employee_code" name="employee_code" value="'.htmlentities($_POST['employee_code']).'">';
    }
}
else
{
    echo '<b>Employee ID:</b><br>';
    echo '<input type="password" id="employee_code" name="employee_code" style="width: 200px;"><br><br>';
    echo '<input type="submit" name="submit" value="Submit" style="width: 100px; height: 50px;">&nbsp;';
    echo '<input type="submit" name="cancel" value="Cancel" style="width: 100px; height: 50px;">';
    echo '<script type="text/javascript">document.form.employee_code.focus();</script>';
}
?>
</form>

<?php

page_foot();

?>
