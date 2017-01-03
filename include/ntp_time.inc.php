<?php

class NTPTime
{
    private static function query_ntp_time_server ($timeserver, $socket)
    {
        /* Query a time server
           (C) 1999-09-29, Ralf D. Kloth (QRQ.software) <ralf at qrq.de> */
        $fp = fsockopen($timeserver, $socket, $err, $errstr, 5);
        # parameters: server, socket, error code, error text, timeout
        if ($fp)
        {
            fputs($fp, "\n");
            $timevalue = fread($fp, 49);
            fclose($fp); # close the connection
        }
        else {
            $timevalue = " ";
        }

        $ret = array();
        $ret[] = $timevalue;
        $ret[] = $err;     # error code
        $ret[] = $errstr;  # error text
        return($ret);
    }

    public static function get_time()
    {
	$dt = new DateTime();
	return $dt;

        $timeserver = 'nist1-ny.ustiming.org';
        $timercvd = self::query_ntp_time_server($timeserver,13);
        if (!$timercvd[1]) { # if no error from query_time_server
            $timevalue = $timercvd[0];
            try
            {
		if (is_numeric(substr($timevalue, 1, 5)) && substr($timevalue, 1, 1) !== '9' && substr($timevalue, 38, 3) === 'UTC')
		{
		    $dt = new DateTime(substr($timevalue, 7, 17).' UTC');
		    $dt->sub(new DateInterval('PT'.((substr($timevalue, 25, 2) == '00') ? '5' : '4').'H'));
		}
		else
		{
		    $dt = new DateTime();
		}
            }
            catch (Exception $e)
            {
                $dt = new DateTime();
            }
        }
        else
        {
            $dt = new DateTime();
        }
        return $dt;
    }

    public static function test()
    {
        $timeserver = 'nist1-ny.ustiming.org';
        $timercvd = self::query_ntp_time_server($timeserver,13);
        if (!$timercvd[1]) { # if no error from query_time_server
            $timevalue = $timercvd[0];
	    echo $timevalue;
	} else {
	    echo '===';
	}

    }
}

?>