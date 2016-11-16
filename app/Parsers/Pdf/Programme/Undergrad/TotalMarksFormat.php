<?php

namespace Imperial\Simp\Parsers\Pdf\Programme\Undergrad;

use Imperial\Simp\Parsers\Pdf\Programme\Undergrad\OldFormat as BaseParser;

class TotalMarksFormat extends BaseParser
{
    public static function identifyParser($text, $details = [])
    {
        return parent::identifyParser($text, $details) && preg_match('/(Total\s+Marks\s+Available\s+Module\s+Total\s+Marks\s+Available)/s', $text);
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
            'Module Total Marks Weighting',
            '(?:Indicative )?Module List',
            'Supporting Information',
        ];
    }

    protected function readModuleTotalMarksWeightingSection(array $lines = [])
    {
        return $this->readModuleWeightingSection($lines);
    }

    public function getModuleWeightingRegex()
    {
        return '@^
        (?<Module>
        (?:(?<Count>(?:[0-9]+|one|t(?:wo|hree)|f(?:our|ive)|s(?:ix|even)|eight|nine|ten))
        (?:\s[x×])?\s(?<Unit>day|hour|week|month|module)s?)?
        (?<Options>[^%]+?)
        )
        (?<Total_Marks>\d+)
        (?<Each>\s*(?:each)?)
        (?<Note>\**)
        $@ismx';
    }

    public function getModuleWeightingYearRegex()
    {
        return '/^(?<Year>(?:Year (?:One|Two|Three|Four|Five|Six|\d))|(?:First|1st|Second|2nd|Third|3rd|Fourth|Fifth|Sixth|[4-6]th|Final) Year)\s+(?<Weighting>[\d\.]+ ?%)\s+(?<Total_Marks>\d+)(?<Rest>.*)$/';
    }
}
