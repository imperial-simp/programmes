<?php

namespace Imperial\Simp\Parsers\Programme\Undergrad\NewFormat;

use Imperial\Simp\Parsers\Programme\Undergrad\OldFormat\Parser as BaseParser;

class Parser extends BaseParser
{
    public function read()
    {
        return $this;
    }

    public static function identifyParser($text, $details = [])
    {
        return parent::identifyParser($text) && preg_match('/(Assessment Dates & Deadlines).*(Indicative Module List)/s', $text);
    }

    public function getSections()
    {
        return [
            'Programme Information',
            'Specification Details',
            'Description of Programme Contents',
            'Learning Outcomes',
            'Entry Requirements',
            'Learning & Teaching Strategy',
            'Assessment Strategy',
            'Programme Structure',
            'Assessment Dates & Deadlines',
            'Assessment Structure.*',
            'Indicative Module List',
            'Supporting Information',
        ];
    }

}
