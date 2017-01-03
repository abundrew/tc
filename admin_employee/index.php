<?php

require_once('admin_employee.inc.php');

if (isset($_POST['excel']))
{
    BOEmployee::export_to_excel($_POST['store_id']);
}

if (isset($_POST['excel_all']))
{
    BOEmployee::export_to_excel();
}

if (isset($_POST['pdf']))
{
    BOEmployee::export_to_fpdf($_POST['store_id']);
}

if (isset($_POST['pdf_all']))
{
    BOEmployee::export_to_fpdf();
}

if (isset($_POST['exit']))
{
    header('Location: '.$web_path.'admin/');
    exit;
}

if (isset($_POST['delete_employee_x']))
{
    BOEmployee::delete_param($_POST['row_id']);
}

if (isset($_POST['submit']) && isset($_POST['insert_employee_id']))
{
    BOEmployee::insert_param(
	$_POST['code'],
	$_POST['first_name'],
	$_POST['last_name'],
	$_POST['store_store_id'],
	$_POST['job_job_id']
    );
}

if (isset($_POST['submit']) && isset($_POST['update_employee_id']))
{
    BOEmployee::update_param(
	$_POST['update_employee_id'],
	$_POST['code'],
	$_POST['first_name'],
	$_POST['last_name'],
	$_POST['store_store_id'],
	$_POST['job_job_id']
    );
}

page_head('Employees...', $check->is_admin(), $check->is_store(), $web_path.'help/admin_employee.html');

echo '<h2>Admin: '.BOAdmin::name_by_id($check->get_admin_id()).'</h2>';

?>

<form name="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<p>
<?php

if (isset($_POST['add_employee_x']) || isset($_POST['edit_employee_x']))
{
    if (isset($_POST['add_employee_x']))
    {
        $employee_id = 0;
        echo '<b>Add Employee:</b><br><br>';
        $store_store_id = $_POST['store_id'];
	$job_job_id = 6;
    }
    else
    {
        $employee_id = $_POST['row_id'];
        $e = new BOEmployee($employee_id);
        $code = $e->code;
        $first_name = $e->first_name;
        $last_name = $e->last_name;
        $store_store_id = $e->store_store_id;
        $job_job_id = $e->job_job_id;
        echo '<b>Edit Employee:</b><br><br>';
    }
?>
<b>ID:</b><br>
<input id="code" type="text" name="code" style="width: 200px;" value="<?php echo $code; ?>"><br>
<b>First Name:</b><br>
<input id="first_name" type="text" name="first_name" style="width: 200px;" value="<?php echo $first_name; ?>"><br>
<b>Last Name:</b><br>
<input id="last_name" type="text" name="last_name" style="width: 200px;" value="<?php echo $last_name; ?>"><br>
<b>Job Description:</b><br>
<select id="job_job_id" name="job_job_id" style="width: 200px">
<?php echo BOJob::option_array($job_job_id); ?>
</select><br>
<b>Store:</b><br>
<select id="store_store_id" name="store_store_id" style="width: 200px">
<?php echo BOStore::option_array($store_store_id); ?>
</select><br><br>
<input id="submit" type="submit" name="submit" value="Submit" style="width: 100px; height: 50px;">&nbsp;
<input type="submit" name="cancel" value="Cancel" style="width: 100px; height: 50px;">&nbsp;
<?php

    if (isset($_POST['add_employee_x']))
    {
	echo '<input type="hidden" name="insert_employee_id" value="1">';
    }
    else
    {
	echo '<input type="hidden" name="update_employee_id" value="'.$_POST['row_id'].'">';
    }
    echo '<input type="hidden" name="store_id" value="'.$_POST['store_id'].'">';
}
else
{

?>
<b>Store:</b><br>
<select id="store_id" name="store_id" style="width: 200px">
<?php echo BOStore::option_array($_POST['store_id']); ?>
</select><br><br>
<b>Employees:</b><br><br>
<table id="employee_table" class="table" cellpadding="0" cellspacing="0" style="width: 100%">
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
        url: 'get_employee_table.php',
        data: {
            store_id: $('#store_id').val()
        },
        success: function(data) {
            $('#employee_table').html(data);
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
