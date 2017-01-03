<?php

require_once('admin.inc.php');

page_head('Admin...', $check->is_admin(), $check->is_store(), $web_path.'help/admin.html');

echo '<h2>Admin: '.BOAdmin::name_by_id($check->get_admin_id()).'</h2>';

?>

<table border="0">
    <tr>
	<td><a href="<?php echo $web_path.'admin_store/'; ?>"><img src="<?php echo $web_path.'image/home_48x48.png'; ?>" border="0" alt=""></a></td>
	<td><a href="<?php echo $web_path.'admin_store/'; ?>" class="main_menu"><h3>Stores</h3></a></td>
    </tr>
    <tr>
	<td><a href="<?php echo $web_path.'admin_ip_address/'; ?>"><img src="<?php echo $web_path.'image/computer_48x48.png'; ?>" border="0" alt=""></a></td>
	<td><a href="<?php echo $web_path.'admin_ip_address/'; ?>" class="main_menu"><h3>IP Addresses</h3></a></td>
    </tr>
    <tr>
	<td><a href="<?php echo $web_path.'admin_period/'; ?>"><img src="<?php echo $web_path.'image/calendar_48x48.png'; ?>" border="0" alt=""></a></td>
	<td><a href="<?php echo $web_path.'admin_period/'; ?>" class="main_menu"><h3>Periods</h3></a></td>
    </tr>
    <tr>
	<td><a href="<?php echo $web_path.'admin_employee/'; ?>"><img src="<?php echo $web_path.'image/people_48x48.png'; ?>" border="0" alt=""></a></td>
	<td><a href="<?php echo $web_path.'admin_employee/'; ?>" class="main_menu"><h3>Employees</h3></a></td>
    </tr>
    <tr>
	<td><a href="<?php echo $web_path.'admin_work_time/'; ?>"><img src="<?php echo $web_path.'image/calculator_48x48.png'; ?>" border="0" alt=""></a></td>
	<td><a href="<?php echo $web_path.'admin_work_time/'; ?>" class="main_menu"><h3>Work Time</h3></a></td>
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
