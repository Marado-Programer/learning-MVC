<?php

/**
 * 
 */

class User
{
    private static $userCounter = 1;
    private static $freeIDs = [];

    public $loggedIn;
    public $id;
    public $username;
    public $password;
    public $realName;
    public $email;
    public $telephone;
    public $permissions;

    public function __construct(
        string $username = 'Guest',
        ?string $password = "\0",
        ?string $realName = "\0",
        string $email = "\0",
        ?string $telephone = "\0",
        int $permissions = PermissionsManager::P_VIEW_ASSOCIATIONS,
        bool $loggedIn = false,
        int $id = null
    ) {
        $this->loggedIn = $loggedIn;
        $this->id = $id ?? ($this->loggedIn ? $this->defineID() : -1);
        $this->permissions = $permissions;
        $this->username = $username;
        $this->password = $password;
        $this->telephone = $telephone;
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
        $association->createPartner($this);
        $this->associations[] = $association;
    }

    public function createAssociation(
        ?int $id,
        string $name,
        string $nickname,
        string $address,
        string $telephone,
        int $taxpayerNumber
    ) {
        $db = new SystemDB();

        if (!$db->pdo)
            die('Connection error');

        $db->pdo->beginTransaction();

        $createdAssoc = $db->insert('associations',
            [
                'id' => $id,
                'name' => $name,
                'nickname' => $nickname,
                'address' => $address,
                'telephone' => $telephone,
                'taxpayerNumber' => $taxpayerNumber,
                'president' => $this->id
            ]
        );

        if (!$createdAssoc) {
            $db->pdo->rollBack();
            die('Failed to create association 1');
        }

        $createdAssocID = $db->query(
                'SELECT `id` FROM `associations` WHERE `nickname` = ?;',
                [
                    $nickname,
                ]
        )->fetch(PDO::FETCH_ASSOC);

        if (!$createdAssocID) {
            $db->pdo->rollBack();
            die('Failed to create association');
        }

        $userAssoc = $db->insert(
            'usersAssociations',
            [
                'associationID' => $createdAssocID['id'],
                'userID' => $this->id,
                'role' => PermissionsManager::AP_PRESIDENT
            ]
        );

        if (!$userAssoc) {
            $db->pdo->rollBack();
            die('Failed to create association');
        }

        $db->pdo->commit();

        $newAssoc = new Association(
            $id,
            $name,
            $nickname,
            $address,
            $telephone,
            $taxpayerNumber,
            $this
        );

        $this->yourAssociations[] = $newAssoc;
        $this->associations[] = $newAssoc;

        return $newAssoc;
    }

    public function initAssociation(
        int $id,
        string $name,
        string $nickname,
        string $address,
        string $telephone,
        int $taxpayerNumber
    ) {
        $newAssoc = new Association(
            $id,
            $name,
            $nickname,
            $address,
            $telephone,
            $taxpayerNumber,
            $this
        );

        return $newAssoc;
    }

    public function getPermissions()
    {
        return $this->permissions;
    }

    public function setPermissions(int $permissions = null)
    {
        if (!isset($permissions))
            return;

        $this->permissions = $permissions;
    }

    public function __clone()
    {
        $this->loggedIn = false;
        $this->password = "";
    }
}

