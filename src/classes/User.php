<?php

/**
 * 
 */

class User
{
    private static $userCounter = 0;
    private static $freeIDs = [];

    public $loggedIn;
    public $id;
    public $username;
    public $password;
    public $realName;
    public $email;
    public $permissions;

    public $associations = [];
    public $yourAssociations = [];

    public function __construct(
        string $username = 'Guest',
        string $password = "\0",
        string $realName = "\0",
        string $email = "\0",
        int $permissions = PermissionsManager::P_ZERO,
        bool $loggedIn = false,
        int $id = null
    ) {
        $this->loggedIn = $loggedIn;
        $this->id = $id ?? $this->loggedIn ? $this->defineID() : -1;
        $this->permissions = $permissions;
        $this->username = $username;
        $this->password = $password;
        $this->realName = $realName;
        $this->email = $email;
    }

    final protected function defineID()
    {
        if (empty(self::$freeIDs))
            $id = self::$userCounter;
        else {
            $id = self::$freeIDs[0];
            unset(self::$freeIDs[0]);
            self::$freeIDs = array_values(self::$freeIDs);
        }
        self::$userCounter++;
        return $id;
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

