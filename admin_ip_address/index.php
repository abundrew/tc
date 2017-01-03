<?php

require_once('admin_ip_address.inc.php');

if (isset($_POST['excel']))
{
    BOIPAddress::export_to_excel($_POST['store_id']);
}

if (isset($_POST['excel_all']))
{
    BOIPAddress::export_to_excel(NULL);
}

if (isset($_POST['pdf']))
{
    BOIPAddress::export_to_fpdf($_POST['store_id']);
}

if (isset($_POST['pdf_all']))
{
    BOIPAddress::export_to_fpdf(NULL);
}

if (isset($_POST['exit']))
{
    header('Location: '.$web_path.'admin/');
    exit;
}

if (isset($_POST['delete_ip_address_x']))
{
    BOIPAddress::unauthorize_ip_address($_POST['row_id']);
}

if (isset($_POST['submit']) && isset($_POST['insert_ip_address_id']))
{
    BOIPAddress::authorize_ip_address_as_store($_POST['ip_address'], $_POST['store_id']);
}

page_head('IP Addresses...', $check->is_admin(), $check->is_store(), $web_path.'help/admin_ip_address.html');

echo '<h2>Admin: '.BOAdmin::name_by_id($check->get_admin_id()).'</h2>';

?>

<form name="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<p>
<?php

if (isset($_POST['add_ip_address_x']))
{
    echo '<b>Authorize New IP Address:</b><br><br>';
    $store_store_id = $_POST['store_id'];

?>
<b>IP Address:</b><br>
<input id="ip_address" type="text" name="ip_address" style="width: 200px;" value="<?php echo $ip_address; ?>"><br><br>
<input id="submit" type="submit" name="submit" value="Submit" style="width: 100px; height: 50px;">&nbsp;
<input type="submit" name="cancel" value="Cancel" style="width: 100px; height: 50px;">&nbsp;
<?php

    echo '<input type="hidden" name="insert_ip_address_id" value="1">';
    echo '<input type="hidden" name="store_id" value="'.$_POST['store_id'].'">';
}
else
{

?>
<b>Store:</b><br>
<select id="store_id" name="store_id" style="width: 200px">
<?php echo BOStore::option_array($_POST['store_id']); ?>
</select><br><br>
<b>IP Addresses:</b><br><br>
<table id="ip_address_table" class="table" cellpadding="0" cellspacing="0" style="width: 100%">
</table><input type="hidden" id="row_id" name="row_id" value=""><br>
<input type="submit" name="excel" value="Excel" style="width: 100px; height: 50px;">&nbsp;
<input type="submit" name="excel_all" value="Excel All" style="width: 100px; height: 50px;">&nbsp;
<input type="submit" name="pdf" value="PDF" style="width: 100px; height: 50px;">&nbsp;
<input type="submit" name="pdf_all" value="PDF All" style="width: 100px; height: 50px;">&nbsp;&nbsp;&nbsp;
<input type="submit" name="exit" value="Exit" style="width: 100px; height: 50px;">
<?php

}

?>

</form>

<script type="text/javascript">
$('#store_id').change(function() {
    $.ajax({
        url: 'get_ip_address_table.php',
        data: {
            store_id: $('#store_id').val()
        },
        success: function(data) {
            $('#ip_address_table').html(data);
        },
        dataType: 'html'
    });
});

$(document).ready(function(){
    $('#store_id').trigger('change');
});
</script>

<?php

page_foot();

?>
