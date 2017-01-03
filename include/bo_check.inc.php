<?php

class BOCheck
{
    private $admin_id;
    private $store_id;

    function __construct()
    {
	$this->check();
    }

    private function check()
    {
	$this->admin_id = 0;
	$this->store_id = 0;
	$ip_address = $_SERVER['REMOTE_ADDR'];

	$sql = "
		SELECT * FROM store_ip_address WHERE ip_address = '$ip_address'
	";

	$q = new DBQuery($sql);
	if ($row = $q->fetch())
	{
	    $this->store_id = $row['store_store_id'];
        }

	$sql = "
		SELECT * FROM admin_ip_address WHERE ip_address = '$ip_address'
	";

	$q = new DBQuery($sql);
	if ($row = $q->fetch())
	{
	    $this->admin_id = $row['admin_admin_id'];
        }
    }

    function is_admin()
    {
	return ($this->admin_id > 0);
    }

    function is_store()
    {
	return ($this->store_id > 0);
    }

    function get_admin_id()
    {
	return $this->admin_id;
    }

    function get_store_id()
    {
	return $this->store_id;
    }

}

?>