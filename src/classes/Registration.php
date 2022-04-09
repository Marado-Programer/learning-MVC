<?php

/**
 *
 */

class Registration
{
    private $event, $partner;

    public function __construct(Events $event, Partners $partner)
    {
        $this->event = $event;
        $this->partner = $partner;
    }

    public function getIdEvent()
    {
        return $this->event->id;
    }

    public function getIdPartner()
    {
        return $this->partner->id;
    }

    // listRegistration
    public function __toString()
    {
        return "The {$this->event->association->name} partner {$this->partner->name} it's registred to the event {$this->event->title}.";
    }

    public function __destruct()
    {
        // remove db info;
        // create a way to unset $this. Create a object that composes $this and use unset $this and there's no both
    }
}
