<?php

/**
 *
 */

class Dues
{
    private $id, $idPartner, $startDate, $endDate, $price;

    private $partner;

    public function __construct(
        int $id,
        Partners $partner,
        DateTime $startDate,
        DateTime $endDate,
        float $price
    ) {
        $this->id = $id;
        $this->partner = $partner;
        $this->idPartner = $this->partner->id;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->price = $price;
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
