<?php

namespace Imperial\Simp\Parsers\Pdf\Programme\Undergrad;

use Imperial\Simp\Parsers\Pdf\Programme\Undergrad\NewFormat as BaseParser;

class BiomedFormat extends BaseParser
{

    public static function identifyParser($text, $details = [])
    {
        return preg_match('/^(.*Eng\s+Biomedical\s+Engineering)/s', $text);
    }

}
