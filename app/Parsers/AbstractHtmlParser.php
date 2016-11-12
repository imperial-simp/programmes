<?php

namespace Imperial\Simp\Parsers;

use Symfony\Component\DomCrawler\Crawler;
use Storage;

abstract class AbstractHtmlParser extends AbstractParser
{
    protected $crawler;

    public function load()
    {
        $html = Storage::get($this->specification->path);
        $this->setText($html);

        // $this->setDetails($pdf->getDetails());
    }

}
