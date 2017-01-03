<?php

require_once('admin_store.inc.php');

if (isset($_POST['excel']))
{
    BOStore::export_to_excel();
}

if (isset($_POST['pdf']))
{
    BOStore::export_to_fpdf();
}

if (isset($_POST['exit']))
{
    header('Location: '.$web_path.'admin/');
    exit;
}

if (isset($_POST['delete_store_x']))
{
    BOStore::delete_param($_POST['row_id']);
}

if (isset($_POST['submit']) && isset($_POST['insert_store_id']))
{
    BOStore::insert_param($_POST['code'], $_POST['description']);
}

if (isset($_POST['submit']) && isset($_POST['update_store_id']))
{
    BOStore::update_param($_POST['update_store_id'], $_POST['code'], $_POST['description']);
}

page_head('Stores...', $check->is_admin(), $check->is_store(), $web_path.'help/admin_store.html');

echo '<h2>Admin: '.BOAdmin::name_by_id($check->get_admin_id()).'</h2>';

?>

<form name="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<p>
<?php
if (isset($_POST['add_store_x']) || isset($_POST['edit_store_x']))
{
    if (isset($_POST['add_store_x']))
    {
        echo '<b>Add Store:</b><br><br>';
    }
    else
    {
        $store_id = $_POST['row_id'];
        $st = new BOStore($store_id);
        $code = $st->code;
        $description = $st->description;
        echo '<b>Edit Store:</b><br><br>';
    }
?>
<b>Number:</b><br>
<input id="code" type="text" name="code" style="width: 200px;" value="<?php echo $code; ?>"><br><br>
<b>Description:</b><br>
<input id="description" type="text" name="description" style="width: 200px;" value="<?php echo $description; ?>"><br><br>
<input id="submit" type="submit" name="submit" value="Submit" style="width: 100px; height: 50px;">&nbsp;
<input type="submit" name="cancel" value="Cancel" style="width: 100px; height: 50px;">&nbsp;
<?php

    if (isset($_POST['add_store_x']))
    {
	echo '<input type="hidden" name="insert_store_id" value="1">';
    }
    else
    {
	echo '<input type="hidden" name="update_store_id" value="'.$_POST['row_id'].'">';
    }
}
else
{

?>
<b>Stores:</b><br><br>
<table id="store_table" class="table" cellpadding="0" cellspacing="0" style="width: 100%">
    <?php echo BOStore::html_table(1); ?>
</table><input type="hidden" id="row_id" name="row_id" value=""><br>

<input type="submit" name="excel" value="Excel" style="width: 100px; height: 50px;">&nbsp;
<input type="submit" name="pdf" value="PDF" style="width: 100px; height: 50px;">&nbsp;&nbsp;&nbsp;
<input type="submit" name="exit" value="Exit" style="width: 100px; height: 50px;">

<?php

}

?>

</form>

<?php

page_foot();

?>
