<?php

/**
 *
 */

class Partner extends User
{
    public $dues = [];

    public function listYourAssociations()
    {
        $list = "{$this->username}'s associations where he's president list:\n";
        foreach ($this->associations as $association)
            $list .= $association;
        return $list;
    }

    public function exitYourAssociation(int $i, User &$user = null)
    {
        if (in_array($this->associations[$i], $this->yourAssociations)) {
            if (!isset($user)) {
                echo "error: you are the president of this association, please give a user to pass the president role.";

                return;
            }
            $this->associations[$i]->president = $user;
        }
        unset($this->associations[$i]);
    }

    public function recieveDue(Association $association, float $price, DateTime $endDate, DateTime $startDate = null)
    {
        $this->dues[] = new Dues($this, $association, $price, $endDate, $startDate);
    }

    public function enterEvent(Events $event) {
        $event->createRegistration($this);
    }

    public function createNews(
        Association $association,
        string $title,
        array $image,
        string $article
    ) {
        $db = new SystemDB();

        if (!$db->pdo)
            die('Connection error');

        $db->pdo->beginTransaction();

        $createdNews = $db->insert(
            'news',
            [
                'association' => $association->id,
                'author' => $this->id,
                'title' => $title,
                'image' => $image['name'],
                'article' => $article,
                'published' => 0,
                'lastEditTime' => ($now = new Datetime())->format('Y-m-d H:i:s')
            ]
        );

        if (!$createdNews) {
            $errors[] = "Failed to create news";
            $_SESSION['news-errors'] = $errors;
            $db->pdo->rollBack();
            return;
        }

        $db->pdo->commit();

        $newNews = $db->query(
            "SELECT `id` FROM `news`
            WHERE `association` = ?
            AND `author` = ?
            AND `title` = ?
            AND `image` = ?
            AND `article` = ?
            AND `published` = 0
            AND `lastEditTime` = ?",
            [
                $association->id,
                $this->id,
                $title,
                $image['name'],
                $article,
                $now->format('Y-m-d H:i:s')
            ]
        );

        if (!$newNews) {
            $_SESSION['news-errors'][] = "Failed to creating news.";
            die('Internal error');
        }

        if (!file_exists(UPLOAD_PATH))
            mkdir(UPLOAD_PATH, 0755, true);

        if (!move_uploaded_file($image['tmp_name'], UPLOAD_PATH . '/' . $image['name'])) {
            $errors[] = "Failed uploading file";
            $_SESSION['news-errors'] = $errors;
            return;
        }

        $_SESSION['news-created'] = 'A news was created.';

        unset($_SESSION['news']);

        return new News(
            clone $association,
            clone $this,
            $title,
            $image['name'],
            $article,
            null,
            $now,
            $newNews->fetchAll(PDO::FETCH_ASSOC)[0]['id']
        );
    }

    public function payQuota(Association $association)
    {
        foreach ($this->userDues as $quota)
            if ($quota->association->id = $association->id) {
                $toPayQuota = $quota;
                break;
            }

        if (!isset($toPayQuota))
            return;

        $association->renewPartnership($this);
    }
}

