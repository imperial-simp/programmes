<?php

namespace Imperial\Simp\Parsers;

use Imperial\Simp\Specification;

abstract class AbstractPdfParser extends AbstractParser
{
    public function load()
    {
        $pdf = app('parser.pdf')->parseFile(storage_path('app/'.$this->specification->path));
        $this->setDetails($pdf->getDetails());
        $this->setText($pdf->getText());
    }

}
