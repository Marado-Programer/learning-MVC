<?php

/**
 *
 */

class Quota extends Updatable
{
    public $startDate, $endDate, $price, $payed;

    public $association;
    public $partner;

    public function __construct(
        Association $association,
        float $price,
        float $payed,
        DateTime $endDate,
        DateTime $startDate,
    ) {
        $this->association = $association;
        $this->price = $price;
        $this->payed = $payed;
        $this->endDate = $endDate;
        $this->startDate = $startDate;
    }

    public function setPartner(Partner $partner)
    {
        $this->partner = $partner;

        return $this;
    }

    public function receiveMoney(Partner &$user, float $money)
    {
        $reminder = $this->price - $this->payed;
        $change = $reminder < $money ? $money - $reminder : 0;

        $this->payed += $user->useWallet($money - $change);

        $this->checkUpdate();
    }

    protected function update()
    {
        if (isset($this->db)) {
            try {
                print_r($this->association->getID());
                print_r($this->partner->getID());
                print_r($this->payed);

                $this->db->checkAccess();

                $this->db->beginTransaction();

                $this->db->update(
                    'quotas',
                    [
                        'association' => $this->association->getID(),
                        'partner' => $this->partner->getID()
                    ],
                    [
                        'payed' => $this->payed
                    ]
                );
            } catch (Exception $e) {
                $this->db->rollBack();
                die($e);
            }
            $this->db->commit();
        }
    }
}
