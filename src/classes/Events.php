<?php

/**
 *
 */

class Events
{
    public $id, $title, $description;

    public $associations = [];

    public $registrations = [];

    public DateTime $endDate;

    public function __construct(Association $association, string $title, string $description, DateTime $endDate, ?int $id)
    {
        $this->title = $title;
        $this->description = $description;
        $this->associations['ini'] = $association;
        $this->endDate = $endDate;
        $this->id = $id;
        $this->getRegistrations();
    }

    public function getRegistrations()
    {
        $db = new SystemDB();

        if (!$db->pdo)
            die('Connection error');

        $registrations = $db->query("SELECT * FROM `registrations` WHERE `event` = $this->id;")->fetchAll(PDO::FETCH_ASSOC);

        if (!$registrations)
            return;

        foreach ($registrations as $registration) {
            $users = $db->query("SELECT * FROM `users` WHERE `id` = " . $registration['partner'] . ';')->fetchAll(PDO::FETCH_ASSOC);
        
            if (!$users)
                return;
            
            foreach ($users as $user) {
                $this->registrations[] = new Registration(
                    $this,
                    new Partner(
                        $user['username'],
                        null,
                        $user['realName'],
                        $user['email'],
                        $user['telephone'],
                        $user['permissions'],
                        false,
                        $user['id']
                    )
                );
            }
        }
    }

    public function addAssociation(Association $association)
    {
        $db = new SystemDB();

        if (!$db->pdo)
            die('Connection error');

        $db->pdo->beginTransaction();

        $associationAddiction = $db->insert(
            'associationsEvents',
            [
                'event' => $this->id,
                'Association' => $association->id,
                'isCreator' => 0
            ]
        );

        if (!$associationAddiction) {
            $db->pdo->rollBack();
            die('Failed to add association to event');
        }

        $db->pdo->commit();
        
        if ($this->associations['ini'] !== $association && !in_array($association, $this->associations))
            $this->associations[] = $association;
    }

    public function removeAssociation(Association $association)
    {
        if ($this->associations['ini'] !== $association && in_array($association, $this->associations)) {
            foreach ($this->associations as $i => $v)
                if ($v === $association) {
                    unset($this->associations[$i]);
                    break;
                }
            $this->associations = array_values($this->associations);
        }
    }

    public function createRegistration(Partner $partner)
    {
        $db = new SystemDB();

        if (!$db->pdo)
            die('Connection error');

        $db->pdo->beginTransaction();

        $associationAddiction = $db->insert(
            'registrations',
            [
                'event' => $this->id,
                'partner' => $partner->id,
            ]
        );

        if (!$associationAddiction) {
            $db->pdo->rollBack();
            die('Failed to add registration to event');
        }

        $db->pdo->commit();

        $this->registrations[] = new Registration($this, $partner);
    }

    public function __toString()
    {
        return "<p>Event titled {$this->title}:</p><ul>\n"
            . "\t<li>Description: " . $this->description . "</li>\n"
            . "\t<li>Number of participating associations: " . count($this->associations) . "</li>\n"
            . "\t<li>Number of registrations: " . count($this->registrations) . "</li></ul>\n\n";
    }
}
