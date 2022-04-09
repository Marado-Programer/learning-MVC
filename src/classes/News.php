<?php

/**
 * 
 */

class News
{
    public $id, $title, $article, $image;
    public $publishTime;
    public $association;

    public function __construct(Association $association, string $title, string $article, string $image = '')
    {
        $this->association = clone $association;        
        $this->title = $title;
        $this->article = $article;
        $this->image = $image;
        $this->publishTime = new DateTime();
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
