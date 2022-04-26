<?php

/**
 *
 */

abstract class Updatable
{
    protected bool $updated = false;
    protected ?DBConnection $db = null;

    final protected function checkUpdate()
    {
        $this->updated = true;

        if (!isset($this->db))
            $this->db = new DBConnection();
    }

    protected abstract function update();

    final public function __destruct()
    {
        $this->update();
    }
}

