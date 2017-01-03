<?php

require_once('admin_period.inc.php');

if (isset($_POST['excel']))
{
    BOPeriod::export_to_excel();
}
if (isset($_POST['pdf']))
{
    BOPeriod::export_to_fpdf();
}
if (isset($_POST['exit']))
{
    header('Location: '.$web_path.'admin/');
    exit;
}

if (isset($_POST['delete_period_x']))
{
    BOPeriod::delete_param($_POST['row_id']);
}
if (isset($_POST['submit']) && isset($_POST['insert_period_id']))
{
    try
    {
        BOPeriod::insert_param(
	    $_POST['period_number'],
	    $started = new DateTime($_POST['started']),
	    $ended = new DateTime($_POST['ended'])
	);
    }
    catch (Exception $e) {}
}
if (isset($_POST['submit']) && isset($_POST['update_period_id']))
{
    $pp = new BOPeriod($_POST['update_period_id']);
    try
    {
	BOPeriod::update_param(
	    $_POST['update_period_id'],
	    $_POST['period_number'],
	    $started = new DateTime($_POST['started']),
	    $ended = new DateTime($_POST['ended'])
	);
    }
    catch (Exception $e) {}
}

page_head('Periods...', $check->is_admin(), $check->is_store(), $web_path.'help/admin_period.html');

echo '<h2>Admin: '.BOAdmin::name_by_id($check->get_admin_id()).'</h2>';

?>

<form name="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<p>
<?php

if (isset($_POST['add_period_x']) || isset($_POST['edit_period_x']))
{
    if (isset($_POST['add_period_x']))
    {
        $period_id = 0;
        echo '<b>Add Period:</b><br><br>';
    }
    else
    {
        $period_id = $_POST['row_id'];
        $pp = new BOPeriod($period_id);
	$period_number = $pp->period_number;
        $started = $pp->started->format('m/d/Y');
        $ended = $pp->ended->format('m/d/Y');
        echo '<b>Edit Period:</b><br><br>';
    }
?>
<b>Period Number:</b><br>
<input id="period_number" type="text" name="period_number" style="width: 200px;" value="<?php echo $period_number; ?>"><br>
<b>Started:</b><br>
<input id="started" type="text" name="started" style="width: 200px;" value="<?php echo $started; ?>"><br>
<b>Ended:</b><br>
<input id="ended" type="text" name="ended" style="width: 200px;" value="<?php echo $ended; ?>"><br><br>
<input id="submit" type="submit" name="submit" value="Submit" style="width: 100px; height: 50px;">&nbsp;
<input type="submit" name="cancel" value="Cancel" style="width: 100px; height: 50px;">&nbsp;
<?php

    if (isset($_POST['add_period_x']))
    {

?>
<input type="hidden" name="insert_period_id" value="1">
<?php

    }
    else
    {

?>
<input type="hidden" name="update_period_id" value="<?php echo $_POST['row_id']; ?>">
<?php

    }
}
else
{

?>
<b>Periods:</b><br><br>
<table id="period_table" class="table" cellpadding="0" cellspacing="0" style="width: 400px">
    <?php echo BOPeriod::html_table(1); ?>
</table><input type="hidden" id="row_id" name="row_id" value=""><br>
<input type="submit" name="excel" value="Excel" style="width: 100px; height: 50px;">&nbsp;
<input type="submit" name="pdf" value="PDF" style="width: 100px; height: 50px;">&nbsp;&nbsp;&nbsp;
<input type="submit" name="exit" value="Exit" style="width: 100px; height: 50px;">
<?php

}

?>

</form>

<?php

if (isset($_POST['add_period_x']) || isset($_POST['edit_period_x']))
{

?>
<script type="text/javascript" src="<?php echo $web_path; ?>js/jquery.ui.core.js"></script>
<script type="text/javascript" src="<?php echo $web_path; ?>js/jquery.ui.datepicker.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $("#started").datepicker();
    $("#ended").datepicker();
});
</script>
<?php

}

page_foot();

?>
