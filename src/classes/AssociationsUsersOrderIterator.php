<?php

/**
 *
 */

class AssociationsUsersOrderIterator implements Iterator
{
    private $list;
    private $pos;
    
    private $sawUsers;

    public function __construct($list)
    {
        $this->list = $list;
        $this->rewind();
    }

    public function current()
    {
        return $this->list->getList()[$this->pos];
    }

    public function key()
    {
        return $this->pos;
    }

    public function next(): void
    {
        do {
            $this->pos++;
            if ($this->key() >= $this->list->getListSize() && !$this->sawUsers) {
                $this->pos = 0;
                $this->sawUsers = true;
            }
        } while ($this->getCondition());
    }

    public function rewind(): void
    {
        $this->pos = 0;
        $this->sawUsers = false;
        if ($this->getCondition())
            $this->next();
    }

    public function valid(): bool
    {
        return isset($this->list->getList()[$this->key()]);
    }

    public function getCondition(): bool
    {
        return $this->sawUsers
            ? $this->current()->president->id == 1
            : $this->current()->president->id != 1;
    }
}

