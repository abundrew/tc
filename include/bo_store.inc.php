<?php

class BOStore {
    public $store_id;
    public $code;
    public $description;

    function __construct($store_id = 0)
    {
        if ($store_id)
        {
            $this->get($store_id);
        }
        else
        {
            $this->store_id = NULL;
            $this->code = NULL;
            $this->description = NULL;
        }
    }

    private function get($store_id)
    {
        $this->store_id = NULL;
        $this->code = NULL;
        $this->description = NULL;

	$sql = "SELECT store_id, code, description FROM store WHERE store_id = '$store_id'";

        $q = new DBQuery($sql);
        if ($row = $q->fetch())
        {
            $this->store_id = $row['store_id'];
            $this->code = $row['code'];
            $this->description = $row['description'];
            return true;
        }
        else
        {
            return false;
        }
    }

    function insert()
    {
	return self::insert_param($this->code, $this->description);
    }

    static function insert_param($code, $description)
    {
	global $config;

	$sql =
            "INSERT INTO store (code, description) VALUES ('".
            DBConnection::db_real_escape_string($code)."', '".
            DBConnection::db_real_escape_string($description)."')";

	//echo $sql;
	//exit;

        $q = new DBQuery($sql, true);
        return !($q->error());
    }

    function update()
    {
	return self::update_param($this->store_id, $this->code, $this->description);
    }

    static function update_param($store_id, $code, $description)
    {
	$sql =
            "UPDATE store SET ".
            "code = '".DBConnection::db_real_escape_string($code)."', ".
            "description = '".DBConnection::db_real_escape_string($description)."' ".
            "WHERE store_id = '$store_id'";

        $q = new DBQuery($sql, true);
        return !($q->error());
    }

    function delete()
    {
	return self::delete_param($this->store_id);
    }

    static function delete_param($store_id)
    {
        if (isset($store_id))
        {
	    $sql = "DELETE FROM store WHERE store_id = '$store_id'";

            new DBQuery($sql);
        }
    }

    private static function data_array(&$data_array)
    {
	$sql = "SELECT store_id, code, description FROM store ORDER BY code";

        $q = new DBQuery($sql);
        $i = 0;
        while ($row = $q->fetch())
        {
            $data_array[$i]['store_id'] = $row['store_id'];
            $data_array[$i]['code'] = $row['code'];
            $data_array[$i]['description'] = $row['description'];
            $i++;
        }
    }

    static function option_array($selected_id)
    {
        unset($data_array);
        self::data_array($data_array);
        $a = "";
        for ($i = 0; $i < count($data_array); $i++)
        {
            $store_id = $data_array[$i]['store_id'];
            $code = $data_array[$i]['code'];
            $a .= '<option value="'.htmlentities($store_id).'"'.($selected_id==$store_id ? ' selected' : '').'>'.htmlentities($code).'</option>';
        }
        return $a;
    }

    static function html_table($editable = 0)
    {
        global $web_path;

        unset($data_array);
        self::data_array($data_array);
        $a =
            '<thead><tr><th>Number</th><th>Description</th>'.
            ($editable ? '<th><div align="right"><input type="image" name="add_store" src="'.$web_path.'image/add.gif" alt="" title="Add"></div></th>' : '').
            '</tr></thead><tbody>';
        for ($i = 0; $i < count($data_array); $i++)
        {
            $store_id = $data_array[$i]['store_id'];
            $code = htmlentities($data_array[$i]['code']);
            $description = htmlentities($data_array[$i]['description']);
            $a .=
                '<tr'.(($i%2) ? ' class="alt_row"' : '').'>'.
                '<td>'.$code.'</td>'.
                '<td>'.$description.'</td>'.
		($editable ?
                '<td align="right">'.
                    '<input type="image" name="edit_store" src="'.$web_path.'image/edit.gif" alt="" title="Edit" onclick="document.form.row_id.value = '.$store_id.';">'.
                    '<input type="image" name="delete_store" src="'.$web_path.'image/delete.gif" alt="" title="Delete" onclick="document.form.row_id.value = '.$store_id.'; return confirm('.
                        "'Delete store ".htmlentities($code)." ?'".
                    ');">'.
                '</td>'
		: '').
                '</tr>';
        }
        $a .= '</tbody>';
        return $a;
    }

    static function export_to_excel()
    {
        unset($data_array);
        self::data_array($data_array);

        xlsHeaders('Stores');
        xlsBOF();

        xlsWriteLabel(2, 0, 'Code');
        xlsWriteLabel(2, 1, 'Description');

        for ($i = 0; $i < count($data_array); $i++)
        {
            xlsWriteLabel($i + 3, 0, $data_array[$i]['code']);
            xlsWriteLabel($i + 3, 1, $data_array[$i]['description']);
        }

        xlsEOF();
        exit;
    }

    static function export_to_pdf()
    {
        unset($data_array);
        self::data_array($data_array);

        try {
            $p = new PDFlib();
            if ($p->begin_document("", "") == 0) {
                die("Error: " . $p->get_errmsg());
            }
            $p->set_info("Creator", "Time Card");
            $p->set_info("Author", "Time Card");
            $p->set_info("Title", "Stores");

            $p->begin_page_ext(612, 792, "");

            $font = $p->load_font("Courier","iso8859-1", "");

            $p->setfont($font, 10.0);
            $p->set_text_pos(72, 792 - 72);

            $p->show("Stores");
            $p->continue_text('');
            $p->continue_text('Code                Description');
            $p->continue_text('');

            for ($i = 0; $i < count($data_array); $i++)
            {
                $p->continue_text(
		    str_pad($data_array[$i]['code'], 20, ' ').
                    $data_array[$i]['description']
		);
	    }

            $p->end_page_ext("");
            $p->end_document("");
            $buf = $p->get_buffer();
            $len = strlen($buf);

            header("Content-type: application/pdf");
            header("Content-Length: $len");
            header("Content-Disposition: attachment; filename=stores.pdf");
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

    static function export_to_fpdf()
    {
        unset($data_array);
        self::data_array($data_array);

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
	$pdf->Cell(160, 7, 'Stores', 0, 1, 'C', false);
	$pdf->Ln();

	$pdf->SetFont('Arial', '', 12);
	$pdf->SetFillColor(245, 245, 245);
	$pdf->SetTextColor(0);
	$pdf->SetDrawColor(0, 0, 0);
	$pdf->SetLineWidth(.1);
	$pdf->SetFont('', 'B');

	$pdf->Cell(40, 7, 'Number', 1, 0, 'C', true);
        $pdf->Cell(120, 7, 'Description', 1, 0, 'C', true);
	$pdf->Ln();

	$pdf->SetTextColor(0);
	$pdf->SetFont('');

        for ($i = 0; $i < count($data_array); $i++)
        {
            $pdf->Cell(40, 6, $data_array[$i]['code'], 1, 0, 'L', false);
	    $pdf->Cell(120, 6, $data_array[$i]['description'], 1, 0, 'L', false);
	    $pdf->Ln();
	}
	$pdf->Output();
	exit;
    }

    static function code_by_id($store_id)
    {
	$sql = "SELECT code FROM store WHERE store_id = '$store_id'";

        $q = new DBQuery($sql);
        if ($row = $q->fetch())
        {
            return $row['code'];
        }
        else
        {
            return '';
        }
    }

    static function id_by_code($store_code)
    {
	$sql = "SELECT store_id FROM store WHERE code = '".DBConnection::db_real_escape_string($store_code)."'";

        $q = new DBQuery($sql);
        if ($row = $q->fetch())
        {
            return $row['store_id'];
        }
        else
        {
            return '';
        }
    }
}

?>