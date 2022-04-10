<?php

/**
 *
 */

class Dues
{
    private $startDate, $endDate, $price;

    private $partner;
    private $association;

    public function __construct(
        Partner $partner,
        Association $association,
        float $price,
        DateTime $endDate,
        DateTime $startDate = $endDate
    ) {
        $this->partner = $partner;
        $this->association = $association;
        $this->price = $price;
        $this->endDate = $endDate;
        $this->startDate = $startDate;
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
