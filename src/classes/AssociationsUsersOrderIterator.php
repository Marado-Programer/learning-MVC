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
        return $this->list->getList()[$this->key()];
    }

    public function key()
    {
        return $this->pos;
    }

    public function next(): void
    {
        $add = 0;
        do {
            $add++;
            if ($this->key() + $add >= $this->list->getListSize())
                if (!$this->sawUsers) {
                    $this->pos = 0;
                    $add = 0;
                    $this->sawUsers = true;
                } else
                    break;
        } while ($this->getCondition($add));
        $this->pos += $add;
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

    public function getSawUsers()
    {
        return $this->sawUsers;
    }

    public function getCondition(int $add = 0): bool
    {
        $cur = $this->list->getList()[$this->key() + $add];

        return $this->sawUsers
            ? $cur->president->id == 1
            : $cur->president->id != 1;
    }
}

