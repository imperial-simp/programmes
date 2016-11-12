<?php

namespace Imperial\Simp\Parsers\Pdf\Module\Module;

use Imperial\Simp\Parsers\Pdf\Module\AbstractPdfParser as BaseParser;

class NewFormat extends BaseParser
{
    public function read()
    {
        return $this;
    }

    public static function identifyParser($text, $details = [])
    {
        return preg_match('/^Module Outline/', $text);
    }

}
