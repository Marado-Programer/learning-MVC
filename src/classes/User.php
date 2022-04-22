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
    public $telephone;
    public $permissions;

    public $userDues = [];

    public function __construct(
        string $username = 'Guest',
        ?string $password = "\0",
        ?string $realName = "\0",
        string $email = "\0",
        ?string $telephone = "\0",
        int $permissions = PermissionsManager::P_VIEW_ASSOCIATIONS,
        bool $loggedIn = false,
        int $id = -1
    ) {
        $this->loggedIn = $loggedIn;
        $this->id = $id;
        $this->permissions = $permissions;
        $this->username = $username;
        $this->password = $password;
        $this->telephone = $telephone;
        $this->realName = $realName;
        $this->email = $email;
        if (isset($this->id))
            $this->getDues();
    }

    public function getDues()
    {
        $db = new SystemDB();

        if (!$db->pdo)
            die('Connection error');

        $userDues = $db->query('SELECT * FROM `dues` WHERE `partner` = ' . $this->id . ';');

        if (!$userDues)
            return;
        
        foreach ($userDues->fetchAll(PDO::FETCH_ASSOC) as $due)
            $this->userDues[] = new Dues(
                clone $this,
                $due['association'],
                $due['price'],
                DateTime::createFromFormat('Y-m-d H:i:s', $due['endDate']),
                DateTime::createFromFormat('Y-m-d H:i:s', $due['startDate']),
            );
    }

    public function receiveDue(Association $association, float $price, DateTime $endDate, ?DateTime $startDate = null)
    {
        if (!isset($startDate))
            $startDate = $endDate;

        $db = new SystemDB();

        if (!$db->pdo)
            die('Connection error');

        $db->pdo->beginTransaction();

        $createdDue = $db->insert(
            'dues',
            [
                'association' => $association->id,
                'partner' => $this->id,
                'price' => $price,
                'startDate' => $startDate->format('Y-m-d H-i-s'),
                'endDate' => $endDate->format('Y-m-d H-i-s')
            ]
        );

        if (!$createdDue) {
            $db->pdo->rollBack();
            die('Could not create due.');
        }

        $db->pdo->commit();
    }

    public function enterAssociation(Association &$association)
    {
        $association->createPartner($this);
        $this->associations[] = $association;
    }

    public function createAssociation(
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
                'name' => $name,
                'nickname' => $nickname,
                'address' => $address,
                'telephone' => $telephone,
                'taxpayerNumber' => $taxpayerNumber,
            ]
        );

        if (!$createdAssoc) {
            $db->pdo->rollBack();
            die('Failed to create association');
        }

        $createdAssocID = $db->query(
                'SELECT `id` FROM `associations` WHERE `nickname` = ?;',
                [
                    $nickname,
                ]
        );

        if (!$createdAssocID) {
            $db->pdo->rollBack();
            die('Failed to create association');
        }
        
        $assocID = $createdAssocID->fetch(PDO::FETCH_ASSOC)['id'];

        $userAssoc = $db->insert(
            'usersAssociations',
            [
                'association' => $assocID,
                'user' => $this->id,
                'role' => PermissionsManager::AP_PRESIDENT
            ]
        );

        if (!$userAssoc) {
            $db->pdo->rollBack();
            die('Failed to create association');
        }

        $db->pdo->commit();

        $newAssoc = new Association(
            $assocID,
            $name,
            $nickname,
            $address,
            $telephone,
            $taxpayerNumber,
        );

        $newAssoc->createPartner($this);

        return $newAssoc;
    }

    public function initAssociation(
        ?int $id,
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
        );

        $newAssoc->initPartner($this);

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

    public function addDue(Dues $due)
    {
        $this->userDues[] = $due;
    }

    public function __clone()
    {
        $this->loggedIn = false;
        $this->password = "";
        $this->userDues = [];
    }
}

