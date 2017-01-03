<?php

class DBConnection
{
    private static $_instance;
    private static $_connection;

    static function connect()
    {
        global $config;

        if (self::$_instance === NULL)
        {
            self::$_instance = new self();
        }

        self::$_connection = mysql_connect(
                $config['db_host'],
                $config['db_user'],
                $config['db_password']
        ) or die('Could not connect: ' . mysql_error());
        mysql_select_db($config['db_name']) or die('Could not select database');
    }

    final protected function __construct() { }
    final protected function __clone() { }

    function __destruct()
    {
        mysql_close(self::$_connection);
    }

    static function db_real_escape_string($string)
    {
        self::connect();
        return mysql_real_escape_string($string);
    }
}

class DBQuery
{
    private $qr;
    private $error;

    function __construct($query, $silent = false)
    {
        DBConnection::connect();
        $this->error = false;
        $this->qr = mysql_query($query) or $this->failed();
        if ($this->error and (!$silent))
        {
            die('Query failed: ' . mysql_error()."\n".$query);
        }
        return $this->qr;
    }

    private function failed()
    {
        $this->error = true;
    }

    function error()
    {
        return $this->error;
    }

    function fetch()
    {
        return mysql_fetch_array($this->qr);
    }
}

?>