<?php

/**
 *
 */

class AssociationsList implements IteratorAggregate
{
    private $list = [];

    public function add(Association $item)
    {
        $this->list[] = $item;
    }

    public function getList()
    {
        return $this->list;
    }

    public function getIterator(): Iterator
    {
        $mode = 1;

        switch ($mode) {
            case 1:
                return new AssociationsAlphaOrderIterator($this);
                break;
        }
    }
}
