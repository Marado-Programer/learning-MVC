<?php

/**
 *
 */

class AssociationsUserCanWriteNewsIterator implements Iterator
{
    private $list;
    private $pos;

    private $user;
    private $permissionsChecker;
    private $db;

    private $query;
    
    public function __construct($list)
    {
        $this->list = $list;
        $this->user = UserSession::getUser();
        $this->permissionsChecker = UsersManager::getTools()->getPremissionsManager();
        $this->db = new DBConnection();

        $this->query = $this->db->createQuery('SELECT `role` FROM `usersAssociations` WHERE `user` = ? AND `association` = ?;');

        $this->rewind();
    }

    public function current()
    {
        return $this->list->getList()[$this->key()];
    }

    public function key()
    {
        return $this->pos;
    }

    public function next(): void
    {
        do {
            $this->pos++;
            if (!$this->valid())
                break;
        } while (!$this->canWrite($this->current()));
    }

    public function rewind(): void
    {
        $this->pos = 0;

        if (!$this->canWrite($this->current()))
            $this->next();
    }

    public function valid(): bool
    {
        return isset($this->list->getList()[$this->key()]);
    }

    private function canWrite(Association $association): bool
    {
        $role = $this->db->query(
            $this->query,
            [$this->user->getID(), $association->getID()]
        )->fetch(PDO::FETCH_ASSOC);

        if (!$role)
            return false;

        return $this->permissionsChecker->checkPermissions(
            $role['role'],
            PermissionsManager::AP_CREATE_NEWS,
            false
        );
    }

    public function canPublish(Association $association): bool
    {
        $role = $this->db->query(
            $this->query,
            [$this->user->getID(), $association->getID()]
        )->fetch(PDO::FETCH_ASSOC);

        if (!$role)
            return false;

        return $this->permissionsChecker->checkPermissions(
            $role['role'],
            PermissionsManager::AP_PUBLISH_NEWS,
            false
        );
    }
}

