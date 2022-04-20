<?php

/**
 *
 */

interface DBMethods
{
    public function beginTransaction();
    public function commit();
    public function rollBack();

    public function query($statment, array $dataArray);
    public function insert(string $table, array ...$inserts);
}

