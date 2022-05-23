<?php

/**
 *
 */

class Association
{
    private $id;
    public $name, $nickname, $address, $telephone, $taxpayerNumber;

    public $president;

    private $partners = [];

    private $quotaPrice;
    private $timeSpanToPayQuota;
    private $payQuotaAtEntering;

    private $wallet;

    public function __construct(
        ?int $id,
        string $name,
        string $nickname,
        string $address,
        string $telephone,
        int $taxpayerNumber,
        President $president,
        float $quotaPrice = 5,
        string $timeSpanToPayQuota = 'P1M',
        bool $payQuotaAtEntering = true
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->nickname = $nickname;
        $this->address = $address;
        $this->telephone = $telephone;
        $this->taxpayerNumber = $taxpayerNumber;
        
        $this->president = $president;

        $this->quotaPrice = $quotaPrice;
        $this->timeSpanToPayQuota = new DateInterval($timeSpanToPayQuota);
        $this->payQuotaAtEntering = $payQuotaAtEntering;

        $this->initPartners();
    }

    public function getID()
    {
        return $this->id;
    }

    public function setID(int $id)
    {
        $this->id = $id;
    }

    public function newPartner(User $user)
    {
        try {
            $db = new DBConnection();

            $db->checkAccess();

            $db->beginTransaction();

            $userAssoc = $db->insert(
                'usersAssociations',
                [
                    'association' => $this->id,
                    'user' => $user->getID(),
                    'role' => PermissionsManager::AP_PARTNER
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

        $this->partners[] = $user;
    }

    public function initPartners()
    {
        try {
            $db = new DBConnection();
    
            $db->checkAccess();
    
            $query = $db->createQuery('SELECT * FROM `usersAssociations` WHERE `association` = ?;');
    
            $partners = $db->query($query, [$this->id])->fetchAll(PDO::FETCH_ASSOC);
    
            if (!$partners)
                throw new Exception('Failed to load partners');

            foreach ($partners as $partner) {
                if ($partner['user'] == $this->president->getID())
                    continue;

                $instanceator = Instanceator::getInstanceator($db);

                $this->partners[] = $instanceator->instanceUserByID($partner['user']);
            }
        } catch (Exception $e) {
            die($e);
        }
    
        $this->updateQuotas();
    }

    public function getPartners()
    {
        return array_merge($this->partners, [$this->president]);
    }

    public function updateQuotas()
    {
        foreach ($this->getPartners() as $partner) {
            try {
                $db = new DBConnection();
    
                $db->checkAccess();

                $query = $db->createQuery('SELECT * FROM `quotas` WHERE `association` = ? AND `partner` = ?;');

                $quota = $db->query(
                        $query,
                        [
                            $this->id,
                            $partner->getID()
                        ]
                );

                $quota = $quota->fetch(PDO::FETCH_ASSOC);

                if (!$quota)
                    $userQuota = $this->createQuotaToPartner($partner);
                elseif ($quota['price'] <= $quota['payed'] && new DateTime() >= DateTime::createFromFormat('Y-m-d H:i:s', $quota['endDate'])) {
                    $partner->deposit($quota['payed'] -= $quota['price']);
                    $this->wallet += $quota['price'];

                    $userQuota = $this->renewPartnership($partner);
                } else
                    $userQuota = new Quota($this, $quota['price'], $quota['payed'], DateTime::createFromFormat('Y-m-d H:i:s', $quota['endDate']), DateTime::createFromFormat('Y-m-d H:i:s', $quota['startDate']));

                if (isset($userQuota))
                    $partner->recieveQuota($this, $userQuota);
            } catch (Exception $e) {
                die($e);
            }
        }
    }

    public function createQuotaToPartner(User &$user)
    {
        $now = new DateTime();

        $end = $now;
        if (!$this->payQuotaAtEntering)
            $end->add($this->timeSpanToPayQuota);

        try {
            $db = new DBConnection();

            $db->checkAccess();

            $db->beginTransaction();

            $createdQuota = $db->insert(
                'quotas',
                [
                    'partner' => $user->getID(),
                    'association' => $this->id,
                    'price' => $this->quotaPrice,
                    'payed' => 0,
                    'startDate' => $now->format('Y-m-d H:i:s'),
                    'endDate' => $end->format('Y-m-d H:i:s')
                ]
            );

            if (!$createdQuota)
                throw new Exception('Could not create a due.');
        } catch (Exception $e) {
            $db->rollBack();
            die($e);
        } finally {
            $db->commit();

            return new Quota($this, $this->quotaPrice, 0, $end, $now);
        }
    }

    public function renewPartnership(Partner $user)
    {
        $now = new DateTime();
        $end = $now;

        try {
            $db = new DBConnection();

            $db->checkAccess();

            $db->beginTransaction();

            $updatedQuota = $db->update(
                'quotas',
                [
                    'partner' => $user->getID(),
                    'association' => $this->id
                ],
                [
                    'price' => $this->quotaPrice,
                    'payed' => 0,
                    'startDate' => $now->format('Y-m-d H:i:s'),
                    'endDate' => $end->add($this->timeSpanToPayQuota)->format('Y-m-d H:i:s')
                ]
            );

            if (!$updatedQuota)
                throw new Exception('Could not create a quota.');
        } catch (Exception $e) {
            $db->rollBack();
            die($e);
        } finally {
            $db->commit();

            return new Quota($this, $this->quotaPrice, 0, $end, $now);
        }
    }

////////////////////////////////////////////////////

    /*public function getPartners()
    {
        $db = new SystemDB();

        if (!$db->pdo)
            die('Connection error');

        $associationsPartners = $db->query('SELECT * FROM `usersAssociations` WHERE `association` = ' . $this->id . ';')->fetchAll(PDO::FETCH_ASSOC);

        if (!$associationsPartners)
            return;

        foreach ($associationsPartners as $partner) {
            $users = $db->query("SELECT * FROM `users` WHERE `id` = " . $partner['user'] . ';')->fetchAll(PDO::FETCH_ASSOC);
        
            if (!$users)
                return;
            
            foreach ($users as $user) {
                $userRoles = $db->query("SELECT `role` FROM `usersAssociations` WHERE `user` = " . $user['id'] . ";")->fetchAll(PDO::FETCH_ASSOC);

                if (count($userRoles) > 0) {
                    $extends = "Partner";
                    foreach ($userRoles as $role) 
                        if (UsersManager::getTools()->getPremissionsManager()->checkPermissions(
                            $role['role'],
                            PermissionsManager::AP_PRESIDENT,
                            false
                        ))
                            $extends = 'President';
                }

                $partner = new $extends(
                    $user['username'],
                    null,
                    $user['realName'],
                    $user['email'],
                    $user['telephone'],
                    $user['permissions'],
                    false,
                    $user['id']
                );
                
                $this->partners[] = $partner;
            }
        }
    }*/

    public function publishNews(News $news) {
        $db = new DBConnection();

        $publishedNews = $db->update(
            'news',
            ['id' => $news->id],
            [
                'published' => 1,
                'publishTime' => (new DateTime())->format('Y-m-s H:i:s'),
                'publishedTitle' => $news->title,
                'publishedImage' => $news->image,
                'publishedArticle' => $news->getArticle()
            ]
        );

        if (!$publishedNews) {
            $errors[] = "Failed to create news";
            $_SESSION['news-errors'] = $errors;
            return;
        }
    }

    public function addNews(News $news)
    {
        if (empty($this->freeSpaceNews)) {
            $this->news[$this->newsCounter] = $news;
            $this->news[$this->newsCounter]->id = $this->newsCounter;
        } else {
            $this->news[$this->freeSpaceNews[0]] = $news;
            $this->news[$this->freeSpaceNews[0]]->id = $this->freeSpaceNews[0];
            unset($this->freeSpaceNews[0]);
            $this->freeSpaceNews = array_values($this->freeSpaceNews);
            ksort($this->news);
        }

        $this->newsCounter++;
    }

    public function deleteNews(int $i)
    {
        if ($this->newsCounter <= 0) {
            echo "error: this association doesn't have news.";
            return;
        }

        if ($i < 0) {
            echo "error: value below zero.";
            return;
        }

        if (!isset($this->news[$i])) {
            echo "error: there's no news here.";
            return;
        }

        $this->freeSpaceNews[] = $i;

        unset($this->news[$i]);

        $this->newsCounter--;
    }

    public function listNewsSimplified() {
        $list = "{$this->name}'s news list (number: {$this->newsCounter}):\n";

        foreach ($this->news as $news)
            $list .= "\t" . $news->readNewsSimple();

        return $list . "\n";
    }

    public function listNews() {
        $list = "{$this->name}'s news list (number: {$this->newsCounter}):\n";

        foreach ($this->news as $news)
            $list .= "\t" . $news;

        return $list . "\n";
    }

    public function listNewsById(int $id)
    {
        return $this->news[$id];
    }

    public function createEvent(string $title, string $description, DateTime $endDate)
    {
        $db = new DBConnection();

        $db->beginTransaction();

        $createdEvent = $db->insert(
            'events',
            [
                'association' => $this->id,
                'title' => $title,
                'description' => $description,
                'endDate' => $endDate->format('Y-m-d H:i:s')
            ]
        );

        $event = $db->query("SELECT `id` FROM `events` WHERE `association` = $this->id AND `title` = '$title' AND `description` = '$description' AND `endDate` = '" . $endDate->format('Y-m-d H:i:s') . '\';');

        if (!$event) {
            $_SESSION['news-errors'][] = "Failed to create event";
            $db->pdo->rollBack();
            die('Internal error');
        }

        $createdAssociationEvent = $db->insert(
            'associationsEvents',
            [
                'association' => $this->id,
                'event' => $event->fetch(PDO::FETCH_ASSOC)['id'],
                'isCreator' => true,
            ]
        );
        
        if (!$createdEvent || !$createdAssociationEvent) {
            $_SESSION['news-errors'][] = "Failed to create event";
            $db->rollBack();
            die('Internal error');
        }

        $db->commit();
    }
    
    public function createImage(string $title, array $image)
    {
        $db = new DBConnection();

        $createdImage = $db->insert(
            'image',
            [
                'association' => $this->id,
                'title' => $title,
                'path' => $image['name'],
            ]
        );

        if (!$createdImage) {
            $errors[] = "Failed to create image";
            $_SESSION['image-errors'] = $errors;
            return;
        }

        if (!file_exists(UPLOAD_PATH))
            mkdir(UPLOAD_PATH, 0755, true);

        if (!move_uploaded_file($image['tmp_name'], UPLOAD_PATH . '/' . $image['name'])) {
            $errors[] = "Failed uploading file";
            $_SESSION['image-errors'] = $errors;
            return;
        }

        $_SESSION['image-created'] = 'A news was created.';

        unset($_SESSION['image']);
    }
    public function initEvent(string $title, string $description, DateTime $endDate, int $id)
    {
        $event = new Events($this, $title, $description, $endDate, $id);

        return $event;
    }

    public function useEvent(Events $event)
    {
        if(in_array($event, $this->events)) {
            echo "error: you are the creator of this event";

            return;
        }

        $event->addAssociation($this);
    }

    public function exitEvent(Events $event)
    {
        if(in_array($event, $this->events)) {
            echo "error: you are the creator of this event";

            return;
        }

        $event->removeAssociation($this);
    }

    public function endEvent(int $i)
    {
        if (empty($this->events)) {
            echo "error: value below zero.";

            return;
        }

        if ($i < 0) {
            echo "error: value below zero.";

            return;
        }

        if (!isset($this->events[$i])) {
            echo "error: event doesn't exists";

            return;
        }

        unset($this->events[$i]);
    }

    public function listEvents()
    {
        $list = "{$this->name}'s events list (number: " . count($this->events) . "):\n";

        foreach ($this->events as $event)
            $list .= "\t" . $event;

        return $list . "\n";
    }

    public function checkIfAdmni(Partner $user)
    {
        $db = new DBConnection();

        $db->checkAccess();

        try {
            $role = $db->query(
                $db->createQuery('SELECT * FROM `usersAssociations` WHERE `user` = ? AND `association` = ?;'),
                [$user->getID(), $this->id]
            );

            if (!$role)
                throw new Exception('error.');

            $role = $role->fetch(PDO::FETCH_ASSOC);

            if (!$role)
                return false;

            return UsersManager::getTools()->getPremissionsManager()->checkPermissions(
                $role['role'],
                PermissionsManager::AP_PARTNER_ADMNI,
                false
            );
        } catch (Exception $e) {
            die($e);
        }
    }
    /*
    private $images;

    public function listPartner(): void
    {

    }

    public function removePartner(): void
    {

    }

    public function validateSubscriptions(): void
    {

    }

    public function insertLinks(): void
    {

    }

    public function listQuotas(): void
    {

    }
    */
}
