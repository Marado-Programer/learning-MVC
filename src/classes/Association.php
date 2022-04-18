<?php

/**
 *
 */

class Association
{
    public $id;
    public $name, $nickname, $address, $telephone, $taxpayerNumber;
    public $news = [];
    public $events = [];
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
        User $president
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->nickname = $nickname;
        $this->address = $address;
        $this->telephone = $telephone;
        $this->taxpayerNumber = $taxpayerNumber;
        $this->partners['president'] = $president;
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
                'associationID' => $this->id,
                'eventID' => $event->fetch(PDO::FETCH_ASSOC)['id'],
                'isCreator' => true,
            ]
        );
        
        if (!$createdEvent || !$createdAssociationEvent) {
            $_SESSION['news-errors'][] = "Failed to create event";
            $db->pdo->rollBack();
            die('Internal error');
        }

        $db->pdo->commit();
        
        $this->events[] = new Events($this, $title, $description, $endDate, null);
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
        $db = new SystemDB();

        if (!$db->pdo)
            die('Connection error');

        $db->pdo->beginTransaction();

        $createdRole = $db->insert(
            'usersAssociations',
            [
                'userID' => $user->id,
                'associationID' => $this->id,
                'role' => PermissionsManager::AP_PARTNER,
            ]
        );

        if (!$createdRole) {
            $db->pdo->rollBack();
            die('Could not enter event.');
        }

        $db->pdo->commit();
        
        $this->partners[] = $user;
        //$this->createDue($this->partners[count($this->partners)], new DateTime());
    }

    public function initPartner(User $user)
    {
        $this->partners[] = $user;
    }

    public function renewPartner(int $id)
    {
        $now = new DateTime();
        $this->createDue($this->partners[$id], $now->modify('+1 month'), $now);
    }

    public function createDue(Partner &$user, DateTime $endDate)
    {
        $user->recieveDue($this, $this->priceDue, $endDate);
    }

    public function registPartner(Partner &$partner, int $event)
    {
        $this->events[$event]->registrations[] = new Registration($this->events[$event], $partner);
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
