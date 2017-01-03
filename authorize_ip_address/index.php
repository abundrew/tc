<?php

require_once('authorize_ip_address.inc.php');

if (isset($_POST['unauthorize']))
{
    BOIPAddress::unauthorize_ip_address($ip_address);
}

if (isset($_POST['authorize_as_admin']))
{
    BOIPAddress::authorize_ip_address_as_admin($ip_address, $_POST['admin_id']);
}

if (isset($_POST['authorize_as_store']))
{
    BOIPAddress::authorize_ip_address_as_store($ip_address, $_POST['store_id']);
}

if (isset($_POST['time_card']))
{
        header('Location: ../');
	exit;
}

$check = new BOCheck();

page_head('Authorize/Unauthorize Your IP Address...', false, false);

if ($check->is_admin())
{
    echo '<h2>Your IP Address ['.$ip_address.'] is authorized as Admin: '.BOAdmin::name_by_id($check->get_admin_id()).'</h2>';
}
else
    if ($check->is_store())
    {
        echo '<h2>Your IP Address ['.$ip_address.'] is authorized as Store: '.BOStore::code_by_id($check->get_store_id()).'</h2>';
    }
    else
    {
        echo '<h2>Your IP Address ['.$ip_address.'] is not authorized.</h2>';
    }

?>

<form name="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<p>
<b>Authorize as Admin:</b><br>
<select id="admin_id" name="admin_id" style="width: 200px">
<?php echo BOAdmin::option_array(0); ?>
</select><br>
<input type="submit" name="authorize_as_admin" value="Authorize" style="width: 120px; height: 50px;"><br><br>
<b>Authorize as Store:</b><br>
<select id="store_id" name="store_id" style="width: 200px">
<?php echo BOStore::option_array(0); ?>
</select><br>
<input type="submit" name="authorize_as_store" value="Authorize" style="width: 120px; height: 50px;"><br><br>
<b>Unauthorize:</b><br>
<input type="submit" name="unauthorize" value="Unauthorize" style="width: 120px; height: 50px;"><br><br>
<b>Go Back to Time Card:</b><br>
<input type="submit" name="time_card" value="Time Card" style="width: 120px; height: 50px;"><br>
</p>
</form>

<?php

page_foot();

?>
