<?php

namespace Imperial\Simp\Parsers;

use Imperial\Simp\Specification;

abstract class AbstractPdfParser extends AbstractParser
{
    protected $lines = [];

    protected function load()
    {
        $pdf = app('parser.pdf')->parseFile(storage_path('app/'.$this->specification->path));
        $this->setDetails($pdf->getDetails());
        $this->setText($pdf->getText());
    }

    protected function setText($text)
    {
        $text = $this->tidyText($text);
        $this->text = $text;
        $this->lines = explode(PHP_EOL, $text);
    }

}
