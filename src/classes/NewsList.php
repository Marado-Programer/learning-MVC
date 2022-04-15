<?php

/**
 *
 */

class NewsList implements IteratorAggregate
{
    public static $DEFAULT_ORDER = 0;
    public static $NEWER_ORDER = 1;

    private $list = [];

    public function add(News $item)
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
            case self::$NEWER_ORDER:
                return new NewsNewerOrderIterator($this);
            case self::$DEFAULT_ORDER:
            default:
                return new NewsIterator($this);
        }
    }
}
