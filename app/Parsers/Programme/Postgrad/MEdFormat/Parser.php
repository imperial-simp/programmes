<?php

namespace Imperial\Simp\Parsers\Programme\Postgrad\MEdFormat;

use Imperial\Simp\Parsers\Programme\Postgrad\NewFormat\Parser as NewFormat;

class Parser extends NewFormat
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