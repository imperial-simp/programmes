<?php

namespace Imperial\Simp\Parsers\Pdf\Programme\Postgrad;

use Imperial\Simp\Parsers\Pdf\Programme\Undergrad\OldFormat as BaseParser;

class OldFormat extends BaseParser
{
    protected $tidyReplacements = [
        '/Programme Specification \(Master\'s Level\)/' => '',
    ];
    public static function identifyParser($text, $details = [])
    {
        return preg_match('/^Programme Specification \(Master/', $text);
    }
}
