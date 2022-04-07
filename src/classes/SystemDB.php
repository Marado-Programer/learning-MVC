<?php

/**
 *  Singleton for DB connection
 */

class SystemDB
{
    private static $instance;

    public $host = 'localhost',
        $dbName = '',
        $user = 'root',
        $password = '',
        $charset = 'uft8mb4',
        $pdo = null,
        $error = null,
        $debug = false,
        $lastId = null;

    public function __construct(
        $host = null,
        $dbName = null,
        $user = null,
        $password = null,
        $charset = null,
        $debug = null
    ) {
        $this->host = defined('DB_HOSTNAME')
            ? DB_HOSTNAME
            : $host;
        $this->db_name = defined('DB_NAME')
            ? DB_NAME
            : $dbName;
        $this->user = defined('DB_USERNAME') 
            ? DB_USERNAME
            : $user;
        $this->password = defined('DB_USER_PASSWORD')
            ? DB_USER_PASSWORD
            : $password;
        $this->charset = defined('DB_CHARSET')
            ? DB_CHARSET
            : $charset;
        $this->debug = defined('DEBUG')
            ? DEBUG
            : $debug;
        $this->connect();
    }

    final protected function connect()
    {
        $pdoDetails = "mysql:host={$this->host};"
            . "dbname={$this->db_name};"
            . "charset={$this->charset};";

        try {
            $this->pdo = self::getInstance(
                $pdoDetails,
                $this->user,
                $this->password
            );

            unset($this->host);
            unset($this->dbName);
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

    public static function getInstance($pdoInfo, $user, $password)
    {
        if (!isset(self::$instance))
            self::$instance = new PDO($pdoInfo, $user, $password);

        return self::$instance;
    }

    public function query($stmt, $dataArray = null)
    {
        $query = $this->pdo->prepare($stmt);

        if (!$query) {
            $this->error = $query->errorInfo()[2];
            return false;
        }

        $checkExec = $query->execute($dataArray);
        if ($checkExec)
            return $query;
    }

    public function insert($table)
    {
        
        $cols = array();
        $placeHolders = '(';
        $values = array();
        $data = func_get_args();

        foreach ($data as $i => $arr) {
            if ($i = 0)
                continue;
            if (!isset($arr) || !is_array($arr))
                return;
        }

        for ($i = 1; $i < count($data); $i++)
            foreach ($data[$i] as $col => $val) {
                if ($i ===  1)
                    $cols[] = "`$col`";

                if ($i != 1)
                    $placeHolders .= '), (';

                $placeHolders .= '?, ';

                $values[] = $val;
            }

        $placeHolders = substr($placeHolders, 0, strlen($placeHolders) - 2);

        $cols = implode(', ', $cols);

        $stmt = "INSERT INTO $table($cols) VALUES $placeHolders)";
        $insert = $this->query($stmt, $values);

        if ($insert) {
            if (method_exists($this->pdo, 'lastInsertId')
                && $this->pdo->lastInsertId())
                $this->lastId = $this->pdo->lastInsertId();
            return $insert;
        }

        return;
    }

    public function update($table, $whereField, $whereFieldVal, $values)
    {
        if (empty($table) || empty($whereField) || empty($whereFieldVal))
            return;

        $stmt = "UPDATE `$table` SET";
        $set = array();
        $where = " WHERE `$whereField` = ? ";

        if (!is_array($values))
            return;

        foreach ($values as $column => $value)
            $set[] = "`$column` = ?";

        $set = implode(', ', $set);

        $stmt .= $set . $where;

        $values[] = $whereFieldVal;
        $values = array_values($values);

        $update = $this->query($stmt, $values);

        if ($update)
            return $update;

        return;
    }

    public function delete($table, $whereField, $whereFieldValue)
    {
        if (empty($table) || empty($whereField) || empty($whereFieldVal))
            return;

        $stmt = "DELETE FROM `$table`";
        $where = " WHERE `$whereField` = ? ";
        $stmt .= $where;

        $values = array($whereFieldValue);

        $delete = $this->query($stmt, $values);

        if ($delete)
            return $delete;

        return;
    }
}

