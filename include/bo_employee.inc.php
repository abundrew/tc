<?php

class BOEmployee {
    public $employee_id;
    public $code;
    public $first_name;
    public $last_name;
    public $store_store_id;
    public $job_job_id;

    function __construct($employee_id = 0)
    {
        if ($employee_id)
        {
            $this->get($employee_id);
        }
        else
        {
            $this->employee_id = NULL;
            $this->code = NULL;
            $this->first_name = NULL;
            $this->last_name = NULL;
            $this->store_store_id = NULL;
            $this->job_job_id = NULL;
        }
    }

    private function get($employee_id)
    {
		$this->employee_id = NULL;
        $this->code = NULL;
        $this->first_name = NULL;
		$this->last_name = NULL;
        $this->store_store_id = NULL;
        $this->job_job_id = NULL;

		$sql = "
			SELECT
				employee_id,
				code,
				first_name,
				last_name,
				store_store_id,
				job_job_id
			FROM
				employee
			WHERE
				employee_id = '$employee_id'
		";

        $q = new DBQuery($sql);
        if ($row = $q->fetch())
        {
            $this->employee_id = $row['employee_id'];
            $this->code = $row['code'];
            $this->first_name = $row['first_name'];
            $this->last_name = $row['last_name'];
            $this->store_store_id = $row['store_store_id'];
            $this->job_job_id = $row['job_job_id'];
            return true;
        }
        else
        {
            return false;
        }
    }

    function insert()
    {
		return self::insert_param($this->code, $this->first_name, $this->last_name, $this->store_id, $this-job_id);
    }

    static function insert_param($code, $first_name, $last_name, $store_id, $job_id)
    {
		global $config;

		$sql = "
			INSERT INTO employee (
				code, first_name, last_name, pwd_md5, store_store_id, job_job_id
			) VALUES (
				'".DBConnection::db_real_escape_string($code)."',
				'".DBConnection::db_real_escape_string($first_name)."',
				'".DBConnection::db_real_escape_string($last_name)."',
				'".DBConnection::db_real_escape_string(md5($config['default_pwd']))."',
				'$store_id',
				'$job_id'
			)
		";

        $q = new DBQuery($sql, true);
        return !($q->error());
    }

    function update()
    {
		return self::update_param($this->employee_id, $this->code, $this->first_name, $this->last_name, $this->store_id, $this-job_id);
    }

    static function update_param($employee_id, $code, $first_name, $last_name, $store_id, $job_id)
    {
		$sql = "
			UPDATE employee SET
				code = '".DBConnection::db_real_escape_string($code)."',
				first_name = '".DBConnection::db_real_escape_string($first_name)."',
				last_name = '".DBConnection::db_real_escape_string($last_name)."',
				store_store_id = '$store_id',
				job_job_id = '$job_id'
            WHERE
				employee_id = '$employee_id'
		";

        $q = new DBQuery($sql, true);
        return !($q->error());
    }

    function delete()
    {
		return self::delete_param($this->employee_id);
    }

    static function delete_param($employee_id)
    {
        if (isset($employee_id))
        {
			$sql = "
				DELETE FROM employee WHERE employee_id = '$employee_id'
			";

            new DBQuery($sql);
        }
    }

    private static function data_array(&$data_array, $store_id = NULL)
    {
		$sql = "
			SELECT
				e.employee_id,
				e.code,
				e.first_name,
				e.last_name,
				e.store_store_id,
				s.code AS store_code,
				e.job_job_id,
				j.description AS job_description
            FROM
				employee e
				JOIN store s ON (s.store_id = e.store_store_id)
				JOIN job j ON (j.job_id = e.job_job_id)
				".(isset($store_id) ? ("WHERE e.store_store_id = '".$store_id."' ") : "")."
            ORDER BY
				e.last_name, e.first_name, e.code
		";

	//echo $sql;
	//exit;

        $q = new DBQuery($sql);
        $i = 0;
        while ($row = $q->fetch())
        {
            $data_array[$i]['employee_id'] = $row['employee_id'];
            $data_array[$i]['code'] = $row['code'];
            $data_array[$i]['first_name'] = $row['first_name'];
            $data_array[$i]['last_name'] = $row['last_name'];
            $data_array[$i]['store_id'] = $row['store_store_id'];
            $data_array[$i]['store_code'] = $row['store_code'];
            $data_array[$i]['job_id'] = $row['job_job_id'];
            $data_array[$i]['job_description'] = $row['job_description'];
            $i++;
        }
    }

    static function option_array($store_id, $selected_id)
    {
        unset($data_array);
        self::data_array($data_array, $store_id);
        $a = "";
        for ($i = 0; $i < count($data_array); $i++)
        {
            $employee_id = $data_array[$i]['employee_id'];
            $code = $data_array[$i]['code'];
            $first_name = $data_array[$i]['first_name'];
            $last_name = $data_array[$i]['last_name'];
            $a .= '<option value="'.htmlentities($employee_id).'"'.($selected_id==$employee_id ? ' selected' : '').'>'.htmlentities($code.' - '.$last_name.', '.$first_name).'</option>';
        }
        return $a;
    }

    static function html_table($store_id = NULL, $editable = 0)
    {
        global $web_path;

        unset($data_array);
        self::data_array($data_array, $store_id);
        $a =
            '<thead><tr><th>ID</th><th>Name</th><th>Job Description</th><th>Store</th>'.
            ($editable ? '<th><div align="right"><input type="image" name="add_employee" src="'.$web_path.'image/add.gif" alt="" title="Add"></div></th>' : '').
            '</tr></thead><tbody>';
        for ($i = 0; $i < count($data_array); $i++)
        {
            $employee_id = $data_array[$i]['employee_id'];
            $code = htmlentities($data_array[$i]['code']);
            $name = htmlentities($data_array[$i]['last_name'].', '.$data_array[$i]['first_name']);
            $store_code = htmlentities($data_array[$i]['store_code']);
            $job_description = htmlentities($data_array[$i]['job_description']);
            $a .=
                '<tr'.(($i%2) ? ' class="alt_row"' : '').'>'.
                '<td>'.$code.'</td>'.
                '<td>'.$name.'</td>'.
                '<td>'.$job_description.'</td>'.
                '<td>'.$store_code.'</td>'.
		($editable ?
                '<td align="right">'.
                    '<input type="image" name="edit_employee" src="'.$web_path.'image/edit.gif" alt="" title="Edit" onclick="document.form.row_id.value = '.$employee_id.';">'.
                    '<input type="image" name="delete_employee" src="'.$web_path.'image/delete.gif" alt="" title="Delete" onclick="document.form.row_id.value = '.$employee_id.'; return confirm('.
                        "'Delete employee ".htmlentities($name)." ?'".
                    ');">'.
                '</td>'
		: '').
                '</tr>';
        }
        $a .= '</tbody>';
        return $a;
    }

    static function export_to_excel($store_id = NULL)
    {
        unset($data_array);
        self::data_array($data_array, $store_id);

        xlsHeaders('Employees');
        xlsBOF();

        if (isset($store_id))
        {
            xlsWriteLabel(0, 0, 'Store: '.BOStore::code_by_id($store_id));
        }

        xlsWriteLabel(2, 0, 'Code');
        xlsWriteLabel(2, 1, 'Name');
        xlsWriteLabel(2, 2, 'Job Description');
        xlsWriteLabel(2, 3, 'Store');

        for ($i = 0; $i < count($data_array); $i++)
        {
            xlsWriteLabel($i + 3, 0, $data_array[$i]['code']);
            xlsWriteLabel($i + 3, 1, $data_array[$i]['last_name'].', '.$data_array[$i]['first_name']);
            xlsWriteLabel($i + 3, 2, $data_array[$i]['job_description']);
            xlsWriteLabel($i + 3, 3, $data_array[$i]['store_code']);
        }

        xlsEOF();
        exit;
    }

    static function export_to_pdf($store_id = NULL)
    {
        unset($data_array);
        self::data_array($data_array, $store_id);

        try {
            $p = new PDFlib();
            if ($p->begin_document("", "") == 0) {
                die("Error: " . $p->get_errmsg());
            }
            $p->set_info("Creator", "Time Card");
            $p->set_info("Author", "Time Card");
            $p->set_info("Title", "Employees");

            $p->begin_page_ext(612, 792, "");

            $font = $p->load_font("Courier","iso8859-1", "");

            $p->setfont($font, 10.0);
            $p->set_text_pos(72, 792 - 72);

            $p->show("Employees");
            $p->continue_text('');
            $p->continue_text(
		str_pad('Code', 20, ' ').
		str_pad('Name', 40, ' ').
		str_pad('Job', 20, ' ').
		'Store');
            $p->continue_text(
		str_pad('', 20, ' ').
		str_pad('', 40, ' ').
		str_pad('Description', 20, ' ').
		'');
            $p->continue_text('');

            for ($i = 0; $i < count($data_array); $i++)
            {
                $p->continue_text(
                    str_pad($data_array[$i]['code'], 20, ' ').
                    str_pad($data_array[$i]['last_name'].', '.$data_array[$i]['first_name'], 40, ' ').
                    str_pad($data_array[$i]['job_description'], 20, ' ').
                    $data_array[$i]['store_code']
                );
            }

            $p->end_page_ext("");
            $p->end_document("");
            $buf = $p->get_buffer();
            $len = strlen($buf);

            header("Content-type: application/pdf");
            header("Content-Length: $len");
            header("Content-Disposition: attachment; filename=employees.pdf");
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

    static function export_to_fpdf($store_id = NULL)
    {
        unset($data_array);
        self::data_array($data_array, $store_id);

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
	$pdf->Cell(160, 7, 'Employees', 0, 1, 'C', false);
	$pdf->Ln();

	$pdf->SetFont('Arial', '', 12);
	$pdf->SetFillColor(245, 245, 245);
	$pdf->SetTextColor(0);
	$pdf->SetDrawColor(0, 0, 0);
	$pdf->SetLineWidth(.1);
	$pdf->SetFont('', 'B');

	$pdf->Cell(30, 7, 'ID', 1, 0, 'C', true);
        $pdf->Cell(60, 7, 'Name', 1, 0, 'C', true);
        $pdf->Cell(40, 7, 'Job Description', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'Store Number', 1, 0, 'C', true);
	$pdf->Ln();

	$pdf->SetTextColor(0);
	$pdf->SetFont('');

        for ($i = 0; $i < count($data_array); $i++)
        {
	    $pdf->Cell(30, 6, $data_array[$i]['code'], 1, 0, 'L', false);
	    $pdf->Cell(60, 6, $data_array[$i]['last_name'].', '.$data_array[$i]['first_name'], 1, 0, 'L', false);
	    $pdf->Cell(40, 6, $data_array[$i]['job_description'], 1, 0, 'L', false);
	    $pdf->Cell(30, 6, $data_array[$i]['store_code'], 1, 0, 'L', false);
	    $pdf->Ln();
	}
	$pdf->Output();
	exit;
    }

    static function id_by_code($employee_code)
    {
		$sql = "
			SELECT
				employee_id
			FROM
				employee
			WHERE
				code = '".DBConnection::db_real_escape_string($employee_code)."'
		";

        $q = new DBQuery($sql);
        if ($row = $q->fetch())
        {
	    return $row['employee_id'];
        }
        else
        {
            return 0;
        }
    }

    static function name_by_id($employee_id)
    {
		$sql = "
			SELECT
				e.first_name,
				e.last_name,
				j.description
			FROM
				employee e
				JOIN job j ON (j.job_id = e.job_job_id)
			WHERE
				employee_id = '$employee_id'
		";

        $q = new DBQuery($sql);
        if ($row = $q->fetch())
        {
	    return $row['first_name'].' '.$row['last_name'].' ('.$row['description'].')';
        }
        else
        {
            return '';
        }
    }
}

?>