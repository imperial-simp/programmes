<?php

namespace Imperial\Simp\Parsers\Pdf\Programme\Undergrad;

use Imperial\Simp\Parsers\Pdf\Programme\Undergrad\NewFormat as BaseParser;

class BEngBiomedFormat extends BaseParser
{

    public static function identifyParser($text, $details = [])
    {
        return preg_match('/^(BEng\s+Biomedical\s+Engineering)/s', $text);
    }

}
