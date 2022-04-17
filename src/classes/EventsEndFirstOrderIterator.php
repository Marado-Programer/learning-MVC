<?php

/**
 *
 */

class EventsEndFirstOrderIterator implements Iterator
{
    private $list;
    private $pos;

    private $order;
    
    public function __construct($list)
    {
        $this->list = $list;
        $this->order = [];
        $this->rewind();
    }

    public function current()
    {
        return $this->list->getList()[$this->key()];
    }

    public function key()
    {
        return $this->order[$this->pos];
    }

    public function next(): void
    {
        $this->pos++;
    }

    public function rewind(): void
    {
        $this->pos = 0;
        foreach ($this->list->getList() as $i => $event)
            if (empty($this->order))
                $this->order[] = $i;
            else
                for ($j = 0; $j < count($this->order); $j++) {
                    if ($this->list->getList()[$this->order[$j]]->endDate > $event->endDate) {
                        $head = array_slice($this->order, 0, $j);
                        $tail = array_slice($this->order, $j);
                        $this->order = array_merge($head, [$i], $tail);
                        break;
                    }
                    if ($j == (count($this->order) - 1)) {
                        $this->order[] = $i;
                        break;
                    }
                }
    }

    public function valid(): bool
    {
        return isset($this->order[$this->pos]) && null !== $this->current();
    }
}

