<?php

/**
 *
 */

class Association
{
    public $id;
    public $name, $nickname, $address, $telephone, $taxpayerNumber;
    public $news = [];
    private $newsCounter = 0;
    private $freeSpaceNews = [];

    public $partners;

    private $priceDue = 5.00;

    public function __construct(
        ?int $id,
        string $name,
        string $nickname,
        string $address,
        string $telephone,
        int $taxpayerNumber,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->nickname = $nickname;
        $this->address = $address;
        $this->telephone = $telephone;
        $this->taxpayerNumber = $taxpayerNumber;
    }

    public function getPartners()
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
                        if (UsersManager::getTools()->getPermissionsManager()->checkPermissions(
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
    }

    public function publishNews(News $news) {
        $db = new SystemDB();

        if (!$db->pdo)
            die('Connection error');

        $publishedNews = $db->update(
            'news',
            'id',
            $news->id,
            [
                'published' => 1,
                'publishTime' => (new DateTime())->format('Y-m-s H:i:s')
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
        $db = new SystemDB();

        if (!$db->pdo)
            die('Connection error');

        $db->pdo->beginTransaction();

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
            $db->pdo->rollBack();
            die('Internal error');
        }

        $db->pdo->commit();
    }
    
    public function createImage(string $title, array $image)
    {
        $db = new SystemDB();

        if (!$db->pdo)
            die('Connection error');

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

    public function createPartner(User $user)
    {
        $this->initPartner($user);
        $this->createDue($user, $this->priceDue, new DateTime());
    }

    public function initPartner(User $user)
    {
        $db = new SystemDB();

        if (!$db->pdo)
            die('Connection error');

        $role = $db->query('SELECT * FROM `usersAssociations` WHERE `user` = ' . $user->id . ' AND `association` = ' . $this->id . ';');

        if (!$role) {
            die('Could not enter event.');
        }
        
        if ($role->fetchAll(PDO::FETCH_ASSOC)[0]['role'] == PermissionsManager::AP_PRESIDENT)
            $this->partners['president'] = $user;
        else
            $this->partners[] = $user;
    }

    public function renewPartnership(Partner $user)
    {
        $this->updateQuota($user);
    }

    protected function updateQuota(Partner $user)
    {
        $db = new SystemDB();

        if (!$db->pdo)
            die('Connection erro');

        $db->pdo->beginTransaction();

        $now = new DateTime();

        $updatedDue = $db->update(
            'dues',
            [
                'partner' => $user->id,
                'association' => $this->id
            ],
            [
                'price' => $this->priceDue,
                'startDate' => $now->format('Y-m-d H:i:s'),
                'endDate' => $now->add(new DateInterval('P1D'))->format('Y-m-d H:i:s')
            ]
        );

        if (!$updatedDue) {
            $db->pdo->rollBack();
            die('Could not create a due.');
        }

        $db->pdo->commit();
    }

    public function createDue(User $user, float $price, DateTime $endDate, DateTime $startDate = null)
    {
        if (!isset($startDate))
            $startDate = $endDate;

        $db = new SystemDB();

        if (!$db->pdo)
            die('Connection erro');

        $db->pdo->beginTransaction();

        $createdDue = $db->insert(
            'dues',
            [
                'partner' => $user->id,
                'association' => $this->id,
                'price' => $price,
                'startDate' => $startDate->format('Y-m-d H:i:s'),
                'endDate' => $startDate->format('Y-m-d H:i:s')
            ]
        );

        if (!$createdDue) {
            $db->pdo->rollBack();
            die('Could not create a due.');
        }

        $db->pdo->commit();

        new Dues($user, $this, $price, $endDate, $startDate);
    }

    public function registPartner(Partner &$partner, int $event)
    {
        $this->events[$event]->registrations[] = new Registration($this->events[$event], $partner);
    }

    public function checkIfAdmin(User $user)
    {
        $db = new SystemDB();

        if (!$db->pdo)
            return;

        if (!$role = $db->query("SELECT * FROM `usersAssociations` WHERE `user` = $user->id AND `association` = $this->id;")->fetchAll(PDO::FETCH_ASSOC)[0])
            return;

        return UsersManager::getTools()->permissionManager->checkPermissions(
            $role['role'],
            PermissionsManager::AP_PARTNER_ADMNI,
            false
        );
    }

    public function getTelephone()
    {
        return $this->telephone['internacionalCodePrefix'] . ' ' . $this->telephone['number'];
    }

    public function __toString()
    {
        return "(#{$this->id})Association --- {$this->name}:\n"
            . "\taddress -> {$this->address}\n"
            . "\ttelephone -> " . $this->getTelephone() . "\n"
            . "\ttaxpayer number -> {$this->taxpayerNumber}\n"
            . "\tnumber of news -> {$this->newsCounter}\n"
            . "\tnumber of events created -> " . count($this->events) . "\n\n";
    }

    public function __clone()
    {
        $this->news = [];
        $this->partners = ['president' => $this->partners['president']];
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
