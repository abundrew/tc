<?php

class BOIPAddress
{

    static function unauthorize_ip_address($ip_address)
    {
		$sql = "
			DELETE FROM admin_ip_address
			WHERE ip_address = '".DBConnection::db_real_escape_string($ip_address)."'
		";

        $q = new DBQuery($sql, true);
        $e = $q->error();

		$sql = "
			DELETE FROM store_ip_address
			WHERE ip_address = '".DBConnection::db_real_escape_string($ip_address)."'
		";

        $q = new DBQuery($sql, true);
        return !($q->error()) && !e;
    }

    static function authorize_ip_address_as_store($ip_address, $store_id)
    {
		self::unauthorize_ip_address($ip_address);

		$sql = "
			INSERT INTO store_ip_address (
				ip_address, store_store_id
			) VALUES (
				'".DBConnection::db_real_escape_string($ip_address)."', '$store_id'
			)
		";

		//echo $sql;
		//exit;

        $q = new DBQuery($sql, true);
        return !($q->error());
    }

    static function authorize_ip_address_as_admin($ip_address, $admin_id)
    {
		self::unauthorize_ip_address($ip_address);

		$sql = "
			INSERT INTO admin_ip_address (
				ip_address, admin_admin_id
			) VALUES (
				'".DBConnection::db_real_escape_string($ip_address)."', '$admin_id'
			)
		";

		//echo $sql;
		//exit;

        $q = new DBQuery($sql, true);
        return !($q->error());
    }

    private static function data_array(&$data_array, $store_id = NULL)
    {
		$sql = "
			SELECT
				a.store_ip_address_id,
				a.ip_address,
				a.store_store_id,
				s.code AS store_code
			FROM
				store_ip_address a
	            JOIN store s ON (s.store_id = a.store_store_id)
	            ".(isset($store_id) ? ("WHERE a.store_store_id = '".$store_id."' ") : "")."
			ORDER BY
				s.code,
				a.ip_address
		";

        $q = new DBQuery($sql);
        $i = 0;
        while ($row = $q->fetch())
        {
            $data_array[$i]['store_ip_address_id'] = $row['store_ip_address_id'];
            $data_array[$i]['ip_address'] = $row['ip_address'];
            $data_array[$i]['store_store_id'] = $row['store_store_id'];
            $data_array[$i]['store_code'] = $row['store_code'];
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
            $store_ip_address_id = $data_array[$i]['store_ip_address_id'];
            $ip_address = $data_array[$i]['ip_address'];
            $a .= '<option value="'.htmlentities($store_ip_address_id).'"'.($selected_id==$store_ip_address_id ? ' selected' : '').'>'.htmlentities($ip_address).'</option>';
        }
        return $a;
    }

    static function html_table($store_id = NULL, $editable = 0)
    {
        global $web_path;

        unset($data_array);
        self::data_array($data_array, $store_id);
        $a =
            '<thead><tr><th>IP Address</th>'.
            ($editable ? '<th><div align="right"><input type="image" name="add_ip_address" src="'.$web_path.'image/add.gif" alt="" title="Authorize New IP Address"></div></th>' : '').
            '</tr></thead><tbody>';
        for ($i = 0; $i < count($data_array); $i++)
        {
            $store_ip_address_id = $data_array[$i]['store_ip_address_id'];
            $ip_address = htmlentities($data_array[$i]['ip_address']);
            $a .=
                '<tr'.(($i%2) ? ' class="alt_row"' : '').'>'.
                '<td>'.$ip_address.'</td>'.
		($editable ?
                '<td align="right">'.
                    '<input type="image" name="delete_ip_address" src="'.$web_path.'image/delete.gif" alt="" title="Unauthorize" onclick="document.form.row_id.value = '."'".htmlentities($ip_address)."'".'; return confirm('.
                        "'Unauthorize IP Address ".htmlentities($ip_address)." ?'".
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

        xlsHeaders('IP Addresses');
        xlsBOF();

        if (isset($store_id))
        {
            xlsWriteLabel(0, 0, 'Store: '.BOStore::code_by_id($store_id));
        }

        if (!isset($store_id))
        {
            xlsWriteLabel(2, 0, 'Store');
            xlsWriteLabel(2, 1, 'IP Address');
        }
		else
		{
            xlsWriteLabel(2, 0, 'IP Address');
		}

        for ($i = 0; $i < count($data_array); $i++)
        {
            if (!isset($store_id))
	    {
	        xlsWriteLabel($i + 3, 0, $data_array[$i]['store_code']);
			xlsWriteLabel($i + 3, 1, $data_array[$i]['ip_address']);
	    }
	    else
	    {
			xlsWriteLabel($i + 3, 0, $data_array[$i]['ip_address']);
	    }
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
            $p->set_info("Title", "IP Addresses");

            $p->begin_page_ext(612, 792, "");

            $font = $p->load_font("Courier","iso8859-1", "");

            $p->setfont($font, 10.0);
            $p->set_text_pos(72, 792 - 72);

            $p->show("IP Addresses");
            $p->continue_text('');
            if (!isset($store_id))
	    {
		$p->continue_text(str_pad('Store', 20, ' ').'IP Address');
	    }
	    else
	    {
		$p->continue_text('IP Address');
	    }
            $p->continue_text('');

            for ($i = 0; $i < count($data_array); $i++)
            {
                if (!isset($store_id))
		{
                    $p->continue_text(str_pad($data_array[$i]['store_code'], 20, ' ').$data_array[$i]['ip_address']);
		}
		else
		{
                    $p->continue_text($data_array[$i]['ip_address']);
		}
            }

            $p->end_page_ext("");
            $p->end_document("");
            $buf = $p->get_buffer();
            $len = strlen($buf);

            header("Content-type: application/pdf");
            header("Content-Length: $len");
            header("Content-Disposition: attachment; filename=ip_addresses.pdf");
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
		$pdf->Cell(160, 7, 'IP Addresses', 0, 1, 'C', false);
		$pdf->Ln();

		$pdf->SetFont('Arial', '', 12);
		$pdf->SetFillColor(245, 245, 245);
		$pdf->SetTextColor(0);
		$pdf->SetDrawColor(0, 0, 0);
		$pdf->SetLineWidth(.1);
		$pdf->SetFont('', 'B');

		$pdf->Cell(20);
		$pdf->Cell(60, 7, 'Store Number', 1, 0, 'C', true);
		$pdf->Cell(60, 7, 'IP Address', 1, 0, 'C', true);
		$pdf->Ln();

		$pdf->SetTextColor(0);
		$pdf->SetFont('');

		for ($i = 0; $i < count($data_array); $i++)
		{
			$pdf->Cell(20);
			$pdf->Cell(60, 6, $data_array[$i]['store_code'], 1, 0, 'L', false);
			$pdf->Cell(60, 6, $data_array[$i]['ip_address'], 1, 0, 'L', false);
			$pdf->Ln();
		}
		$pdf->Output();
		exit;
    }

}

?>
