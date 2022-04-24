<?php

/**
 *  Singleton for DB connection
 */

class DBService implements DBMethods
{
    private static $instance;

    private $pdo;

    private PDOConfig $config;

    private function __construct()
    {
        $this->config = $this->createConfig();
        $this->useConfig();
    }

    public static function getInstance()
    {
        if (!isset(self::$instance))
            self::$instance = new DBService();

        return self::$instance;
    }

    public function createConfig(
        string $host = 'localhost',
        string $dbName = '',
        string $user = 'root',
        string $password = '',
        string $charset = 'utf8mb4',
        bool $debug = true
    ): PDOConfig {
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

        $conf = $this->connect();

        unset(
            $this->host,
            $this->dbName,
            $this->password,
            $this->user,
            $this->charset
        );

        return $conf;
    }

    private function connect()
    {
        $dsn = "mysql:host={$this->host};"
            . "dbname={$this->db_name};"
            . "charset={$this->charset};";

        return new PDOConfig(
            $dsn,
            $this->user,
            $this->password
        );
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setConfig(PDOConfig $config, bool $use = false)
    {
        $this->config = $config;

        if ($use)
            $this->useConfig($this->config);
    }

    public function useConfig(PDOConfig $config = null)
    {
        if (!isset($config))
            $config = $this->config;

        $dsn = $config->getDsn();
        $username = $config->getUsername();
        $password = $config->getPassword();

        try {
            $this->pdo = new PDO($dsn, $username, $password);
        } catch (PDOException $e) {
            if ($this->debug === true)
                return "Error: {$e->getMessage()}";
            die();
        }
    }

    public function beginTransaction()
    {
        $this->pdo->beginTransaction();
    }

    public function commit()
    {
        $this->pdo->commit();
    }

    public function rollBack()
    {
        $this->pdo->rollBack();
    }

    public function query(PDOStatement $statment, array $dataArray = [])
    {
        $checkExec = $statment->execute($dataArray);

        if (!$checkExec)
            throw new Exception('error.');

        return $statment;
    }

    public function getPDO()
    {
        return $this->pdo;
    }
}
