<?php

/**
 *
 */

class President extends Partner
{
    public function listAssociations()
    {
        $list = "{$this->username}'s associations list:\n";
        foreach ($this->associations as $association)
            $list .= $association;
        return $list;
    }

    public function exitAssociation($i)
    {
        unset($this->associations[$i]);
    }

    public function enterEvent(int $association, int $event)
    {
        $this->associations[$association]->registPartner($this, $event);
    }
}

