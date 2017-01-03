<?php

class BOWorkTime
{
    static function date_store_summary($date, $time, $employee_id, $store_id, &$begin_time, &$end_time, &$work_time)
    {
	$sql =
	    "SELECT ".
		"SEC_TO_TIME(MIN(wt4.itm)) AS begin_time, ".
		"SEC_TO_TIME(MAX(wt4.otm)) AS end_time, ".
		"SEC_TO_TIME(SUM(wt4.otm - wt4.itm)) AS work_time ".
	    "FROM ( ".
		"SELECT wt3.dt, MIN(wt3.itm) AS itm, IFNULL(wt3.otm, ".(isset($time) ? "TIME_TO_SEC('".$time->format('H:i:s')."')" : '60').") AS otm FROM ( ".
		    "SELECT wt1.dt, TIME_TO_SEC(wt1.tm) AS itm, MIN(TIME_TO_SEC(wt2.tm)) AS otm ".
		    "FROM ".
			"(SELECT punch_date AS dt, punch_time AS tm, punch_io AS io FROM punch WHERE employee_employee_id = $employee_id AND store_store_id = $store_id) wt1 LEFT JOIN (SELECT punch_date AS dt, punch_time AS tm, punch_io AS io FROM punch WHERE employee_employee_id = $employee_id AND store_store_id = $store_id) wt2 ON ( ".
			    "wt1.dt = wt2.dt AND wt1.io = 'I' AND wt2.io = 'O' AND wt1.tm < wt2.tm ".
			") ".
		    "WHERE ( ".
			"wt1.io = 'I' ".
		    ") ".
		    "GROUP BY wt1.dt, wt1.tm ".
		") wt3 ".
		"GROUP BY wt3.dt, wt3.otm ".
	    ") wt4 ".
	    "WHERE wt4.dt = '".$date->format('Y-m-d')."'";

	//echo $sql;
	//exit;

        $q = new DBQuery($sql);
        if ($row = $q->fetch())
        {
	    $begin_time = $row['begin_time'];
	    $end_time = $row['end_time'];
	    $work_time = $row['work_time'];
	    return true;
        }
	else
	{
	    return false;
	}
    }

    static function date_summary($date, $time, $employee_id, &$begin_time, &$end_time, &$work_time)
    {
	$sql =
	    "SELECT ".
		"SEC_TO_TIME(MIN(wt4.itm)) AS begin_time, ".
		"SEC_TO_TIME(MAX(wt4.otm)) AS end_time, ".
		"SEC_TO_TIME(SUM(wt4.otm - wt4.itm)) AS work_time ".
	    "FROM ( ".
		"SELECT wt3.dt, wt3.st, MIN(wt3.itm) AS itm, IFNULL(wt3.otm, ".(isset($time) ? "TIME_TO_SEC('".$time->format('H:i:s')."')" : 'MIN(wt3.itm) + 60').") AS otm FROM ( ".
		    "SELECT wt1.dt, wt1.st, TIME_TO_SEC(wt1.tm) AS itm, MIN(TIME_TO_SEC(wt2.tm)) AS otm ".
		    "FROM ".
			"(SELECT punch_date AS dt, punch_time AS tm, punch_io AS io, store_store_id AS st FROM punch WHERE employee_employee_id = $employee_id) wt1 LEFT JOIN (SELECT punch_date AS dt, punch_time AS tm, punch_io AS io, store_store_id AS st FROM punch WHERE employee_employee_id = $employee_id) wt2 ON ( ".
			    "wt1.dt = wt2.dt AND wt1.st = wt2.st AND wt1.io = 'I' AND wt2.io = 'O' AND wt1.tm < wt2.tm ".
			") ".
		    "WHERE ( ".
			"wt1.io = 'I' ".
		    ") ".
		    "GROUP BY wt1.dt, wt1.st, wt1.tm ".
		") wt3 ".
		"GROUP BY wt3.dt, wt3.st, wt3.otm ".
	    ") wt4 ".
	    "WHERE wt4.dt = '".$date->format('Y-m-d')."'";

	//echo $sql;
	//exit;

        $q = new DBQuery($sql);
        if ($row = $q->fetch())
        {
	    $begin_time = $row['begin_time'];
	    $end_time = $row['end_time'];
	    $work_time = $row['work_time'];
	    return true;
        }
	else
	{
	    return false;
	}
    }

    static function period_summary($started, $ended, $employee_id, &$work_time)
    {
	if (!isset($started) or !isset($ended))
	{
	    return false;
	}

	$sql =
	    "SELECT ".
		"SEC_TO_TIME(SUM(wt5.work_time)) AS work_time ".
	    "FROM ( ".
	    "SELECT ".
		"wt4.dt, ".
		"SUM(wt4.otm - wt4.itm) AS work_time ".
	    "FROM ( ".
		"SELECT wt3.dt, wt3.st, MIN(wt3.itm) AS itm, IFNULL(wt3.otm, MIN(wt3.itm) + 60) AS otm FROM ( ".
		    "SELECT wt1.dt, wt1.st, TIME_TO_SEC(wt1.tm) AS itm, MIN(TIME_TO_SEC(wt2.tm)) AS otm ".
		    "FROM ".
			"(SELECT punch_date AS dt, punch_time AS tm, punch_io AS io, store_store_id AS st FROM punch WHERE employee_employee_id = $employee_id) wt1 LEFT JOIN (SELECT punch_date AS dt, punch_time AS tm, punch_io AS io, store_store_id AS st FROM punch WHERE employee_employee_id = $employee_id) wt2 ON ( ".
			    "wt1.dt = wt2.dt AND wt1.st = wt2.st AND wt1.io = 'I' AND wt2.io = 'O' AND wt1.tm < wt2.tm ".
			") ".
		    "WHERE ( ".
			"wt1.io = 'I' ".
		    ") ".
		    "GROUP BY wt1.dt, wt1.st, wt1.tm ".
		") wt3 ".
		"GROUP BY wt3.dt, wt3.st, wt3.otm ".
	    ") wt4 ".
	    "WHERE wt4.dt >= '".$started->format('Y-m-d')."' AND wt4.dt <= '".$ended->format('Y-m-d')."' ".
	    "GROUP BY wt4.dt ".
	    ") wt5 ";

	//echo $sql;
	//exit;

        $q = new DBQuery($sql);
        if ($row = $q->fetch())
        {
            $work_time = $row['work_time'];
	    return true;
        }
	else
	{
	    return false;
	}
    }

    private static function date_store_data_array($date, $employee_id, $store_id, &$data_array)
    {
	$sql =
	    "SELECT punch_time, punch_io FROM punch ".
	    "WHERE employee_employee_id = $employee_id AND store_store_id = $store_id AND punch_date = '".$date->format('Y-m-d')."' ".
	    "ORDER BY punch_time";

        $q = new DBQuery($sql);
        $i = 0;
        while ($row = $q->fetch())
        {
            $data_array[$i]['punch_time'] = $row['punch_time'];
            $data_array[$i]['punch_io'] = $row['punch_io'];
            $i++;
        }
    }

    private static function date_data_array($date, $employee_id, &$data_array)
    {
	$sql =
	    "SELECT p.punch_time, p.punch_io, p.store_store_id, s.code FROM punch p JOIN store s ON (p.store_store_id = s.store_id) ".
	    "WHERE p.employee_employee_id = $employee_id AND p.punch_date = '".$date->format('Y-m-d')."' ".
	    "ORDER BY p.punch_time";

	//echo $sql;
	//exit;

        $q = new DBQuery($sql);
        $i = 0;
        while ($row = $q->fetch())
        {
            $data_array[$i]['punch_time'] = $row['punch_time'];
            $data_array[$i]['punch_io'] = $row['punch_io'];
            $data_array[$i]['store_id'] = $row['store_store_id'];
            $data_array[$i]['store_code'] = $row['code'];
            $i++;
        }
    }

    private static function period_data_array($started, $ended, $employee_id, &$data_array)
    {
	if (!isset($started) or !isset($ended))
	{
	    return;
	}

	$sql =
	    "SELECT ".
		"wt4.dt AS date, ".
		"SEC_TO_TIME(MIN(WT4.itm)) AS begin_time, ".
		"SEC_TO_TIME(MAX(wt4.otm)) AS end_time, ".
		"SEC_TO_TIME(SUM(wt4.otm - wt4.itm)) AS work_time ".
	    "FROM ( ".
		"SELECT wt3.dt, wt3.st, MIN(wt3.itm) AS itm, IFNULL(wt3.otm, MIN(wt3.itm) + 60) AS otm FROM ( ".
		    "SELECT wt1.dt, wt1.st, TIME_TO_SEC(wt1.tm) AS itm, MIN(TIME_TO_SEC(wt2.tm)) AS otm ".
		    "FROM ".
			"(SELECT punch_date AS dt, punch_time AS tm, punch_io AS io, store_store_id AS st FROM punch WHERE employee_employee_id = $employee_id) wt1 LEFT JOIN (SELECT punch_date AS dt, punch_time AS tm, punch_io AS io, store_store_id AS st FROM punch WHERE employee_employee_id = $employee_id) wt2 ON ( ".
			    "wt1.dt = wt2.dt AND wt1.st = wt2.st AND wt1.io = 'I' AND wt2.io = 'O' AND wt1.tm < wt2.tm ".
			") ".
		    "WHERE ( ".
			"wt1.io = 'I' ".
		    ") ".
		    "GROUP BY wt1.dt, wt1.st, wt1.tm ".
		") wt3 ".
		"GROUP BY wt3.dt, wt3.st, wt3.otm ".
	    ") wt4 ".
	    "WHERE wt4.dt >= '".$started->format('Y-m-d')."' AND wt4.dt <= '".$ended->format('Y-m-d')."' ".
	    "GROUP BY wt4.dt ".
	    "ORDER BY wt4.dt ";

        $date = $started;

        $q = new DBQuery($sql);
        $i = 0;
        while ($row = $q->fetch())
        {
            $next_date = new DateTime($row['date']);
            $begin_time = '';
            $end_time = '';
            $work_time = '';

	    $begin_time = $row['begin_time'];
            $end_time = $row['end_time'];
	    $work_time = $row['work_time'];
            while ($date < $next_date)
            {
                $data_array[$i]['date'] = $date->format('m/d/Y');
                $data_array[$i]['dateYMD'] = $date->format('Y-m-d');
                $data_array[$i]['begin_time'] = '';
                $data_array[$i]['end_time'] = '';
                $data_array[$i]['work_time'] = '';
                $i++;
                $date->add(new DateInterval('P1D'));
            }
            $date = $next_date;
            $data_array[$i]['date'] = $date->format('m/d/Y');
            $data_array[$i]['dateYMD'] = $date->format('Y-m-d');
            $data_array[$i]['begin_time'] = $begin_time;
            $data_array[$i]['end_time'] = $end_time;
            $data_array[$i]['work_time'] = $work_time;
            $i++;
            $date->add(new DateInterval('P1D'));
        }
        while ($date <= $ended)
        {
            $data_array[$i]['date'] = $date->format('m/d/Y');
            $data_array[$i]['dateYMD'] = $date->format('Y-m-d');
                $data_array[$i]['begin_time'] = '';
                $data_array[$i]['end_time'] = '';
                $data_array[$i]['work_time'] = '';
            $i++;
            $date->add(new DateInterval('P1D'));
        }
    }

    static function date_store_html_table($date, $time, $employee_id, $store_id, $editable = NULL)
    {
        global $web_path;

        unset($data_array);

        self::date_store_data_array($date, $employee_id, $store_id, $data_array);
	self::date_store_summary($date, $time, $employee_id, $begin_time, $end_time, $work_time);

	$a ='<tr><th colspan="2">'.$date->format('m/d/Y').'</th>'.(isset($editable) ? '<th></th>' : '').'</tr>';
        for ($i = 0; $i < count($data_array); $i++)
        {
	    if (isset($data_array[$i]['punch_time']))
	    {
		$a .=
		    '<tr><td>'.
		    (($data_array[$i]['punch_io'] == 'I') ?
			'<font color="green">IN:</font></td><td><font color="green">'.$data_array[$i]['punch_time'].'</font>' :
			'<font color="red">OUT:</font></td><td><font color="red">'.$data_array[$i]['punch_time'].'</font>'
		    ).
		    '</td>'.
		    (isset($editable) ?
			'<td align="right">'.
			    '<img border="0" src="'.$web_path.'image/delete.gif" alt="" title="Delete" onclick="DeletePunch('.$store_id.", '".$date->format('Y-m-d')."', '".$data_array[$i]['punch_time']."'".');">'.
			'</td>' : ''
		    ).
		    '</tr>';
	    }
        }
        $a .= '<tr class="total"><td>IN:</td><td>'.$begin_time.'</td>'.(isset($editable) ? '<td></td>' : '').'</tr>';
        $a .= '<tr class="total"><td>OUT:</td><td>'.$end_time.'</td>'.(isset($editable) ? '<td></td>' : '').'</tr>';
        $a .= '<tr class="total"><td>TOTAL:</td><td>'.$work_time.'</td>'.(isset($editable) ? '<td></td>' : '').'</tr>';

	if ($editable)
	{
	    $a .= '<tr><td><font color="green">IN:</font></td><td><input type="text" id="punch_in_time" name="punch_in_time" style="width: 60px;"></td><td><img border="0" src="'.$web_path.'image/add.gif" alt="" title="Add" onclick="AddPunchIn('.$store_id.", '".$date->format('Y-m-d')."');".'"></td></tr>';
	    $a .= '<tr><td><font color="red">OUT:</font></td><td><input type="text" id="punch_out_time" name="punch_out_time" style="width: 60px;"></td><td><img border="0" src="'.$web_path.'image/add.gif" alt="" title="Add" onclick="AddPunchOut('.$store_id.", '".$date->format('Y-m-d')."');".'"></td></tr>';
	}
	
	return $a;
    }

    static function date_html_table($date, $time, $employee_id, $editable = NULL)
    {
        global $web_path;

        unset($data_array);

        self::date_data_array($date, $employee_id, $data_array);
	self::date_summary($date, $time, $employee_id, $begin_time, $end_time, $work_time);

	$a ='<tr><th colspan="3">'.$date->format('m/d/Y').'</th>'.(isset($editable) ? '<th></th>' : '').'</tr>';
        for ($i = 0; $i < count($data_array); $i++)
        {
	    if (isset($data_array[$i]['punch_time']))
	    {
		$a .=
		    '<tr><td>Store: '.htmlentities($data_array[$i]['store_code']).'</td><td>'.
		    (($data_array[$i]['punch_io'] == 'I') ?
			'<font color="green">IN:</font></td><td><font color="green">'.$data_array[$i]['punch_time'].'</font>' :
			'<font color="red">OUT:</font></td><td><font color="red">'.$data_array[$i]['punch_time'].'</font>'
		    ).
		    '</td>'.
		    (isset($editable) ?
			'<td align="right">'.
			    '<img border="0" src="'.$web_path.'image/delete.gif" alt="" title="Delete" onclick="DeletePunch('."'".$date->format('Y-m-d')."', '".$data_array[$i]['punch_time']."'".');">'.
			'</td>' : ''
		    ).
		    '</tr>';
	    }
        }

        $a .= '<tr class="total"><td></td><td>IN:</td><td>'.$begin_time.'</td>'.(isset($editable) ? '<td></td>' : '').'</tr>';
        $a .= '<tr class="total"><td></td><td>OUT:</td><td>'.$end_time.'</td>'.(isset($editable) ? '<td></td>' : '').'</tr>';
        $a .= '<tr class="total"><td></td><td>TOTAL:</td><td>'.$work_time.'</td>'.(isset($editable) ? '<td></td>' : '').'</tr>';

	if ($editable)
	{
	    $a .= '<tr><td><input type="text" id="punch_in_store_code" name="punch_in_store_code" style="width: 60px;"></td><td><font color="green">IN:</font></td><td><input type="text" id="punch_in_time" name="punch_in_time" style="width: 60px;"></td><td><img border="0" src="'.$web_path.'image/add.gif" alt="" title="Add" onclick="AddPunchIn('."'".$date->format('Y-m-d')."');".'"></td></tr>';
	    $a .= '<tr><td><input type="text" id="punch_out_store_code" name="punch_out_store_code" style="width: 60px;"></td><td><font color="red">OUT:</font></td><td><input type="text" id="punch_out_time" name="punch_out_time" style="width: 60px;"></td><td><img border="0" src="'.$web_path.'image/add.gif" alt="" title="Add" onclick="AddPunchOut('."'".$date->format('Y-m-d')."');".'"></td></tr>';
	}
	
	return $a;
    }

    static function period_html_table($started, $ended, $employee_id, $clickable = NULL)
    {
        global $web_path;

        unset($data_array);
        unset($work_time);
        self::period_data_array($started, $ended, $employee_id, $data_array);
	self::period_summary($started, $ended, $employee_id, $work_time);

        $a = '<thead><tr><th>Date</th><th>IN</th><th>OUT</th><th>TOTAL</th>'.(isset($clickable) ? '<th></th>' : '').'<tr></thead><tbody>';
        for ($i = 0; $i < count($data_array); $i++)
        {
            $a .=
                '<tr'.(($i%2) ? ' class="alt_row"' : '').' onclick="populateDateTable('."'".$data_array[$i]['dateYMD']."'".');">'.
                '<td>'.$data_array[$i]['date'].'</td>'.
                '<td>'.$data_array[$i]['begin_time'].'</td>'.
                '<td>'.$data_array[$i]['end_time'].'</td>'.
                '<td>'.$data_array[$i]['work_time'].'</td>'.
		(isset($clickable) ?
		    '<td>'.
                        '<img border="0" src="'.$web_path.'image/quick.gif" alt="" title="Details" onclick="populateDateTable('."'".$data_array[$i]['dateYMD']."'".');">'.
		    '</td>' : ''
		).
		'</tr>';
        }
        $a .= '<tr class="total"><td>TOTAL</td><td></td><td></td><td>'.$work_time.'</td>'.(isset($clickable) ? '<td></td>' : '').'</tr>';
        $a .= '</tbody>';
        return $a;
    }

    static function period_export_to_excel($started, $ended, $employee_id)
    {
        global $web_path;

        unset($data_array);
        unset($work_time);
        self::period_data_array($started, $ended, $employee_id, $data_array);
	self::period_summary($started, $ended, $employee_id, $work_time);

        xlsHeaders('Work Time');
        xlsBOF();

        xlsWriteLabel(0, 0, BOEmployee::name_by_id($employee_id));

        xlsWriteLabel(2, 0, 'Date');
        xlsWriteLabel(2, 1, 'IN');
        xlsWriteLabel(2, 2, 'OUT');
        xlsWriteLabel(2, 3, 'TOTAL');

        for ($i = 0; $i < count($data_array); $i++)
        {
            xlsWriteLabel($i + 3, 0, $data_array[$i]['date']);
            xlsWriteLabel($i + 3, 1, $data_array[$i]['begin_time']);
            xlsWriteLabel($i + 3, 2, $data_array[$i]['end_time']);
            xlsWriteLabel($i + 3, 3, $data_array[$i]['work_time']);
        }

        $i = 3 + count($data_array);
        xlsWriteLabel($i, 0, 'TOTAL');
        xlsWriteLabel($i, 3, $work_time);

        xlsEOF();
        exit;
    }

    static function period_export_to_pdf($started, $ended, $employee_id)
    {
        global $web_path;

        unset($data_array);
        unset($work_time);
        self::period_data_array($started, $ended, $employee_id, $data_array);
	self::period_summary($started, $ended, $employee_id, $work_time);

        try {
            $p = new PDFlib();
            if ($p->begin_document("", "") == 0) {
                die("Error: " . $p->get_errmsg());
            }
            $p->set_info("Creator", "Time Card");
            $p->set_info("Author", "Time Card");
            $p->set_info("Title", "Work Time");

            $p->begin_page_ext(612, 792, "");

            $font = $p->load_font("Courier","iso8859-1", "");

            $p->setfont($font, 12.0);
            $p->set_text_pos(72, 792 - 72);

            $p->show("Work Time");
            $p->continue_text('');
            $p->continue_text(BOEmployee::name_by_id($employee_id));
            $p->continue_text('');
            $p->continue_text('Date        IN          OUT       TOTAL');
            $p->continue_text('');

            for ($i = 0; $i < count($data_array); $i++)
            {
                $p->continue_text(
                    str_pad($data_array[$i]['date'], 12, ' ').
                    str_pad($data_array[$i]['begin_time'], 12, ' ').
                    str_pad($data_array[$i]['end_time'], 10, ' ').
                    $data_array[$i]['work_time']
                );
            }
            $p->continue_text('');
            $p->continue_text(
                str_pad('TOTAL', 34, ' ').
                str_pad($work_time, 11, ' ').
                $total_time
            );

            $p->end_page_ext("");
            $p->end_document("");
            $buf = $p->get_buffer();
            $len = strlen($buf);

            header("Content-type: application/pdf");
            header("Content-Length: $len");
            header("Content-Disposition: attachment; filename=work_time.pdf");
            print $buf;
        }
        catch (PDFlibException $e) {
            die("PDFlib exception occurred in application:\n" .
                "[" . $e->get_errnum() . "] " . $e->get_apiname() . ": " .
                $e->get_errmsg() . "\n");
        }
        catch (Exception $e) {
            die($e);
        }
        $p = 0;
        exit;
    }

    static function period_export_to_fpdf($started, $ended, $employee_id)
    {
        global $web_path;

        unset($data_array);
        unset($work_time);
        self::period_data_array($started, $ended, $employee_id, $data_array);
	self::period_summary($started, $ended, $employee_id, $work_time);

	$pdf = new FPDF();
	$pdf->SetMargins(25, 25);
	$pdf->AddPage();

	$pdf->SetFont('Arial', '', 12);
	$pdf->Cell(80, 7, 'Time Card System', 0, 0, 'L', false);

	$dt = new DateTime();

	$pdf->Cell(40, 7, $dt->format('m/d/Y'), 0, 0, 'R', false);
	$pdf->Cell(40, 7, $dt->format('H:i:s'), 0, 1, 'R', false);
	$pdf->Ln();

	$pdf->SetFont('Arial', 'B', 14);
	$pdf->Cell(160, 7, 'Work Time - '.BOEmployee::name_by_id($employee_id), 0, 1, 'C', false);
	$pdf->Ln();

	$pdf->SetFont('Arial', '', 12);
	$pdf->SetFillColor(245, 245, 245);
	$pdf->SetTextColor(0);
	$pdf->SetDrawColor(0, 0, 0);
	$pdf->SetLineWidth(.1);
	$pdf->SetFont('', 'B');

	$pdf->Cell(20);
	$pdf->Cell(30, 7, 'Date', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'IN', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'OUT', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'Total', 1, 0, 'C', true);
	$pdf->Ln();

	$pdf->SetTextColor(0);
	$pdf->SetFont('');

        for ($i = 0; $i < count($data_array); $i++)
        {
	    $pdf->Cell(20);
            $pdf->Cell(30, 6, $data_array[$i]['date'], 1, 0, 'L', false);
	    $pdf->Cell(30, 6, $data_array[$i]['begin_time'], 1, 0, 'R', false);
	    $pdf->Cell(30, 6, $data_array[$i]['end_time'], 1, 0, 'R', false);
	    $pdf->Cell(30, 6, $data_array[$i]['work_time'], 1, 0, 'R', false);
	    $pdf->Ln();
	}

	$pdf->SetFont('', 'B');

	$pdf->Cell(20);
	$pdf->Cell(90, 7, 'TOTAL', 'TLB', 0, 'C', true);
        $pdf->Cell(30, 7, $work_time, 'TRB', 0, 'R', true);
	$pdf->Ln();

	$pdf->Output();
	exit;
    }

}

?>
