<?php

class BOPunch
{

    private static function punch($date, $time, $punch_io, $employee_id, $store_id, $manager_employee_id)
    {
		$sql =
			"INSERT INTO punch (
				punch_date, punch_time, punch_io,
				employee_employee_id, store_store_id, manager_employee_id
			) VALUES (
				'".$date->format('Y-m-d')."', '".$time->format('H:i:s')."', '$punch_io',
				'$employee_id', '$store_id',
				".(isset($manager_employee_id) ? "'$manager_employee_id'" : 'NULL')."
			)";

		//echo $sql;
		//exit;

        $q = new DBQuery($sql, true);
        return $q && (!$q->error());
    }

    static function punch_in($date, $time, $employee_id, $store_id, $manager_employee_id = NULL)
    {
		if (self::punched_out($date, $time, $employee_id))
		{
			return ($done = self::punch($date, $time, 'I', $employee_id, $store_id, $manager_employee_id));
		}
		else
		{
		    return false;
		}
    }

    static function punch_out($date, $time, $employee_id, $store_id, $manager_employee_id = NULL)
    {
		if (self::punched_in($date, $time, $employee_id, $store_id))
		{
			return ($done = self::punch($date, $time, 'O', $employee_id, $store_id, $manager_employee_id));
		}
		else
		{
		    return false;
		}
    }

    private static function get_punch_io($date, $time, $employee_id, &$punch_io, &$store_id)
    {
		$sql = "
			SELECT
				punch_io,
				store_store_id
			FROM
				punch
			WHERE
				employee_employee_id = '$employee_id' AND
				punch_date = '".$date->format('Y-m-d')."' AND
				punch_time <= '".$time->format('H:i:s')."'
			ORDER BY
				punch_time DESC
			LIMIT 1";

		$q = new DBQuery($sql);
		if ($row = $q->fetch())
		{
			$punch_io = $row['punch_io'];
			$store_id = $row['store_store_id'];
		}
		if ($punch_io != 'I')
		{
		    $punch_io = 'O';
		}
    }

    static function punched_in($date, $time, $employee_id, &$store_id)
    {
		$punch_io = 'O';
		self::get_punch_io($date, $time, $employee_id, $punch_io, $store_id);
		return ($punch_io == 'I');
    }

    static function punched_out($date, $time, $employee_id)
    {
		return !($pin = self::punched_in($date, $time, $employee_id, $store_id));
    }

    static function delete($date, $time, $employee_id)
    {
		$sql = "
			DELETE
			FROM
				punch
			WHERE
				employee_employee_id = '$employee_id' AND
				punch_date = '".$date->format('Y-m-d')."' AND
				punch_time = '".$time->format('H:i:s')."'";

        $q = new DBQuery($sql, true);
        return $q && (!$q->error());
    }

}

?>
