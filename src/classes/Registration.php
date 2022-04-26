<?php

/**
 *
 */

class Registration
{
    private $event, $partner;

    public function __construct(Events $event, Partner $partner)
    {
        $this->event = $event;
        $this->partner = $partner;
    }

    public function getEvent()
    {
        return $this->event;
    }

    public function getIdEvent()
    {
        return $this->event->id;
    }

    public function getIdPartner()
    {
        return $this->partner->getID();
    }

    public function __toString()
    {
        return "The {$this->event->association->name} partner {$this->partner->name} it's registred to the event {$this->event->title}.";
    }
}
