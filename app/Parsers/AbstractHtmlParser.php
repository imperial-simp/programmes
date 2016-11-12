<?php

namespace Imperial\Simp\Parsers;

use Symfony\Component\DomCrawler\Crawler;

abstract class AbstractHtmlParser extends AbstractParser
{
    protected $crawler;

    protected function crawler()
    {
        if (!$this->crawler) {
            $this->crawler = new Crawler($this->text);
        }

        return $this->crawler;
    }

    public function getParsers()
    {
        return [
            //  'Html' TODO
        ];
    }

}
