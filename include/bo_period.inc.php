<?php

class BOPeriod {

    public $period_id;
    public $period_number;
    public $started;
    public $ended;

    function __construct($period_id = 0)
    {
        if ($period_id)
        {
            $this->get($period_id);
        }
        else
        {
            $this->period_id = NULL;
            $this->period_number = NULL;
            $this->started = NULL;
            $this->ended = NULL;
        }
    }

    private function get($period_id)
    {
        $this->period_id = NULL;
        $this->period_number = NULL;
        $this->started = NULL;
        $this->ended = NULL;

	$sql = "SELECT period_id, period_number, started, ended FROM period WHERE period_id = '$period_id'";

        $q = new DBQuery($sql);
        if ($row = $q->fetch())
        {
            $this->period_id = $row['period_id'];
            $this->period_number = $row['period_number'];
            $this->started = new DateTime($row['started']);
            $this->ended = new DateTime($row['ended']);
            return true;
        }
        else
        {
            return false;
        }
    }

    function insert()
    {
	return self::insert_param($this->period_number, $this->started, $this->ended);
    }

    static function insert_param($period_number, $started, $ended)
    {
	$sql =
            "INSERT INTO period (period_number, started, ended) VALUES ('".
	    DBConnection::db_real_escape_string($period_number)."', '".
            $started->format('Y-m-d')."', '".
            $ended->format('Y-m-d')."')";

        $q = new DBQuery($sql, true);
        return !($q->error());
    }

    function update()
    {
	return self::update_param($this->period_id, $this->period_number, $this->started, $this->ended);
    }

    static function update_param($period_id, $period_number, $started, $ended)
    {
	$sql =
            "UPDATE period SET ".
	    "period_number = '".DBConnection::db_real_escape_string($period_number)."', ".
            "started = '".$started->format('Y-m-d')."', ".
            "ended = '".$ended->format('Y-m-d')."' ".
            "WHERE period_id = '$period_id'";

        $q = new DBQuery($sql , true);
        return !($q->error());
    }

    function delete()
    {
	return self::delete_param($this->period_id);
    }

    static function delete_param($period_id)
    {
        if (isset($period_id))
        {
	    $sql = "DELETE FROM period WHERE period_id = '$period_id'";

            new DBQuery($sql);
        }
    }

    private static function data_array(&$data_array)
    {
	$sql = "SELECT period_id, period_number, started, ended FROM period ORDER BY period_number";

        $q = new DBQuery($sql);
        $i = 0;
        while ($row = $q->fetch())
        {
            $data_array[$i]['period_id'] = $row['period_id'];
            $data_array[$i]['period_number'] = $row['period_number'];
            $data_array[$i]['started'] = $row['started'];
            $data_array[$i]['ended'] = $row['ended'];
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
            $period_id = $data_array[$i]['period_id'];
            $period_number = $data_array[$i]['period_number'];
            $started = new DateTime($data_array[$i]['started']);
            $ended = new DateTime($data_array[$i]['ended']);
            $a .=
                "<option value='".htmlentities($period_id)."' ".
                ($selected_id==$period_id ? ' selected' : '').">".
                '#'.htmlentities($period_number).': '.
                htmlentities($started->format('m/d/Y')).' - '.
                htmlentities($ended->format('m/d/Y')).
                "</option>";
        }
        return $a;
    }

    static function html_table($editable = 0)
    {
        global $web_path;
        
        unset($data_array);
        self::data_array($data_array);
        $a = 
            '<thead><tr><th>Number</th><th>Started</th><th>Ended</th>'.
            ($editable ? '<th><div align="right"><input type="image" name="add_period" src="'.$web_path.'image/add.gif" alt="" title="Add"></div></th>' : '').
            '</tr></thead><tbody>';
        for ($i = 0; $i < count($data_array); $i++)
        {
            $period_id = $data_array[$i]['period_id'];
            $period_number = (int)$data_array[$i]['period_number'];
            $started = new DateTime($data_array[$i]['started']);
            $ended = new DateTime($data_array[$i]['ended']);
            $a .=
                '<tr'.(($i%2) ? ' class="alt_row"' : '').'>'.
		'<td>'.htmlentities($period_number).'</td>'.
		'<td>'.htmlentities($started->format('m/d/Y')).'</td>'.
                '<td>'.htmlentities($ended->format('m/d/Y')).'</td>'.
		($editable ?
                '<td align="right">'.
                    '<input type="image" name="edit_period" src="'.$web_path.'image/edit.gif" alt="" title="Edit" onclick="document.form.row_id.value = '.$period_id.';">'.
                    '<input type="image" name="delete_period" src="'.$web_path.'image/delete.gif" alt="" title="Delete" value="'.$period_id.'" onclick="document.form.row_id.value = '.$period_id.'; return confirm('.
                        "'Delete period ".
			htmlentities($period_number).': '.
                        htmlentities($started->format('m/d/Y')).' - '.
                        htmlentities($ended->format('m/d/Y')).
                        " ?'".
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

        xlsHeaders('Periods');
        xlsBOF();

        xlsWriteLabel(0, 0, 'Periods');

        xlsWriteLabel(2, 0, 'Number');
        xlsWriteLabel(2, 1, 'Started');
        xlsWriteLabel(2, 2, 'Ended');

        for ($i = 0; $i < count($data_array); $i++)
        {
            $period_number = (int)$data_array[$i]['period_number'];
            $started = new DateTime($data_array[$i]['started']);
            $ended = new DateTime($data_array[$i]['ended']);

            xlsWriteLabel($i + 3, 0, $period_number);
            xlsWriteLabel($i + 3, 1, $started->format('m/d/Y'));
            xlsWriteLabel($i + 3, 2, $ended->format('m/d/Y'));
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
            $p->set_info("Title", "Periods");

            $p->begin_page_ext(612, 792, "");

            $font = $p->load_font("Courier","iso8859-1", "");

            $p->setfont($font, 12.0);
            $p->set_text_pos(72, 792 - 72);

            $p->show("Periods");
            $p->continue_text('');
            $p->continue_text('Number  Started     Ended');
            $p->continue_text('');

            for ($i = 0; $i < count($data_array); $i++)
            {
                $period_number = (int)$data_array[$i]['period_number'];
                $started = new DateTime($data_array[$i]['started']);
                $ended = new DateTime($data_array[$i]['ended']);

                $p->continue_text(
                    str_pad($period_number, 8, ' ').
                    str_pad($started->format('m/d/Y'), 12, ' ').
                    str_pad($ended->format('m/d/Y'), 12, ' ')
                );
            }

            $p->end_page_ext("");
            $p->end_document("");
            $buf = $p->get_buffer();
            $len = strlen($buf);

            header("Content-type: application/pdf");
            header("Content-Length: $len");
            header("Content-Disposition: attachment; filename=period.pdf");
            print $buf;
        }
        catch (PDFlibException $e) {
            die("PDFlib exception occurred in hello sample:\n" .
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
	$pdf->Cell(160, 7, 'Periods', 0, 1, 'C', false);
	$pdf->Ln();

	$pdf->SetFont('Arial', '', 12);
	$pdf->SetFillColor(245, 245, 245);
	$pdf->SetTextColor(0);
	$pdf->SetDrawColor(0, 0, 0);
	$pdf->SetLineWidth(.1);
	$pdf->SetFont('', 'B');

	$pdf->Cell(20);
	$pdf->Cell(40, 7, 'Number', 1, 0, 'C', true);
        $pdf->Cell(40, 7, 'Started', 1, 0, 'C', true);
        $pdf->Cell(40, 7, 'Ended', 1, 0, 'C', true);
	$pdf->Ln();

	$pdf->SetTextColor(0);
	$pdf->SetFont('');

        for ($i = 0; $i < count($data_array); $i++)
        {
	    $period_number = (int)$data_array[$i]['period_number'];
            $started = new DateTime($data_array[$i]['started']);
            $ended = new DateTime($data_array[$i]['ended']);

	    $pdf->Cell(20);
            $pdf->Cell(40, 6, $period_number, 1, 0, 'R', false);
	    $pdf->Cell(40, 6, $started->format('m/d/Y'), 1, 0, 'L', false);
	    $pdf->Cell(40, 6, $ended->format('m/d/Y'), 1, 0, 'L', false);
	    $pdf->Ln();
	}
	$pdf->Output();
	exit;
    }

    static function text_by_id($period_id)
    {
	$sql = "SELECT started, ended FROM period WHERE period_id = '$period_id'";

        $q = new DBQuery($sql);
        if ($row = $q->fetch())
        {
            $started = new DateTime($row['started']);
            $ended = new DateTime($row['ended']);
            return $started->format('m/d/Y').' - '.$ended->format('m/d/Y');
        }
        else
        {
            return '[unknown]';
        }
    }

    static function id_by_date($date)
    {
	$sql = "SELECT period_id FROM period WHERE started <= '".$date->format('Y-m-d')."' AND ended >= '".$date->format('Y-m-d')."'";

        $q = new DBQuery($sql);
        if ($row = $q->fetch())
        {
            return (int)$row['period_id'];
        }
        else
        {
            return 0;
        }
    }

    static function period_number_by_date($date)
    {
	$p = new BOPeriod(self::id_by_date($date));
	return $p->period_number;
    }

}

?>
