<?php

/**
 * 
 */

class NewsModel extends MainModel
{
    public function getNews()
    {
        $news = $this->db->query('SELECT * FROM `news`;');

        if (!$news)
            return;

        foreach ($news->fetchAll(PDO::FETCH_ASSOC) as $aNews)
            $this->controller->news->add($this->instanceNews($aNews));
    }
    public function getNewsByDate($date)
    {
        $query = 'SELECT * FROM `news`;';
        if (isset($date[0])) {
            $this->controller->date = (new DateTime())->setTime(0, 0);
            if ($date[0] == 'today') {
                $query = 'SELECT * FROM `news`'
                    . ' WHERE `publishTime`'
                    . ' BETWEEN \'' . $this->controller->date->format('Y-m-d H:i:s') . '\''
                    . ' AND \'' . $this->controller->date->modify('+1 day')->format('Y-m-d H:i:s') . '\';';
            } else {
                if ($date[0] >= 1970 && $date[0] <= $this->controller->date->format('Y')) {
                    $year = $date[0];
                    $this->controller->use = 'Y';
                    if (isset($date[1]) && $date[1] >= 1 && $date[1] <= 12) {
                        $month = ((strlen($date[1]) != 2) ? '0' . $date[1] : $date[1]);
                        $this->controller->use = 'M';
                        if (isset($date[2]) && $date[2] >= 1 && $date[2] <= 31) {
                            $day = ((strlen($date[2]) != 2) ? '0' . $date[2] : $date[2]);
                            $this->controller->use = 'D';
                        }
                    }
                }
                $year ??= $this->controller->date->format('Y');
                $month ??= 0;
                $day ??= 0;
                $this->controller->date = $this->controller->date->setDate($year, $month, $day);
                $query = 'SELECT * FROM `news`'
                    . ' WHERE `publishTime`'
                    . ' BETWEEN \'' . $this->controller->date->format('Y-m-d H:i:s') . '\''
                    . ' AND \'' . $this->controller->date->add(new DateInterval('P1' . $this->controller->use))->format('Y-m-d H:i:s') . '\';';
                $this->controller->date->sub(new DateInterval('P1' . $this->controller->use));
            }
        }

        $news = $this->db->query($query);

        if (!$news)
            return;

        foreach ($news->fetchAll(PDO::FETCH_ASSOC) as $article)
            $this->controller->news->add($this->instanceNews($article));
    }

    public function getNewsByID($id)
    {
        $news = $this->db->query("SELECT * FROM `news` WHERE `id` = $id");

        if (!$news)
            return;

        $this->controller->news->add($this->instanceNews($news->fetch(PDO::FETCH_ASSOC)));
    }

    public function instanceNews(array $news)
    {
        return new News(
            $this->getAssociationByID($news['association']),
            $this->getPartnerByID($news['author']),
            $news['title'],
            $news['image'],
            $news['article'],
            DateTime::createFromFormat('Y-m-d H:i:s', $news['publishTime']),
            DateTime::createFromFormat('Y-m-d H:i:s', $news['lastEditTime']),
            $news['id']
        );
    }

    public function getAssociationByID($id)
    {
        $association = $this->db->query("SELECT * FROM `associationWPresident` WHERE `id` = $id;");

        if (!$association)
            return;

        return $this->instanceAssociation($association->fetch(PDO::FETCH_ASSOC));
    }

    private function instanceAssociation(array $association)
    {
        return new Association(
            $association['id'],
            $association['name'],
            $association['nickname'],
            $association['address'],
            $association['telephone'],
            $association['taxpayerNumber'],
            $this->getPartnerByID($association['president'])
        );
    }

    private function getPartnerByID(int $id)
    {
        $user = $this->db->query("SELECT * FROM `users` WHERE `id` = $id;");

        if (!$user)
            return;

        return $this->instancePartnerByID($user->fetch(PDO::FETCH_ASSOC));
    }

    private function instancePartnerByID(array $user)
    {
        return new Partner(
            $user['username'],
            null,
            $user['realName'],
            $user['email'],
            $user['telephone'],
            $user['permissions'],
            false,
            $user['id']
        );
    }
}

