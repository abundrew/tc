<?php

require_once('check.inc.php');

if (isset($_POST['code']) && isset($_POST['pwd']))
{
    $_SESSION['code'] = $_POST['code'];
    $_SESSION['pwd'] = $_POST['pwd'];

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

page_head('Check...', false, false, $web_path.'help/check.html');

if ($check->is_admin())
{
    echo '<h2>Admin: '.BOAdmin::name_by_id($check->get_admin_id()).'</h2>';
}

if ($check->is_store())
{
    echo '<h2>Store: '.BOStore::code_by_id($check->get_store_id()).'</h2>';
}

?>

<form name="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<p>
<b>ID:</b><br>
<input type="password" id="code" name="code" style="width: 200px;"><br><br>
<b>Password:</b><br>
<input type="password" name="pwd" style="width: 200px;"><br><br>
<input type="submit" name="submit" value="Submit" style="width: 100px; height: 50px;">&nbsp;
<input type="reset" name="reset" value="Clear" style="width: 100px; height: 50px;">&nbsp;
</p>
</form>

<script type="text/javascript">
    
document.form.code.focus();

</script>

<?php

page_foot();

?>
