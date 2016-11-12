<?php

namespace App\Parsers\Programme\Undergrad\OldFormat;

use App\Parsers\Programme\Parser as ProgrammeParser;

class Parser extends ProgrammeParser
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