<?php

/**
 * 
 */

class User extends Updatable
{
    protected $id;

    public $username;

    protected $password;
    protected $realName;
    protected $email;
    protected $telephone;
    protected $permissions;
    protected $loggedIn;
    protected $wallet;

    public $news = [];

    public $quotas = [];

    public function __construct(
        string $username = 'Guest',
        ?string $password = null,
        ?string $realName = null,
        ?string $email = null,
        ?string $telephone = null,
        float $money = 0,
        int $permissions = PermissionsManager::P_GUEST,
        bool $loggedIn = false,
        int $id = -1
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->realName = $realName;
        $this->password = $password;
        $this->email = $email;
        $this->telephone = $telephone;
        $this->wallet += $money;
        $this->permissions = $permissions;
        $this->loggedIn = $loggedIn;
    }

    public function getID(): int
    {
        return $this->id;
    }

    public function setID(int $id)
    {
        $this->id = $id;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;

        $this->checkUpdate();
    }

    public function getRealName()
    {
        return $this->realName;
    }

    public function setRealName(string $realName)
    {
        $this->realName = $realName;

        $this->checkUpdate();
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;

        $this->checkUpdate();
    }

    public function getTelephone()
    {
        return $this->telephone;
    }

    public function setTelephone(int $prefixCode, string $number)
    {
        $this->telephone = "+$prefixCode $number";

        $this->checkUpdate();
    }

    public function getPremissions()
    {
        return $this->premissions;
    }

    public function setPremissions(int $premissions)
    {
        $this->premissions = $premissions;

        $this->checkUpdate();
    }

    public function isLoggedIn(): bool
    {
        return $this->loggedIn;
    }

    public function logIn()
    {
        $this->loggedIn = true;
    }

    public function logOut()
    {
        $this->loggedIn = false;
    }

    public function getWallet()
    {
        return $this->wallet;
    }

    public function setWallet(float $wallet)
    {
        $this->wallet = $wallet;

        $this->checkUpdate();
    }

    public function deposit(float $quantity)
    {
        $this->wallet += $quantity;

        $this->checkUpdate();
    }

    public function getNews()
    {
        return $this->news;
    }

    public function getQuotas()
    {
        return $this->quotas;
    }

    public function createAssociation(
        string $name,
        string $nickname,
        string $address,
        string $telephone,
        int $taxpayerNumber
    ) {
        try {
            $db = new DBConnection();

            $db->checkAccess();

            $db->beginTransaction();

            $createdAssoc = $db->insert('associations',
                [
                    'name' => $name,
                    'nickname' => $nickname,
                    'address' => $address,
                    'telephone' => $telephone,
                    'taxpayerNumber' => $taxpayerNumber,
                ]
            );

            $createdAssocID = $db->query(
                    $db->createQuery('SELECT `id` FROM `associations` WHERE `nickname` = ?;'),
                    [
                        $nickname,
                    ]
            );

            $assocID = $createdAssocID->fetch(PDO::FETCH_ASSOC)['id'];

            $userAssoc = $db->insert(
                'usersAssociations',
                [
                    'association' => $assocID,
                    'user' => $this->id,
                    'role' => PermissionsManager::AP_PRESIDENT
                ]
            );

            if (!$userAssoc)
                throw new Exception('Failed to create association');
        } catch (Exception $e) {
            $db->rollBack();
            die($e);
        }

        $db->commit();
    }

    protected function update()
    {
        if (isset($this->db))
            try {
                $this->db->update(
                    'users',
                    ['id' => $this->id],
                    [
                        'username' => $this->username,
                        'password' => UsersManager::getTools()->getPhPass()->HashPassword($this->password),
                        'realName' => $this->realName,
                        'email' => $this->email,
                        'telephone' => $this->telephone,
                        'permissions' => $this->permissions,
                        'wallet' => $this->wallet
                    ]
                );
            } catch (Exception $e) {
                print_r($e);
            }
    }

////////////////////////////////////////////////////////////////////////

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
            $this
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

