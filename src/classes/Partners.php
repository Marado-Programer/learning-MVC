<?php

/**
 *
 */

class Partners
{
    private $id, $name, $email, $login, $password;

    private $association;

    private $dues = [];

    public function __construct(
        int $id,
        string $name,
        string $email,
        string $login,
        string $password,
        Association $association
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->login = $login;
        $this->password = $password;
        $this->association = $association;
    }

    public function checkData(): void
    {
    }

    public function changeData(): void
    {
    }

    public function subscriptions(): void
    {
    }

    public function duesPayment(): void
    {
    }
}
