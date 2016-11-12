<?php

namespace Imperial\Simp\Parsers;

use Symfony\Component\DomCrawler\Crawler;
use Storage;

abstract class AbstractHtmlParser extends AbstractParser
{
    protected $crawler;

    protected function load()
    {
        $html = Storage::get($this->specification->path);
        $this->setText($html);
        $this->crawler = new Crawler($html);

        // $this->setDetails(TODO);
    }

    public function getParsers()
    {
        return [
            //  'Html' TODO
        ];
    }

}
