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
    private $role;
    
    public function __construct($list)
    {
        $this->list = $list;
        $this->user = UserSession::getUser();
        $this->permissionsChecker = UsersManager::getTools()->getPremissionsManager();
        $this->db = new DBConnection();

        $this->query = $this->db->createQuery('
            SELECT `usersAssociations`.`role`
            FROM `usersAssociations`
            INNER JOIN `quotas`
            ON `usersAssociations`.`user` = `quotas`.`partner`
            WHERE `usersAssociations`.`user` = ?
            AND `usersAssociations`.`association` = ?
            AND NOT (
                `quotas`.`payed` < `quotas`.`price`
                AND `quotas`.`endDate` <= ?
            );
        ');

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
            $this->setRole($this->current());
        } while (!$this->canWrite());
    }

    public function rewind(): void
    {
        $this->pos = 0;

        $this->setRole($this->current());
        if (!$this->canWrite())
            $this->next();
    }

    public function valid(): bool
    {
        return isset($this->list->getList()[$this->key()]);
    }

    private function setRole(Association $association)
    {
        $this->role = $this->db->query(
            $this->query,
            [
                $this->user->getID(),
                $association->getID(),
                (new DateTime())->format('Y-m-d H:i:s')
            ]
        )->fetch(PDO::FETCH_ASSOC);
    }

    private function canWrite(): bool
    {
        if (!$this->role)
            return false;

        return $this->permissionsChecker->checkPermissions(
            $this->role['role'],
            PermissionsManager::AP_CREATE_NEWS,
        );
    }

    public function canPublish(): bool
    {
        if (!$this->role)
            return false;

        return $this->permissionsChecker->checkPermissions(
            $this->role['role'],
            PermissionsManager::AP_PUBLISH_NEWS,
        );
    }
}

