<?php

/**
 *
 */

class DBConnection implements DBMethods
{
    private DBService $connection;

    private $cache = [];
    private $error;

    public function __construct()
    {
        $this->connection = DBService::getInstance();
    }

    public function checkConnection()
    {
        return (bool) $this->connection;
    }

    public function beginTransaction()
    {
        $this->connection->beginTransaction();
    }

    public function commit()
    {
        $this->connection->commit();
    }

    public function rollBack()
    {
        $this->connection->rollBack();
    }

    public function query($statment, array $dataArray)
    {
        $query = $this->connection->pdo->prepare($statment);

        if (!$query) {
            $this->error = $query->errorInfo()[2];
            return false;
        }

        if (!isset($this->cache[$query])) {
            $result = $this->connection->query($statment, $dataArray);
            $this->cache[$query] = $result;
        }

        return $this->cache[$query];
    }

    public function insert(string $table, array ...$inserts) {
        $this->connection->insert($table, $inserts);
    }

    public function resultToCache(PDOStatement $query, $result, $force = false)
    {
        if (!isset($this->cache[$query]) || $force)
            $this->cache[$query] = $result;
    }

    public function getErrors()
    {
        return $this->error;
    }
}

