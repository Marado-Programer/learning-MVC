<?php

/**
 * 
 */

class User
{
    public $loggedIn;
    public $id;
    public $username;
    public $password;
    public $realName;
    public $email;
    public $permissions;

    public $associations = [];
    public $yourAssociations = [];

    public function __construct()
    {
        $this->loggedIn = false;
        $this->permissions = PermissionsManager::P_ZERO;
    }

    public function enterAssociation(Association &$association)
    {
        if (in_array($association, $this->yourAssociations)) {
            echo "error: you are the president of this association, already in it";

            return;
        }

        if (in_array($association, $this->associations)) {
            echo "error: already in this association";

            return;
        }

        $association->addPartner($this);
        $this->associations[] = $association;
    }

    public function createAssociation(
        string $name,
        string $address,
        int $telephoneInternationalCodePrefix,
        int $telephoneNumber,
        int $taxpayerNumber
    ) {
        $this->yourAssociations[] = new Association(
            $name,
            $address,
            $telephoneInternationalCodePrefix,
            $telephoneNumber,
            $taxpayerNumber,
            $this
        );
        $this->associations[] = $this->yourAssociations[count($this->yourAssociations)];
    }
}

