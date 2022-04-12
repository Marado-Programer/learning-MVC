<?php

/**
 *
 */

class AssociationsIterator implements Iterator
{
    private $list;
    private $pos;

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
        $this->pos++;
    }

    public function rewind(): void
    {
        $this->pos = 0;
    }

    public function valid(): bool
    {
        return isset($this->list->getList()[$this->key()]);
    }

}

