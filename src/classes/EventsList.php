<?php

/**
 *
 */

class EventsList implements IteratorAggregate
{
    public static $DEFAULT_ORDER = 0;
    public static $END_FIRST_ORDER = 1;

    private $list = [];

    public function add(Events $item)
    {
        $this->list[] = $item;
    }

    public function getList()
    {
        return $this->list;
    }

    public function getListSize()
    {
        return count($this->list);
    }

    public function getIterator(): Iterator
    {
        $mode = func_num_args() > 0 ? func_get_arg(0) : self::$DEFAULT_ORDER;
        $mode = is_numeric($mode) ? $mode : self::$DEFAULT_ORDER;

        switch ($mode) {
            case self::$END_FIRST_ORDER:
                return new EventsEndFirstOrderIterator($this);
            case self::$DEFAULT_ORDER:
            default:
                return new EventsIterator($this);
        }
    }
}
