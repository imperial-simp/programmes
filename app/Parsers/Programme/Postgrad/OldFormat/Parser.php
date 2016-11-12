<?php

namespace Imperial\Simp\Parsers\Programme\Postgrad\OldFormat;

use Imperial\Simp\Parsers\Programme\Undergrad\NewFormat\Parser as BaseParser;

class Parser extends BaseParser
{
    public function read()
    {
        return $this;
    }

    public static function identifyParser($text, $details = [])
    {
        return preg_match('/^Programme Specification \(Master/', $text);
    }
}
