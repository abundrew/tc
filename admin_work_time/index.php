<?php

require_once('admin_work_time.inc.php');

if (isset($_POST['excel']))
{
    $p = new BOPeriod($_POST['period_id']);
    BOWorkTime::period_export_to_excel($p->started, $p->ended, $_POST['employee_id']);
}

if (isset($_POST['pdf']))
{
    $p = new BOPeriod($_POST['period_id']);
    BOWorkTime::period_export_to_fpdf($p->started, $p->ended, $_POST['employee_id']);
}

if (isset($_POST['exit']))
{
    header('Location: '.$web_path.'admin/');
    exit;
}

page_head('Work Time...', $check->is_admin(), $check->is_store(), $web_path.'help/admin_work_time.html');

echo '<h2>Admin: '.BOAdmin::name_by_id($check->get_admin_id()).'</h2>';

?>

<form name="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<p>
<b>Period:</b><br>
<select id="period_id" name="period_id" style="width: 200px">
<?php echo BOPeriod::option_array($_POST['period_id']); ?>
</select><br>
<b>Store:</b><br>
<select id="store_id" name="store_id" style="width: 200px">
<?php echo BOStore::option_array($_POST['store_id']); ?>
</select><br>
<b>Employee:</b><br>
<select id="employee_id" name="employee_id" style="width: 200px">
<?php echo BOEmployee::option_array($_POST['store_id'], $_POST['employee_id']); ?>
</select><br><br>
<b>Work Time:</b><br><br>
<table border="0" width="100%" cellpadding="0" cellspacing="0">
    <tr>
	<td valign="top">
	    <table id="work_time_table" class="table" cellpadding="0" cellspacing="0" style="width: 400px"></table>
	</td>
	<td>&nbsp;</td>
	<td align="right" valign="top">
	    <table id="date_table" class="table" cellpadding="0" cellspacing="0"  style="width: 180px"></table>
	</td>
    </tr>
</table><br>
<input type="submit" name="excel" value="Excel" style="width: 100px; height: 50px;">&nbsp;
<input type="submit" name="pdf" value="PDF" style="width: 100px; height: 50px;">&nbsp;&nbsp;&nbsp;
<input type="submit" name="exit" value="Exit" style="width: 100px; height: 50px;">

</form>

<script type="text/javascript">
$('#store_id').change(function() {
    $.ajax({
        url: 'get_employees.php',
        data: {store_id: $('#store_id').val(), employee_id: <?php echo (int)$_POST['employee_id']; ?>},
        success: function(data) {$('#employee_id').html(data); $('#employee_id').trigger('change');},
        dataType: 'html'
    });
});

$(document).ready(function(){
    $('#store_id').trigger('change');
});

$('#period_id').change(function() {
    populateWorkTimeTable();
    populateDateTableByPeriod();
});

$('#employee_id').change(function() {
    populateWorkTimeTable();
    populateDateTableByPeriod();
});

function populateWorkTimeTable()
{
    $.ajax({
        url: 'get_work_time.php',
        data: {
            employee_id: $('#employee_id').val(),
            period_id: $('#period_id').val()
        },
        success: function(data) {
            $('#work_time_table').html(data);
        },
        dataType: 'html'
    });
}

function populateDateTable(date)
{
    $.ajax({
        url: 'get_date.php',
        data: {
            employee_id: $('#employee_id').val(),
            date: date
        },
        success: function(data) {
            $('#date_table').html(data);
        },
        dataType: 'html'
    });
}

function populateDateTableByPeriod()
{
    $.ajax({
        url: 'get_date_by_period.php',
        data: {
            employee_id: $('#employee_id').val(),
            period_id: $('#period_id').val()
        },
        success: function(data) {
            $('#date_table').html(data);
        },
        dataType: 'html'
    });
}

function DeletePunch(date, time)
{
    if (confirm('Delete record on ' + date + ' ' + time + '?'))
    {
	$.ajax({
	    url: 'delete_punch.php',
	    data: {
                employee_id: $('#employee_id').val(),
                date: date,
		time: time
	    },
	    success: function() {
		populateWorkTimeTable();
		populateDateTable(date);
	    }
	});
    }
}

function AddPunchIn(date)
{
    $.ajax({
	url: 'add_punch_in.php',
	data: {
	    employee_id: $('#employee_id').val(),
            date: date,
	    time: $('#punch_in_time').val(),
	    store_code: $('#punch_in_store_code').val()
	},
	success: function() {
	    populateWorkTimeTable();
	    populateDateTable(date);
	}
    });
}

function AddPunchOut(date)
{
    $.ajax({
	url: 'add_punch_out.php',
	data: {
	    employee_id: $('#employee_id').val(),
            date: date,
	    time: $('#punch_out_time').val(),
	    store_code: $('#punch_out_store_code').val()
	},
	success: function() {
	    populateWorkTimeTable();
	    populateDateTable(date);
	}
    });
}

</script>

<?php

page_foot();

?>
