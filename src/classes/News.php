<?php

/**
 * 
 */

class News
{
    public $id, $title, $article, $image;
    public $publishTime;
    public $association;

    public function __construct(
        Association $association,
        Partner $author,
        string $title,
        string $image,
        string $article,
        DateTime $publishTime,
        DateTime $lastEditTime,
        int $id
    ) {
        $this->association = clone $association;        
        $this->author = clone $author;
        $this->title = $title;
        $this->image = $image;
        $this->article = $article;
        $this->publishTime = $publishTime;
        $this->lastEditTime = $lastEditTime;
        $this->id = $id;
    }

    public function readNewsSimple()
    {
        return "(#{$this->id})News --- {$this->title}: {$this->article}\n";
    }

    public function __toString()
    {
        return "(#{$this->id})News --- {$this->title}:\n"
            . "\tarticle -> {$this->article}\n"
            . "\timage -> {$this->image}\n"
            . "\tpublish time -> " . $this->publishTime->format('Y-m-d H:i:s') . "\n"
            . "\tassociation -> {$this->association->name}\n\n";
    }
}
