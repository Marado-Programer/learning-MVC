<?php

/**
 *
 */

class Dues
{
    public $startDate, $endDate, $price;

    public $association;

    public function __construct(
        User $user,
        Association|int $association,
        float $price,
        DateTime $endDate,
        DateTime $startDate,
    ) {
        if (is_numeric($association))
            $association = $this->instanceAssociation($association);
        $this->association = $association;
        $this->price = $price;
        $this->endDate = $endDate;
        $this->startDate = $startDate;
        $this->deliver($user);
    }

    public function instanceAssociation($id)
    {
        $db = new SystemDB();

        if (!$db->pdo)
            return;

        $associationData = $db->query(
            'SELECT * FROM `associationWPresident` WHERE `id` = ?',
            [$id]
        );

        if (!$associationData)
            return;

        $association = $associationData->fetch(PDO::FETCH_ASSOC);

        if (($user = clone UserSession::getUser())->id != $association['president']) {
            $userData = $db->query(
                'SELECT * FROM `users` WHERE `id` = ?',
                [$association['president']]
            );

            if (!$userData)
                return;

            $userDataFetched = $userData->fetch(PDO::FETCH_ASSOC);

            $user = new User(
                $userDataFetched['username'],
                null,
                $userDataFetched['realName'],
                $userDataFetched['email'],
                $userDataFetched['telephone'],
                PermissionsManager::P_ZERO
            );
        }

        return $user->initAssociation(
            $association['id'],
            $association['name'],
            $association['nickname'],
            $association['address'],
            $association['telephone'],
            $association['taxpayerNumber'],
        );
    }

    private function deliver(User $user)
    {
        $user->addDue($this);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIdPartner()
    {
        return $this->idPartner;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    public function setEndDate(DateTime $endDate)
    {
        $this->endDate = $endDate;
    }

    public function calcPrice()
    {
        return $this->price;
    }

    // addDues() ???

    // listDues
    public function __toString()
    {
        return "";
    }
}
