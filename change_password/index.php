<?php

include('change_password.inc.php');

if (isset($_POST['cancel']))
{
    if ($check->is_admin())
    {
        header('Location: '.$web_path.'admin/');
	exit;
    }
    if ($check->is_store())
    {
        header('Location: '.$web_path.'store/');
	exit;
    }
}

$done = false;

if (isset($_POST['old_password']) && isset($_POST['new_password']) && isset($_POST['confirm_new_password']))
{
    if ($check->is_admin())
    {
	if (change_admin_password($admin_id, $_POST['old_password'], $_POST['new_password']))
	{
	    $done = true;
	}
    }
    if ($check->is_store())
    {
	if (change_manager_password($manager_id, $_POST['old_password'], $_POST['new_password']))
	{
	    $done = true;
	}
    }
}

if ($done)
{
    $_SESSION['pwd'] = $_POST['new_password'];
}

page_head('Change Password...', $check->is_admin(), $check->is_store(), $web_path.'help/change_password.html');

if ($check->is_admin())
{
    echo '<h2>Admin: '.BOAdmin::name_by_id($admin_id).'</h2>';
}
else
{
    echo '<h2>Store: '.BOStore::code_by_id($check->get_store_id()).'</h2>';
    echo '<h2>'.user_name().'</h2>';
}
echo '<br>';

if ($done)
{
    echo '<h2>Password changed.</h2><br>';
}

?>

<form name="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<p>
<b>Old Password:</b><br>
<input id="old_password" type="password" name="old_password" style="width: 200px;"><br>
<b>New Password:</b><br>
<input id="new_password" type="password" name="new_password" style="width: 200px;"><br>
<b>Confirm New Password:</b><br>
<input id="confirm_new_password" type="password" name="confirm_new_password" style="width: 200px;"><br><br>
<input id="submit" type="submit" name="submit" value="Submit" style="width: 100px; height: 50px;">&nbsp;
<input id="cancel" type="submit" name="cancel" value="Cancel" style="width: 100px; height: 50px;">&nbsp;
</p>
</form>

<script type="text/javascript">
$('#submit').click(function(event) {
    old_pwd = $('#old_password').val();
    new_pwd = $('#new_password').val();
    cfm_pwd = $('#confirm_new_password').val();
    if (!(old_pwd && new_pwd && cfm_pwd && (new_pwd == cfm_pwd)))
    {
        alert('Password is empty or is not confirmed.');
        event.preventDefault();
    }
});

document.form.old_password.focus();
</script>

<?php

page_foot();

?>
