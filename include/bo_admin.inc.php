<?php

class BOAdmin {
    public $admin_id;
    public $code;
    public $name;

    function __construct($admin_id = 0)
    {
        if ($admin_id)
        {
            $this->get($admin_id);
        }
        else
        {
            $this->admin_id = NULL;
            $this->code = NULL;
            $this->name = NULL;
        }
    }

    private function get($admin_id)
    {
        $this->admin_id = NULL;
        $this->code = NULL;
        $this->name = NULL;

		$sql = "
			SELECT
				admin_id,
				code,
				name
			FROM
				admin
			WHERE
				admin_id = '$admin_id'
			";

        $q = new DBQuery($sql);
        if ($row = $q->fetch())
        {
            $this->admin_id = $row['admin_id'];
            $this->code = $row['code'];
            $this->name = $row['name'];
            return true;
        }
        else
        {
            return false;
        }
    }

    function insert()
    {
		return self::insert_param($this->code, $this->name);
    }

    static function insert_param($code, $name)
    {
		global $config;

		$sql = "
			INSERT INTO admin (
				code, name, pwd_md5
			) VALUES (
				'".DBConnection::db_real_escape_string($code)."',
				'".DBConnection::db_real_escape_string($name)."',
				'".DBConnection::db_real_escape_string(md5($config['default_pwd']))."'
			)
		";

        $q = new DBQuery($sql, true);
        return !($q->error());
    }

    function update()
    {
	return self::update_param($this->admin_id, $this->code, $this->name);
    }

    static function update_param($admin_id, $code, $name)
    {
		$sql = "
			UPDATE admin SET
				code = '".DBConnection::db_real_escape_string($code)."',
				name = '".DBConnection::db_real_escape_string($name)."'
			WHERE
				admin_id = '$admin_id'
		";

        $q = new DBQuery($sql, true);
        return !($q->error());
    }

    function delete()
    {
		return self::delete_param($this->admin_id);
    }

    static function delete_param($admin_id)
    {
        if (isset($admin_id))
        {
			$sql = "
				DELETE FROM admin
				WHERE
					admin_id = '$admin_id'
			";

            new DBQuery($sql);
        }
    }

    private static function data_array(&$data_array)
    {
		$sql = "
			SELECT
				admin_id,
				code,
				name
			FROM
				admin
			ORDER BY
				code
		";

        $q = new DBQuery($sql);
        $i = 0;
        while ($row = $q->fetch())
        {
            $data_array[$i]['admin_id'] = $row['admin_id'];
            $data_array[$i]['code'] = $row['code'];
            $data_array[$i]['name'] = $row['name'];
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
            $admin_id = $data_array[$i]['admin_id'];
            $code = $data_array[$i]['code'];
            $a .= '<option value="'.htmlentities($admin_id).'"'.($selected_id==$admin_id ? ' selected' : '').'>'.htmlentities($code).'</option>';
        }
        return $a;
    }

    static function html_table($editable = 0)
    {
        global $web_path;

        unset($data_array);
        self::data_array($data_array);
        $a =
            '<thead><tr><th>Code</th><th>Name</th>'.
            ($editable ? '<th><div align="right"><input type="image" name="add_admin" src="'.$web_path.'image/add.gif" alt="" title="Add" value="1"></div></th>' : '').
            '</tr></thead><tbody>';
        for ($i = 0; $i < count($data_array); $i++)
        {
            $store_id = $data_array[$i]['admin_id'];
            $code = htmlentities($data_array[$i]['code']);
            $name = htmlentities($data_array[$i]['name']);
            $a .=
                '<tr'.(($i%2) ? ' class="alt_row"' : '').'>'.
                '<td>'.$code.'</td>'.
                '<td>'.$name.'</td>'.
		($editable ?
                '<td align="right">'.
                    '<input type="image" name="edit_admin" src="'.$web_path.'image/edit.gif" alt="" title="Edit" value="'.$admin_id.'">'.
                    '<input type="image" name="delete_admin" src="'.$web_path.'image/delete.gif" alt="" title="Delete" value="'.$admin_id.'" onclick="return confirm('.
                        "'Delete admin ".htmlentities($code)." ?'".
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

        xlsHeaders('Admins');
        xlsBOF();

        xlsWriteLabel(2, 0, 'Code');
        xlsWriteLabel(2, 1, 'Name');

        for ($i = 0; $i < count($data_array); $i++)
        {
            xlsWriteLabel($i + 3, 0, $data_array[$i]['code']);
            xlsWriteLabel($i + 3, 1, $data_array[$i]['name']);
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
            $p->set_info("Title", "Admins");

            $p->begin_page_ext(612, 792, "");

            $font = $p->load_font("Courier","iso8859-1", "");

            $p->setfont($font, 10.0);
            $p->set_text_pos(72, 792 - 72);

            $p->show("Admins");
            $p->continue_text('');
            $p->continue_text('Code                Name');
            $p->continue_text('');

            for ($i = 0; $i < count($data_array); $i++)
            {
                $p->continue_text(
		    str_pad($data_array[$i]['code'], 20, ' ').
                    $data_array[$i]['name']
		);
	    }

            $p->end_page_ext("");
            $p->end_document("");
            $buf = $p->get_buffer();
            $len = strlen($buf);

            header("Content-type: application/pdf");
            header("Content-Length: $len");
            header("Content-Disposition: attachment; filename=admins.pdf");
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

    static function name_by_id($admin_id)
    {
		$sql = "
			SELECT name FROM admin WHERE admin_id = '$admin_id'
		";

        $q = new DBQuery($sql);
        if ($row = $q->fetch())
        {
            return $row['name'];
        }
        else
        {
            return '';
        }
    }
}

?>