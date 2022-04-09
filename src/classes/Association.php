<?php

/**
 *
 */

class Association
{
    public $id;
    public $name, $address, $telephone, $taxpayerNumber;
    public $news = [];
    public $events = [];
    private $newsCounter = 0;
    private $freeSpaceNews = [];

    public $partners;
    public $president;

    public function __construct(
        string $name,
        string $address,
        int $telephoneInternationalCodePrefix,
        int $telephoneNumber,
        int $taxpayerNumber,
        User $president
    ) {
        $this->name = $name;
        $this->address = $address;
        $this->telephone = [
            'internacionalCodePrefix' => '+' . $telephoneInternationalCodePrefix,
            'number' => $telephoneNumber
        ];
        $this->taxpayerNumber = $taxpayerNumber;
        $this->partners[] = $president;
        $this->president = $president;
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

    public function createEvent(string $title, string $description)
    {
        $this->events[] = new Events($this, $title, $description);
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

    public function addPartner(User &$user)
    {
        $this->partners[] = $user;
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
            . "\tnumber of events -> " . count($this->events) . "\n\n";
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

    public function insertQuotas(): void
    {

    }

    public function listQuotas(): void
    {

    }
    */
}
