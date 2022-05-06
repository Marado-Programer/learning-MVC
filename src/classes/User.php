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

    public $news;

    public function __construct(
        ?int $id = -1,
        string $username = 'Guest',
        ?string $password = null,
        ?string $realName = null,
        ?string $email = null,
        ?string $telephone = null,
        float $money = 0,
        int $permissions = PermissionsManager::P_GUEST,
        bool $loggedIn = false
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

        $this->news = new NewsList();
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
        return $this->permissions;
    }

    public function setPremissions(int $permissions)
    {
        $this->permissions = $permissions;

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

    public function useWallet(float $money)
    {
        $this->wallet -= $money;

        $this->checkUpdate();

        return $money;
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

    public function createNews(
        Association $association,
        string $title,
        array $image,
        string $article
    ) {
        $db = new DBConnection();

        $db->beginTransaction();

        $createdNews = $db->insert(
            'news',
            [
                'association' => $association->getID(),
                'author' => $this->id,
                'title' => $title,
                'image' => $image['name'],
                'article' => $article,
                'published' => 0,
                'lastEditTime' => ($now = new Datetime())->format('Y-m-d H:i:s')
            ]
        );

        if (!$createdNews) {
            $errors[] = "Failed to create news";
            $_SESSION['news-errors'] = $errors;
            $db->pdo->rollBack();
            return;
        }

        $db->commit();

        $newNews = $db->query(
            $db->createQuery("SELECT `id` FROM `news`
            WHERE `association` = ?
            AND `author` = ?
            AND `title` = ?
            AND `image` = ?
            AND `article` = ?
            AND `published` = 0
            AND `lastEditTime` = ?;"),
            [
                $association->getID(),
                $this->id,
                $title,
                $image['name'],
                $article,
                $now->format('Y-m-d H:i:s')
            ]
        );

        if (!$newNews) {
            $_SESSION['news-errors'][] = "Failed to creating news.";
            die('Internal error');
        }

        if (!file_exists(UPLOAD_PATH))
            mkdir(UPLOAD_PATH, 0755, true);

        if (!move_uploaded_file($image['tmp_name'], UPLOAD_PATH . '/' . $image['name'])) {
            $errors[] = "Failed uploading file";
            $_SESSION['news-errors'] = $errors;
            return;
        }

        $_SESSION['news-created'] = 'A news was created.';

        unset($_SESSION['news']);

        return new News(
            $association,
            $this,
            $title,
            $image['name'],
            null,
            $now,
            $newNews->fetchAll(PDO::FETCH_ASSOC)[0]['id']
        );
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
                    'taxpayerNumber' => $taxpayerNumber
                ]);

            if (!$createdAssoc)
                throw new Exception('Failed to create association');

            $createdAssocID = $db->query(
                $db->createQuery('SELECT `id` FROM `associations` WHERE `nickname` = ?;'),
                [$nickname]
            );

            $assocID = $createdAssocID->fetch(PDO::FETCH_ASSOC)['id'];

            $userAssoc = $db->insert(
                'usersAssociations',
                [
                    'association' => $assocID,
                    'user' => $this->id,
                    'role' => dechex(PermissionsManager::AP_PRESIDENT)
                ]
            );

            if (!$userAssoc)
                throw new Exception('Failed to create association');
        } catch (Exception $e) {
            $db->rollBack();
            die($e);
        } finally {
            $db->commit();
        }
    }

    public function getMyNews() {
        try {
            $db = new DBConnection();

            $db->checkAccess();

            $query = $db->createQuery(
                'SELECT * FROM `news`
                WHERE `author` = ?'
            );
            $data = [$this->id];
            $news = $db->query($query, $data)->fetchAll(PDO::FETCH_ASSOC);

            foreach ($news as $aNews)
                $this->news->add(Instanceator::getInstanceator($db)->instanceNewsByID($aNews['id']));
        } catch (Exception $e) {
            die($e);
        }
    }

    public function enterAssociation(Association $association) {
        $association->newPartner($this);
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
                        'permissions' => dechex($this->permissions),
                        'wallet' => $this->wallet
                    ]
                );
            } catch (Exception $e) {
                die($e);
            }
    }

////////////////////////////////////////////////////////////////////////

    public function receiveDue(Association $association, float $price, DateTime $endDate, ?DateTime $startDate = null)
    {
        if (!isset($startDate))
            $startDate = $endDate;

        $db = new DBConnection();

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

        return $newAssoc;
    }

    public function addDue(Quota $due)
    {
        $this->userDues[] = $due;
    }

    public function __clone()
    {
        $this->password = "\0";
        $this->news = null;
        $this->quotas = null;
    }
}

