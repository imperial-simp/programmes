<?php

namespace Imperial\Simp\Parsers\Pdf\Programme\Undergrad;

use Imperial\Simp\Parsers\Pdf\Programme\Undergrad\OldFormat as BaseParser;

class NewFormat extends BaseParser
{

    public static function identifyParser($text, $details = [])
    {
        return parent::identifyParser($text) && preg_match('/(Programme Structure).*(Assessment Dates & Deadlines)/s', $text);
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
