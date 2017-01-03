<?php

class BOStatus
{

    static function status_store_data_array($date, $store_id, &$data_array)
    {
	$sql =
	    "SELECT ".
		"p.employee_employee_id, e.first_name, e.last_name, j.description, p.punch_io, p.punch_date, p.punch_time ".
	    "FROM punch p JOIN ( ".
		"SELECT employee_employee_id, MAX(punch_time) AS max_punch_time ".
		"FROM punch ".
		"WHERE punch_date = '".$date->format('Y-m-d')."' AND store_store_id = '$store_id' ".
		"GROUP BY employee_employee_id ".
	    ") x ON ( ".
		"x.employee_employee_id = p.employee_employee_id AND ".
		"x.max_punch_time = p.punch_time ".
	    ") JOIN employee e ON ( ".
		"e.employee_id = p.employee_employee_id ".
	    ") JOIN job j ON ( ".
		"j.job_id = e.job_job_id ".
	    ") ".
	    "ORDER BY ".
	    "e.last_name, e.first_name";

        $q = new DBQuery($sql);
        $i = 0;
        while ($row = $q->fetch())
        {
            $data_array[$i]['first_name'] = $row['first_name'];
            $data_array[$i]['last_name'] = $row['last_name'];
            $data_array[$i]['job'] = $row['description'];
            $data_array[$i]['status'] = ($row['punch_io'] === 'I') ? 'IN' : 'OUT';
            $data_array[$i]['time'] = $row['punch_time'];
            $i++;
        }
    }

    static function status_store_html_table($date, $store_id)
    {
        global $web_path;

        unset($data_array);
        self::status_store_data_array($date, $store_id, $data_array);

	$a ='<tr><th>Employee Name</th><th>Job Description</th><th>Status</th><th>Last Punch Date</th><th>Last Punch Time</th></tr>';
        for ($i = 0; $i < count($data_array); $i++)
        {
	    $employee_name = $data_array[$i]['last_name'].', '.$data_array[$i]['first_name'];
	    $job = $data_array[$i]['job'];
	    $status = $data_array[$i]['status'];
	    $time = $data_array[$i]['time'];
	    $a .=
                '<tr'.(($i%2) ? ' class="alt_row"' : '').'>'.
		'<td>'.htmlentities($employee_name).'</td>'.
		'<td>'.htmlentities($job).'</td>'.
		'<td>'.$status.'</td>'.
		'<td>'.$date->format('m/d/Y').'</td>'.
		'<td>'.htmlentities($time).'</td>'.
		'</tr>';
	}
	return $a;
    }

    static function status_store_export_to_excel($date, $store_id)
    {
        unset($data_array);
        self::status_store_data_array($date, $store_id, $data_array);

        xlsHeaders('Current Status');
        xlsBOF();

        xlsWriteLabel(0, 0, 'Store: '.BOStore::code_by_id($store_id));
        xlsWriteLabel(2, 0, 'Employees Current Punch Date/Time Status');

	xlsWriteLabel(4, 0, 'Employee Name');
        xlsWriteLabel(4, 1, 'Job Description');
        xlsWriteLabel(4, 2, 'Status');
        xlsWriteLabel(4, 3, 'Last Punch Date');
        xlsWriteLabel(4, 4, 'Last Punch Time');

        for ($i = 0; $i < count($data_array); $i++)
        {
            xlsWriteLabel($i + 6, 0, $data_array[$i]['last_name'].', '.$data_array[$i]['first_name']);
            xlsWriteLabel($i + 6, 1, $data_array[$i]['job']);
            xlsWriteLabel($i + 6, 2, $data_array[$i]['status']);
            xlsWriteLabel($i + 6, 3, $date->format('m/d/Y'));
            xlsWriteLabel($i + 6, 4, $data_array[$i]['time']);
        }

        xlsEOF();
        exit;
    }

    static function status_store_export_to_pdf($date, $store_id)
    {
        unset($data_array);
        self::status_store_data_array($date, $store_id, $data_array);

        try {
            $p = new PDFlib();
            if ($p->begin_document("", "") == 0) {
                die("Error: " . $p->get_errmsg());
            }
            $p->set_info("Creator", "Time Card");
            $p->set_info("Author", "Time Card");
            $p->set_info("Title", "Current Status");

            $p->begin_page_ext(612, 792, "");

            $font = $p->load_font("Courier","iso8859-1", "");

            $p->setfont($font, 10.0);
            $p->set_text_pos(72, 792 - 72);

            $p->show('Store: '.BOStore::code_by_id($store_id));
            $p->continue_text('');
            $p->continue_text('Employees Current Punch Date/Time Status');
            $p->continue_text('');
            $p->continue_text(
		str_pad('Employee Name', 30, ' ').
		str_pad('Job Description', 20, ' ').
		str_pad('Status', 7, ' ').
		str_pad('Punch Date', 11, ' ').
		'Last Punch Time'
	    );
            $p->continue_text('');

            for ($i = 0; $i < count($data_array); $i++)
            {
                $p->continue_text(
		    str_pad($data_array[$i]['last_name'].', '.$data_array[$i]['first_name'], 30, ' ').
		    str_pad($data_array[$i]['job'], 20, ' ').
		    str_pad($data_array[$i]['status'], 7, ' ').
		    str_pad($date->format('m/d/Y'), 11, ' ').
		    $data_array[$i]['time']
		);
	    }

            $p->end_page_ext("");
            $p->end_document("");
            $buf = $p->get_buffer();
            $len = strlen($buf);

            header("Content-type: application/pdf");
            header("Content-Length: $len");
            header("Content-Disposition: attachment; filename=status.pdf");
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

    static function status_store_export_to_fpdf($date, $store_id)
    {
        unset($data_array);
        self::status_store_data_array($date, $store_id, $data_array);

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
	$pdf->Cell(160, 7, 'Current Punch Time Status - Store: '.BOStore::code_by_id($store_id), 0, 1, 'C', false);
	$pdf->Ln();

	$pdf->SetFont('Arial', '', 10);
	$pdf->SetFillColor(245, 245, 245);
	$pdf->SetTextColor(0);
	$pdf->SetDrawColor(0, 0, 0);
	$pdf->SetLineWidth(.1);
	$pdf->SetFont('', 'B');

	$pdf->Cell(50, 7, 'Employee Name', 1, 0, 'C', true);
        $pdf->Cell(35, 7, 'Job Description', 1, 0, 'C', true);
        $pdf->Cell(15, 7, 'Status', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'Last Punch Date', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'Last Punch Time', 1, 0, 'C', true);
	$pdf->Ln();

	$pdf->SetTextColor(0);
	$pdf->SetFont('');

        for ($i = 0; $i < count($data_array); $i++)
        {
            $pdf->Cell(50, 6, $data_array[$i]['last_name'].', '.$data_array[$i]['first_name'], 1, 0, 'L', false);
	    $pdf->Cell(35, 6, $data_array[$i]['job'], 1, 0, 'L', false);
	    $pdf->Cell(15, 6, $data_array[$i]['status'], 1, 0, 'L', false);
	    $pdf->Cell(30, 6, $date->format('m/d/Y'), 1, 0, 'L', false);
	    $pdf->Cell(30, 6, $data_array[$i]['time'], 1, 0, 'L', false);
	    $pdf->Ln();
	}
	$pdf->Output();
	exit;
    }

}

?>
