<?php

namespace Imperial\Simp\Parsers\Pdf\Programme\Undergrad;

use Imperial\Simp\Parsers\Pdf\Programme\Undergrad\OldFormat as BaseParser;

class BEngBiomedFormat extends BaseParser
{

    public static function identifyParser($text, $details = [])
    {
        return preg_match('/^(BEng Biomedical Engineering)/s', $text);
    }

}
