<?php

require_once($include_path.'bo_employee.inc.php');

function page_head($page_title, $is_admin = false, $is_store = false, $help_page = NULL)
{
    global $config;
    global $web_path;

    include($include_path.'page_head.inc.php');
}

function page_foot()
{
    include($include_path.'page_foot.inc.php');
}

function auth_admin_id($redirect_to_check = true)
{
    global $web_path;

    $code = DBConnection::db_real_escape_string($_SESSION['code']);
    $pwd = DBConnection::db_real_escape_string($_SESSION['pwd']);
    $pwd_md5 = DBConnection::db_real_escape_string(md5($_SESSION['pwd']));
    $ip_address = DBConnection::db_real_escape_string($_SERVER['REMOTE_ADDR']);

    $sql =
	"SELECT * FROM ".
	"admin a JOIN admin_ip_address i ON (i.admin_admin_id = a.admin_id) ".
	"WHERE code = '$code' AND pwd_md5 = '$pwd_md5' AND i.ip_address = '$ip_address'";

    $q = new DBQuery($sql);
    if ($row = $q->fetch())
    {
	$admin_id = $row['admin_id'];
	return $admin_id;
    }

    $sql =
	"INSERT INTO fraud (fraud_date, fraud_time, code, pwd, ip_address) VALUES (".
	"SYSDATE(), SYSDATE(), '$code', '$pwd', '$ip_address')";
    $q = new DBQuery($sql, true);

    if ($redirect_to_check)
    {
        header('Location: '.$web_path.'check/');
        exit;
    }
    else
    {
		return 0;
    }
}

function auth_manager_id($redirect_to_check = true)
{
    global $web_path;

    $ip_address = DBConnection::db_real_escape_string($_SERVER['REMOTE_ADDR']);

    $sql = "SELECT * FROM store_ip_address WHERE ip_address = '$ip_address'";

    $q = new DBQuery($sql);
    if (!$q->fetch())
    {
        if ($redirect_to_check)
		{
			header('Location: '.$web_path.'check/');
			exit;
		}
		else
		{
			return 0;
        }
    }

    $code = DBConnection::db_real_escape_string($_SESSION['code']);
    $pwd = DBConnection::db_real_escape_string($_SESSION['pwd']);
    $pwd_md5 = DBConnection::db_real_escape_string(md5($_SESSION['pwd']));

    $sql =
	"SELECT * FROM ".
	"employee e JOIN job j ON (j.job_id = e.job_job_id) ".
	"WHERE ".
	"e.code = '$code' AND e.pwd_md5 = '$pwd_md5' AND ".
	"j.manager = 1";

    $q = new DBQuery($sql);
    if ($row = $q->fetch())
    {
		$manager_employee_id = $row['employee_id'];
		return $manager_employee_id;
    }

    $sql =
	"INSERT INTO fraud (fraud_date, fraud_time, code, pwd, ip_address) VALUES ( ".
	"SYSDATE(), SYSDATE(), '$code', '$pwd', '$ip_address')";
    $q = new DBQuery($sql, true);

    if ($redirect_to_check)
    {
        header('Location: '.$web_path.'check/');
        exit;
    }
    else
    {
		return 0;
    }
}

function change_admin_password($admin_id, $old_pwd, $new_pwd)
{
    $admin_id = DBConnection::db_real_escape_string($admin_id);
    $old_pwd_md5 = DBConnection::db_real_escape_string(md5($old_pwd));
    $new_pwd_md5 = DBConnection::db_real_escape_string(md5($new_pwd));

    $sql = "SELECT * FROM admin WHERE admin_id = '$admin_id' AND pwd_md5 = '$old_pwd_md5'";

    $q = new DBQuery($sql);
    if (!$q->fetch())
    {
        return false;
    }

    $sql = "UPDATE admin SET pwd_md5 = '$new_pwd_md5' WHERE admin_id = '$admin_id' AND pwd_md5 = '$old_pwd_md5'";

    $q = new DBQuery($sql);

    $sql = "SELECT * FROM admin WHERE admin_id = '$admin_id' AND pwd_md5 = '$new_pwd_md5'";

    $q = new DBQuery($sql);
    if (!$q->fetch())
    {
        return false;
    }
    return true;
}

function change_manager_password($employee_id, $old_pwd, $new_pwd)
{
    $employee_id = DBConnection::db_real_escape_string($employee_id);
    $old_pwd_md5 = DBConnection::db_real_escape_string(md5($old_pwd));
    $new_pwd_md5 = DBConnection::db_real_escape_string(md5($new_pwd));

    $sql = "SELECT * FROM employee WHERE employee_id = '$employee_id' AND pwd_md5 = '$old_pwd_md5'";

    $q = new DBQuery($sql);
    if (!$q->fetch())
    {
        return false;
    }

    $sql = "UPDATE employee SET pwd_md5 = '$new_pwd_md5' WHERE employee_id = '$employee_id' AND pwd_md5 = '$old_pwd_md5'";

    $q = new DBQuery($sql);

    $sql = "SELECT * FROM employee WHERE employee_id = '$employee_id' AND pwd_md5 = '$new_pwd_md5'";

    $q = new DBQuery($sql);
    if (!$q->fetch())
    {
        return false;
    }
    return true;
}

function user_name()
{
    $admin_id = auth_admin_id(false);
    if ($admin_id > 0)
    {
	return BOAdmin::name_by_id($admin_id);
    }
    $manager_id = auth_manager_id(false);
    if ($manager_id > 0)
    {
	return BOEmployee::name_by_id($manager_id);
    }
    return '[unknown]';
}

?>