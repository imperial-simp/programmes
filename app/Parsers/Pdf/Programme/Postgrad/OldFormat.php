<?php

namespace Imperial\Simp\Parsers\Pdf\Programme\Postgrad;

use Imperial\Simp\Parsers\Pdf\Programme\Undergrad\OldFormat as BaseParser;

class OldFormat extends BaseParser
{
    protected $tidyReplacements = [
        '/Programme Specification \(Master\'s Level\)/' => '',
    ];

    public static function identifyParser($text, $details = [])
    {
        return preg_match('/^Programme Specification \(Master/', $text);
    }

    public function getModuleRegex()
    {
        return '^
        (?<Code>(?:[A-Z]{3,}\s[0-9]{3,}|[^\s]+))\s+
        (?<Title>[^%]{5,100}?)\s+
        (?<Elective>CORE\**|ELECTIVE(?:\s\([^)]+\))|ELECTIVE\**)\s+
        (?:(?<Learning_Hours>[\d.]+)\s+
        (?<Indiv_Study_Hours>[\d.]+)\s+
        (?<Placement_Hours>(?:[\d.]+|See\sbelow))|(?<Various_Hours>Various|Variable))\s+
        (?<Total_Hours>[\d.]+)\s+
        (?:(?:(?<Written_Exam>[\d.]+%?)\s+
        (?<Coursework>[\d.]+%?)\s+
        (?<Practical>[\d.]+%?)|(?<Various_Assessment>Various|Variable))\s+
        (?<FHEQ>\d)|(?<Various_Assessment_FHEQ>Various|Variable))\s+
        (?<ECTS>[\d.]+)
        $';
    }

    public function getVariousAllModuleRegex()
    {
        return '^
        (?<Code>(?:[A-Z]{3,}\s[0-9]{3,}|[^\s]+))\s+
        (?<Title>(?:[^%]{5,100}?)|IDX)\s
        (?<Elective>CORE\**|ELECTIVE(?:\s\([^)]+\))|ELECTIVE\**)\s+
        (?<Various_All>Various|Variable)\s+
        (?<FHEQ>\d)\s+
        (?<ECTS>[\d.]+)
        $';
    }

    public function getNotAssessedModuleRegex()
    {
        return '^
        (?<Code>(?:[A-Z]{3,}\s[0-9]{3,}|[^\s]+))\s+
        (?<Title>(?:[^%]{5,100}?)|IDX)\s
        (?<Elective>CORE\**|ELECTIVE(?:\s\([^)]+\))|ELECTIVE\**)\s+
        (?:(?<Learning_Hours>[\d.]+)\s+
        (?<Indiv_Study_Hours>[\d.]+)\s+
        (?<Placement_Hours>(?:[\d.]+|See\sbelow))|(?<Various_Hours>Various))\s+
        (?<Total_Hours>[\d.]+)\s+
        (?<Not_Assessed>(?:Not\sassessed|N/A))
        (?:\s+N/A|\s+0(?:\.00)?)*
        $';
    }
}
