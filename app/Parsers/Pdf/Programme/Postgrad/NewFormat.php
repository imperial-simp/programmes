<?php

namespace Imperial\Simp\Parsers\Pdf\Programme\Postgrad;

use Imperial\Simp\Parsers\Pdf\Programme\Undergrad\NewFormat as BaseParser;

class NewFormat extends BaseParser
{

    public static function identifyParser($text, $details = [])
    {
        return preg_match('/^Programme Specification \(Master.*(Programme Structure).*(Assessment Dates & Deadlines)/s', $text);
    }

}
