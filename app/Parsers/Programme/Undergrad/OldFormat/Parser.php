<?php

namespace Imperial\Simp\Parsers\Programme\Undergrad\OldFormat;

use Imperial\Simp\Parsers\Programme\AbstractPdfParser as BaseParser;

class Parser extends BaseParser
{
    public function read()
    {
        return $this;
    }

    public static function identifyParser($text, $details = [])
    {
        return preg_match('/^Programme Specification \(Undergraduate\)/', $text);
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
            'Assessment Structure',
            'Module Weighting',
            '(Indicative )?Module List',
            'Supporting Information',
        ];
    }

}
