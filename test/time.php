<?php

require ('../include/ntp_time.inc.php');

for ($i = 0; $i < 20; $i++)
{
    echo $i.' '.NTPTime::get_time()->format('m/d/Y').'<br>';
    //sleep(1);
}

?>
