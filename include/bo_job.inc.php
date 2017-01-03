<?php

class BOJob {
    public $job_id;
    public $code;
    public $description;
    public $manager;

    function __construct($job_id = 0)
    {
        if ($job_id)
        {
            $this->get($job_id);
        }
        else
        {
            $this->job_id = NULL;
            $this->code = NULL;
            $this->description = NULL;
            $this->manager = NULL;
        }
    }

    private function get($job_id)
    {
	$this->job_id = NULL;
        $this->code = NULL;
        $this->description = NULL;
        $this->manager = NULL;

	$sql = "SELECT job_id, code, description, CAST(manager AS unsigned integer) AS manager FROM job WHERE job_id = '$job_id'";

        $q = new DBQuery($sql);
        if ($row = $q->fetch())
        {
            $this->job_id = $row['job_id'];
            $this->code = $row['code'];
            $this->description = $row['description'];
            $this->manager = ($row['manager'] != 0);
            return true;
        }
        else
        {
            return false;
        }
    }

    function insert()
    {
	return self::insert_param($this->code, $this->description, $this->manager);
    }

    static function insert_param($code, $description, $manager)
    {
	global $config;

	$sql =
            "INSERT INTO job (code, description, manager) VALUES ('".
            DBConnection::db_real_escape_string($code)."', ".
            DBConnection::db_real_escape_string($description)."', ";
            ($manager ? '1' : '0').")";

        $q = new DBQuery($sql, true);
        return !($q->error());
    }

    function update()
    {
	return self::update_param($this->job_id, $this->code, $this->description, $this->manager);
    }

    static function update_param($job_id, $code, $description, $manager)
    {
	$sql =
            "UPDATE job SET ".
            "code = '".DBConnection::db_real_escape_string($code)."', ".
            "description = '".DBConnection::db_real_escape_string($description)."', ".
            "manager = ".($manager ? '1' : '0')." ".
            "WHERE job_id = '$job_id'";

        $q = new DBQuery($sql, true);
        return !($q->error());
    }

    function delete()
    {
	return self::delete_param($this->job_id);
    }

    static function delete_param($job_id)
    {
        if (isset($job_id))
        {
	    $sql = "DELETE FROM job WHERE job_id = '$job_id'";

            new DBQuery($sql);
        }
    }

    private static function data_array(&$data_array)
    {
	$sql = "SELECT job_id, code, description, CAST(manager AS unsigned integer) AS manager FROM job ORDER BY code";

        $q = new DBQuery($sql);
        $i = 0;
        while ($row = $q->fetch())
        {
            $data_array[$i]['job_id'] = $row['job_id'];
            $data_array[$i]['code'] = $row['code'];
            $data_array[$i]['description'] = $row['description'];
            $data_array[$i]['manager'] = ($row['manager'] != 0);
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
            $job_id = $data_array[$i]['job_id'];
            $description = $data_array[$i]['code'].' - '.$data_array[$i]['description'];
            $a .= '<option value="'.htmlentities($job_id).'"'.($selected_id==$job_id ? ' selected' : '').'>'.htmlentities($description).'</option>';
        }
        return $a;
    }

    static function html_table($editable = 0)
    {
        global $web_path;

        unset($data_array);
        self::data_array($data_array);
        $a =
            '<thead><tr><th>Code</th><th>Description</th><th>Manager</th>'.
            ($editable ? '<th><div align="right"><input type="image" name="add_job" src="'.$web_path.'image/add.gif" alt="" title="Add" value="1"></div></th>' : '').
            '</tr></thead><tbody>';
        for ($i = 0; $i < count($data_array); $i++)
        {
            $job_id = $data_array[$i]['job_id'];
            $code = htmlentities($data_array[$i]['code']);
            $description = htmlentities($data_array[$i]['descrition']);
            $a .=
                '<tr'.(($i%2) ? ' class="alt_row"' : '').'>'.
                '<td>'.$code.'</td>'.
                '<td>'.$description.'</td>'.
		'<td>'.(($manager == 1) ? 'Manager' : '').'</td>'.
		($editable ?
                '<td align="right">'.
                    '<input type="image" name="edit_job" src="'.$web_path.'image/edit.gif" alt="" title="Edit" value="'.$job_id.'">'.
                    '<input type="image" name="delete_job" src="'.$web_path.'image/delete.gif" alt="" title="Delete" value="'.$job_id.'" onclick="return confirm('.
                        "'Delete job ".htmlentities($code).' - '.htmlentities($description)." ?'".
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

        xlsHeaders('Jobs');
        xlsBOF();

        xlsWriteLabel(2, 0, 'Code');
        xlsWriteLabel(2, 1, 'Description');
        xlsWriteLabel(2, 2, 'Manager');

        for ($i = 0; $i < count($data_array); $i++)
        {
            xlsWriteLabel($i + 3, 0, $data_array[$i]['code']);
            xlsWriteLabel($i + 3, 1, $data_array[$i]['description']);
            xlsWriteLabel($i + 3, 1, $data_array[$i]['manager']);
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
            $p->set_info("Title", "Jobs");

            $p->begin_page_ext(612, 792, "");

            $font = $p->load_font("Courier","iso8859-1", "");

            $p->setfont($font, 10.0);
            $p->set_text_pos(72, 792 - 72);

            $p->show("Jobs");
            $p->continue_text('');
            $p->continue_text(
		    str_pad('Code', 20, ' ').
		    str_pad('Description', 45, ' ').
		    'Manager'
	    );
            $p->continue_text('');

            for ($i = 0; $i < count($data_array); $i++)
            {
                $p->continue_text(
		    str_pad($data_array[$i]['code'], 20, ' ').
		    str_pad($data_array[$i]['description'], 45, ' ').
                    (($manager == 1) ? 'Manager' : '')
		);
	    }

            $p->end_page_ext("");
            $p->end_document("");
            $buf = $p->get_buffer();
            $len = strlen($buf);

            header("Content-type: application/pdf");
            header("Content-Length: $len");
            header("Content-Disposition: attachment; filename=jobs.pdf");
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

    static function description_by_id($job_id)
    {
	$sql = "SELECT description FROM job WHERE job_id = '$job_id'";

        $q = new DBQuery($sql);
        if ($row = $q->fetch())
        {
            return $row['description'];
        }
        else
        {
            return '';
        }
    }
}

?>