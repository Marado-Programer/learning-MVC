<?php

/**
 *
 */

class DBConnection implements DBMethods
{
    private DBService $connection;

    private $cache = [];
    private $error;

    public function __construct(DBService $service)
    {
        $this->connection = $service;
    }

    public function query($statment, $dataArray = null)
    {
        $query = $this->connection->pdo->prepare($statment);

        if (!$query) {
            $this->error = $query->errorInfo()[2];
            return false;
        }

        if (!isset($this->cache[$query])) {
            $result = $this->connection->query($query, $dataArray);
            $this->cache[$query] = $result;
        }

        return $this->cache[$query];
    }

    public function getErrors()
    {
        return $this->error;
    }
}

