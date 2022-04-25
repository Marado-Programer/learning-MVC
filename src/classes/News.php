<?php

/**
 * 
 */

class News
{
    public $id, $title, $image;
    public $publishTime;
    public $association;

    public function __construct(
        Association $association,
        Partner $author,
        string $title,
        string $image,
        ?DateTime $publishTime = null,
        ?DateTime $lastEditTime = null,
        ?int $id = null
    ) {
        $this->association = clone $association;        
        $this->author = clone $author;
        $this->title = $title;
        $this->image = $image;
        $this->publishTime = $publishTime;
        $this->lastEditTime = $lastEditTime;
        $this->id = $id ?? -1;
    }

    public function getArticle()
    {
        $db = new DBConnection();

        return $db->query(
            $db->createQuery('SELECT `article` FROM `news` WHERE `id` = ?'),
            [$this->id]
        )->fetch(PDO::FETCH_ASSOC)['article'];
    }

    public function readNewsSimple()
    {
        return "(#{$this->id})News --- {$this->title}: {$this->article}\n";
    }

    public function __toString()
    {
        return "<p><a href=\"" . HOME_URI . '/article/' . $this->id . '">' . "(#{$this->id})News --- {$this->title}:</a></p><ul>\n"
            . (isset($this->publishTime) ? "\t<li>publish time -> " . $this->publishTime->format('Y-m-d H:i:s') . "</li>\n" : '')
            . "\t<li>association -> <a href=\"" . HOME_URI . '/@' . $this->association->nickname . '">' . $this->association->name . "</a></li></ul>\n\n";
    }
}
