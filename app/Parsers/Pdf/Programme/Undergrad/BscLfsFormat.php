<?php

namespace Imperial\Simp\Parsers\Pdf\Programme\Undergrad;

use Imperial\Simp\Parsers\Pdf\Programme\Undergrad\OldFormat as BaseParser;

class BScLfsFormat extends BaseParser
{
    public function read()
    {
        return $this;
    }

    public static function identifyParser($text, $details = [])
    {
        return parent::identifyParser($text) && preg_match('/(.*with (French|German|Spanish) for Science.*)/s', $text);
    }

}
