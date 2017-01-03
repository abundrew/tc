<?php

class BOActivity
{

    static function activity_store_data_array($date, $time, $store_id, &$data_array, &$total_array)
    {
		$sql = "
			SELECT
				p.employee_employee_id,
				e.first_name,
				e.last_name,
				s.code AS store_code,
				j.description,
				p.punch_time,
				p.punch_io
			FROM
				punch p
				JOIN employee e ON (e.employee_id = p.employee_employee_id)
				JOIN job j ON (j.job_id = e.job_job_id)
				JOIN store s ON (s.store_id = e.store_store_id)
			WHERE
				p.store_store_id = '$store_id' AND
				p.punch_date = '".$date->format('Y-m-d')."'
			ORDER BY
				e.last_name,
				e.first_name,
				p.employee_employee_id,
				p.punch_time
			";

		$q = new DBQuery($sql);
		$i = 0;
		while ($row = $q->fetch())
		{
			$data_array[$i]['employee_id'] = $row['employee_employee_id'];
			$data_array[$i]['first_name'] = $row['first_name'];
			$data_array[$i]['last_name'] = $row['last_name'];
			$data_array[$i]['job'] = $row['description'];
			$data_array[$i]['store'] = $row['store_code'];
			$data_array[$i]['status'] = ($row['punch_io'] === 'I') ? 'IN' : 'OUT';
			$data_array[$i]['time'] = $row['punch_time'];
			$i++;
		}

		$sql = "
			SELECT
				wt4.ei AS employee_id,
				SEC_TO_TIME(SUM(wt4.otm - wt4.itm)) AS work_time
			FROM (
				SELECT
					wt3.st,
					wt3.ei,
					wt3.dt,
					MIN(wt3.itm) AS itm,
					IFNULL(wt3.otm, TIME_TO_SEC('".$time->format('H:i:s')."')) AS otm
				FROM (
					SELECT
						wt1.st,
						wt1.ei,
						wt1.dt,
						TIME_TO_SEC(wt1.tm) AS itm, MIN(TIME_TO_SEC(wt2.tm)) AS otm
					FROM (
						SELECT
							store_store_id AS st,
							employee_employee_id AS ei,
							punch_date AS dt,
							punch_time AS tm,
							punch_io AS io
						FROM punch
					) wt1
					LEFT JOIN (
						SELECT
							store_store_id AS st,
							employee_employee_id AS ei,
							punch_date AS dt,
							punch_time AS tm,
							punch_io AS io
						FROM punch
					) wt2 ON (
						wt1.st = wt2.st AND
						wt1.ei = wt2.ei AND
						wt1.dt = wt2.dt AND
						wt1.io = 'I' AND
						wt2.io = 'O' AND
						wt1.tm < wt2.tm
					)
					WHERE (
						wt1.io = 'I'
					)
					GROUP BY
						wt1.st,
						wt1.ei,
						wt1.dt,
						wt1.tm
				) wt3
				GROUP BY
					wt3.st,
					wt3.ei,
					wt3.dt,
					wt3.otm
			) wt4
			WHERE
				wt4.st = '$store_id' AND
				wt4.dt = '".$date->format('Y-m-d')."'
			GROUP BY
				wt4.ei
			";

        $q = new DBQuery($sql);
        $i = 0;
        while ($row = $q->fetch())
        {
            $total_array[$row['employee_id']] = $row['work_time'];
            $i++;
        }
    }

    static function activity_store_html_table($date, $time, $store_id)
    {
        global $web_path;

        unset($data_array);
        unset($total_array);
        self::activity_store_data_array($date, $time, $store_id, $data_array, $total_array);

	$a ='<tr><th>Employee Name</th><th>Job Description</th><th>Store Number</th><th>Status</th><th>Punch Time</th><th>Total Hours</th></tr>';
        for ($i = 0; $i < count($data_array); $i++)
        {
	    $employee_id = $data_array[$i]['employee_id'];
	    if ($i > 0 && $current_employee_id != $employee_id)
	    {
		$time = $total_array[$current_employee_id];
		$a .=
		    '<tr class="total">'.
		    '<td colspan="4" align="right">'.htmlentities($employee_name).'</td>'.
		    '<td></td>'.
		    '<td>'.htmlentities($time).'</td>'.
		'</tr>';
	    }
	    $current_employee_id = $employee_id;

	    $employee_name = $data_array[$i]['last_name'].', '.$data_array[$i]['first_name'];
	    $job = $data_array[$i]['job'];
	    $store = $data_array[$i]['store'];
	    $status = $data_array[$i]['status'];
	    $time = $data_array[$i]['time'];
	    $a .=
                '<tr'.(($i%2) ? ' class="alt_row"' : '').'>'.
		'<td>'.htmlentities($employee_name).'</td>'.
		'<td>'.htmlentities($job).'</td>'.
		'<td>'.htmlentities($store).'</td>'.
		'<td>'.$status.'</td>'.
		'<td>'.htmlentities($time).'</td>'.
		'<td></td>'.
		'</tr>';
	}
	if ($i > 0)
	{
	    $time = $total_array[$current_employee_id];
	    $a .=
		'<tr class="total">'.
		'<td colspan="4" align="right">'.htmlentities($employee_name).'</td>'.
		'<td></td>'.
		'<td>'.htmlentities($time).'</td>'.
		'</tr>';
	}
	return $a;
    }

    static function activity_store_export_to_excel($date, $time, $store_id)
    {
        unset($data_array);
        unset($total_array);
        self::activity_store_data_array($date, $time, $store_id, $data_array, $total_array);

        xlsHeaders('Activity Report');
        xlsBOF();

        xlsWriteLabel(0, 0, 'Store: '.BOStore::code_by_id($store_id));
        xlsWriteLabel(2, 0, 'Time Card Activity Report');
        xlsWriteLabel(3, 0, $date->format('m/d/Y'));
        xlsWriteLabel(4, 0, $time->format('H:i:s'));

	xlsWriteLabel(6, 0, 'Employee Name');
        xlsWriteLabel(6, 1, 'Job Description');
        xlsWriteLabel(6, 2, 'Store Number');
        xlsWriteLabel(6, 3, 'Status');
        xlsWriteLabel(6, 4, 'Punch Time');
        xlsWriteLabel(6, 5, 'Total Hours');

	$ofs = 0;

        for ($i = 0; $i < count($data_array); $i++)
        {
	    $employee_id = $data_array[$i]['employee_id'];
	    if ($i > 0 && $current_employee_id != $employee_id)
	    {
		$time = $total_array[$current_employee_id];

		xlsWriteLabel($i + $ofs + 8, 0, $employee_name);
		xlsWriteLabel($i + $ofs + 8, 5, $time);

		$ofs++;
	    }
	    $current_employee_id = $employee_id;

	    $employee_name = $data_array[$i]['last_name'].', '.$data_array[$i]['first_name'];
	    $job = $data_array[$i]['job'];
	    $store = $data_array[$i]['store'];
	    $status = $data_array[$i]['status'];
	    $time = $data_array[$i]['time'];

	    xlsWriteLabel($i + $ofs + 8, 0, $employee_name);
	    xlsWriteLabel($i + $ofs + 8, 1, $job);
	    xlsWriteLabel($i + $ofs + 8, 2, $store);
	    xlsWriteLabel($i + $ofs + 8, 3, $status);
	    xlsWriteLabel($i + $ofs + 8, 4, $time);
	}
	if ($i > 0)
	{
	    $time = $total_array[$current_employee_id];
	    xlsWriteLabel($i + $ofs + 8, 0, $employee_name);
	    xlsWriteLabel($i + $ofs + 8, 5, $time);
	}

        xlsEOF();
        exit;
    }

    static function activity_store_export_to_pdf($date, $time, $store_id)
    {
        unset($data_array);
        unset($total_array);
        self::activity_store_data_array($date, $time, $store_id, $data_array, $total_array);

        try {
            $p = new PDFlib();
            if ($p->begin_document("", "") == 0) {
                die("Error: " . $p->get_errmsg());
            }
            $p->set_info("Creator", "Time Card");
            $p->set_info("Author", "Time Card");
            $p->set_info("Title", "Activity Report");

            $p->begin_page_ext(612, 792, "");

            $font = $p->load_font("Courier","iso8859-1", "");

            $p->setfont($font, 8.0);
            $p->set_text_pos(72, 792 - 72);

            $p->show('Store: '.BOStore::code_by_id($store_id));
            $p->continue_text('');
            $p->continue_text('Time Card Activity Report');
            $p->continue_text($date->format('m/d/Y').'   '.$time->format('H:i:s'));
            $p->continue_text('');
            $p->continue_text(
		str_pad('Employee Name', 30, ' ').
		str_pad('Job', 20, ' ').
		str_pad('Store', 12, ' ').
		str_pad('Status', 7, ' ').
		str_pad('Punch', 10, ' ').
		'Total'
	    );
            $p->continue_text(
		str_pad('Name', 30, ' ').
		str_pad('Description', 20, ' ').
		str_pad('Number', 12, ' ').
		str_pad('', 7, ' ').
		str_pad('Time', 10, ' ').
		'Hours'
	    );
            $p->continue_text('');

	    for ($i = 0; $i < count($data_array); $i++)
	    {
		$employee_id = $data_array[$i]['employee_id'];
		if ($i > 0 && $current_employee_id != $employee_id)
		{
		    $time = $total_array[$current_employee_id];
	            $p->continue_text('');
                    $p->continue_text(str_pad($employee_name, 79, ' ').$time);
	            $p->continue_text('');
		}
		$current_employee_id = $employee_id;

		$employee_name = $data_array[$i]['last_name'].', '.$data_array[$i]['first_name'];
		$job = $data_array[$i]['job'];
		$store = $data_array[$i]['store'];
		$status = $data_array[$i]['status'];
		$time = $data_array[$i]['time'];

                $p->continue_text(
		    str_pad($employee_name, 30, ' ').
		    str_pad($job, 20, ' ').
		    str_pad($store, 12, ' ').
		    str_pad($status, 7, ' ').
		    str_pad($time, 10, ' ')
		);
	    }
	    if ($i > 0)
	    {
		$time = $total_array[$current_employee_id];
		$p->continue_text('');
		$p->continue_text(str_pad($employee_name, 79, ' ').$time);
	    }

            $p->end_page_ext("");
            $p->end_document("");
            $buf = $p->get_buffer();
            $len = strlen($buf);

            header("Content-type: application/pdf");
            header("Content-Length: $len");
            header("Content-Disposition: attachment; filename=activity.pdf");
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

    static function activity_store_export_to_fpdf($date, $time, $store_id)
    {
        unset($data_array);
        unset($total_array);
        self::activity_store_data_array($date, $time, $store_id, $data_array, $total_array);

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
	$pdf->Cell(160, 7, 'Activity Report - Store: '.BOStore::code_by_id($store_id), 0, 1, 'C', false);
	$pdf->Ln();

	$pdf->SetFont('Arial', '', 10);
	$pdf->SetFillColor(245, 245, 245);
	$pdf->SetTextColor(0);
	$pdf->SetDrawColor(0, 0, 0);
	$pdf->SetLineWidth(.1);
	$pdf->SetFont('', 'B');

	$pdf->Cell(50, 7, 'Employee Name', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'Job Description', 1, 0, 'C', true);
        $pdf->Cell(15, 7, 'Store', 1, 0, 'C', true);
        $pdf->Cell(15, 7, 'Status', 1, 0, 'C', true);
        $pdf->Cell(25, 7, 'Punch Time', 1, 0, 'C', true);
        $pdf->Cell(25, 7, 'Total Hours', 1, 0, 'C', true);
	$pdf->Ln();

	$pdf->SetTextColor(0);
	$pdf->SetFont('');

	for ($i = 0; $i < count($data_array); $i++)
	{
	    $employee_id = $data_array[$i]['employee_id'];
	    if ($i > 0 && $current_employee_id != $employee_id)
	    {
		$pdf->SetFont('', 'B');

		$time = $total_array[$current_employee_id];
		$pdf->Cell(135, 7, 'Total Hours for '.$employee_name, 'TLB', 0, 'C', true);
		$pdf->Cell(25, 7, $time, 'TRB', 0, 'R', true);
		$pdf->Ln();
	    }
	    $current_employee_id = $employee_id;

	    $pdf->SetFont('');

	    $employee_name = $data_array[$i]['last_name'].', '.$data_array[$i]['first_name'];
	    $job = $data_array[$i]['job'];
	    $store = $data_array[$i]['store'];
	    $status = $data_array[$i]['status'];
	    $time = $data_array[$i]['time'];

            $pdf->Cell(50, 6, $employee_name, 1, 0, 'L', false);
	    $pdf->Cell(30, 6, $job, 1, 0, 'L', false);
	    $pdf->Cell(15, 6, $store, 1, 0, 'L', false);
	    $pdf->Cell(15, 6, $status, 1, 0, 'L', false);
	    $pdf->Cell(25, 6, $time, 1, 0, 'R', false);
	    $pdf->Cell(25, 6, '', 'TLR', 0, 'L', false);
	    $pdf->Ln();

	}
	if ($i > 0)
	{
	    $time = $total_array[$current_employee_id];

	    $pdf->SetFont('', 'B');
	    
	    $pdf->Cell(135, 7, 'Total Hours for '.$employee_name, 'TLB', 0, 'C', true);
	    $pdf->Cell(25, 7, $time, 'TRB', 0, 'R', true);
	    $pdf->Ln();
	}

	$pdf->Output();
	exit;
    }

}

?>
