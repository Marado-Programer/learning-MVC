<?php

/**
 *  Singleton for DB connection
 */

class SystemDB
{
    private static $instance;

    public $host = 'localhost',
        $db_name = '',
        $user = 'root',
        $password = '',
        $charset = 'uft8',
        $pdo = null,
        $error = null,
        $debug = false,
        $last_id = null;

    public function __construct(
        $host = null,
        $db_name = null,
        $user = null,
        $password = null,
        $charset = null,
        $debug = null
    )
    {
        $this->host = defined('DB_HOSTNAME')
            ? DB_HOSTNAME
            : host;
        $this->db_name = defined('DB_NAME')
            ? DB_NAME
            : db_name;
        $this->user = defined('DB_USERNAME')
            ? DB_USERNAME
            : user;
        $this->password = defined('DB_USER_PASSWORD')
            ? DB_USER_PASSWORD
            : password;
        $this->charset = defined('DB_CHARSET')
            ? DB_CHARSET
            : charset;
        $this->debug = defined('DEBUG')
            ? DEBUG
            : debug;
        $this->connect();
    }

    final protected function connect()
    {
        $pdo_details = "mysql:host={$this->host};"
            . "dbname={$this->db_name};"
            . "charset={$this->charset};";
        try {
            $this->pdo = self::getInstance(
                $pdo_details,
                $this->user,
                $this->password
            );
            unset($this->host);
            unset($this->db_name);
            unset($this->password);
            unset($this->user);
            unset($this->charset);
        } catch (PDOException $e) {
            if ($this->debug === true)
                echo "Error: {$e->getMessage()}";
            else
                die();
        }
    }

    public static function getInstance($pdo_details, $user, $password)
    {
        if (!isset(self::$instance))
            self::$instance = new PDO($pdo_details, $user, $password);
        return self::$instance;
    }

    public function query($stmt, $data_array = null)
    {
        $query = $this->pdo->prepare($stmt);
        if (!$query) {
            $this->error = $query->errorInfo()[2];
            return false;
        }
        $check_exec = $query->execute($query);
        if ($check_exec)
            return $check_exec;
    }

    public function insert($table)
    {
        $cols = array();
        $place_holders = '(';
        $values = array();
        $data = func_get_args();
        if (!isset($data[1]) || !is_array($data[1]))
            return;
        $j = 1;
        for ($i = 1; $i < count($data); $i++)
            foreach ($data[$i] as $col => $val) {
                if ($i ===  1)
                    $cols[] = "`$col`";
                if ($j != $i)
                    $place_holders .= '), (';
                $place_holders .= '?, ';
                $values[] = $val;
            }
        $cols = implode(', ', $cols);
        $stmt = "INSERT INOT `$table` ($cols) VALUES $place_holders)";
        $insert = $this->query($stmt, $values);
        if ($insert)
            if (method_exists(
                $this->pdo, 'lastInsertId'
                && $this->pdo->lastInsertId()
            )
            $this->last_id = $this->pdo->lastInsertId();
        return;
    }
}

