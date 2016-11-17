<?php

namespace Imperial\Simp\Parsers;

use Imperial\Simp\Specification;

abstract class AbstractPdfParser extends AbstractParser
{
    protected $lines = [];

    protected function afterSetText()
    {
        $this->text = preg_replace('/ {2,}/', ' ', $this->text);
        $this->lines = explode(PHP_EOL, $this->text);
    }

}
