<?php

/**
 *
 */

class PDOConfig
{
    private $dsn, $username, $password;

    public function __construct(
        string $dsn,
        ?string $username = null,
        ?string $password = null,
    ) {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
    }

    public function getDsn()
    {
        return $this->dsn;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setDsn(string $dsn)
    {
        $this->dsn = $dsn;
    }

    public function setUsername(string $username)
    {
        $this->username = $username;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;
    }
}

