<?php

namespace Imperial\Simp\Parsers\Pdf\Module\Project;

use Imperial\Simp\Parsers\Pdf\Module\Module\NewFormat as BaseParser;

class NewFormat extends BaseParser
{
    public static function identifyParser($text, $details = [])
    {
        return preg_match('/^Project Outline/', $text);
    }

}
