<?php

/**
 *
 */

interface DBMethods
{
    public function beginTransaction();
    public function commit();
    public function rollBack();

    public function query(PDOStatement $statment, array $dataArray);
}

