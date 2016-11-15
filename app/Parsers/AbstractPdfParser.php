<?php

namespace Imperial\Simp\Parsers;

use Imperial\Simp\Specification;

abstract class AbstractPdfParser extends AbstractParser
{
    protected $lines = [];

    protected function afterSetText()
    {
        $this->lines = explode(PHP_EOL, $this->text);
    }

}
