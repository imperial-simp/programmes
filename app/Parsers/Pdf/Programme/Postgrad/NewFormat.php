<?php

namespace Imperial\Simp\Parsers\Pdf\Programme\Postgrad;

use Imperial\Simp\Parsers\Pdf\Programme\Undergrad\NewFormat as BaseParser;

class NewFormat extends BaseParser
{
    public function read()
    {
        return $this;
    }

    public static function identifyParser($text, $details = [])
    {
        return parent::identifyParser($text) && preg_match('/(Assessment Dates & Deadlines).*(Indicative Module List)/s', $text);
    }

}
