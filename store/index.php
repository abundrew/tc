<?php

require_once('store.inc.php');

$now = NTPTime::get_time();

page_head('Store...', $check->is_admin(), $check->is_store(), $web_path.'help/store.html');

echo '<h2>Store: '.BOStore::code_by_id($check->get_store_id()).'</h2>';
echo '<h2>'.$now->format('l jS \of F Y').'&nbsp;&nbsp;&nbsp;Period '.BOPeriod::period_number_by_date($now).'&nbsp;&nbsp;&nbsp;Week '.$now->format('W').'</h2>';
echo '<br>';

?>

<table border="0">
    <tr>
	<td><a href="<?php echo $web_path.'store_punch/'; ?>"><img src="<?php echo $web_path.'image/clock_48x48.png'; ?>" border="0" alt=""></a></td>
	<td><a href="<?php echo $web_path.'store_punch/'; ?>" class="main_menu"><h3>Punch IN/OUT</h3></a></td>
    </tr>
    <tr>
	<td><a href="<?php echo $web_path.'store_activity/'; ?>"><img src="<?php echo $web_path.'image/people_48x48.png'; ?>" border="0" alt=""></a></td>
	<td><a href="<?php echo $web_path.'store_activity/'; ?>" class="main_menu"><h3>Activity Report</h3></a></td>
    </tr>
    <tr>
	<td><a href="<?php echo $web_path.'store_status/'; ?>"><img src="<?php echo $web_path.'image/man_48x48.png'; ?>" border="0" alt=""></a></td>
	<td><a href="<?php echo $web_path.'store_status/'; ?>" class="main_menu"><h3>Current Status</h3></a></td>
    </tr>
    <tr>
	<td colspan="2"><hr></td>
    </tr>
    <tr>
	<td><a href="<?php echo $web_path.'change_password/'; ?>"><img src="<?php echo $web_path.'image/key_48x48.png'; ?>" border="0" alt=""></a></td>
	<td><a href="<?php echo $web_path.'change_password/'; ?>" class="main_menu"><h3>Change Password</h3></a></td>
    </tr>
    <tr>
	<td><a href="<?php echo $web_path.'logout/'; ?>"><img src="<?php echo $web_path.'image/stop_48x48.png'; ?>" border="0" alt=""></a></td>
	<td><a href="<?php echo $web_path.'logout/'; ?>" class="main_menu"><h3>Log Out</h3></a></td>
    </tr>
</table>

<?php

page_foot();

?>
