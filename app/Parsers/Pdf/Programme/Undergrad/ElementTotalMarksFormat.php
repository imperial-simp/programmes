<?php

namespace Imperial\Simp\Parsers\Pdf\Programme\Undergrad;

use Imperial\Simp\Parsers\Pdf\Programme\Undergrad\TotalMarksFormat as BaseParser;

class ElementTotalMarksFormat extends BaseParser
{
    public static function identifyParser($text, $details = [])
    {
        return preg_match('/^Programme Specification \(Undergraduate\).*(Year\s+Weighting)/s', $text) && preg_match('@((Element\s+Module|Assessment\s+Element ?/ ?Module)\s+Mark\s+Weighting\s+Total\s+Marks)@ms', $text);
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
            'Module Element Total Marks Weighting',
            '(?:Indicative )?Module List',
            'Supporting Information',
        ];
    }

    protected function readModuleElementTotalMarksWeightingSection(array $lines = [])
    {
        throw new \Exception('"Module Element Total Marks Weighting" not implemented yet.');
        return $this->readModuleWeightingSection($lines);
    }

    public function getModuleWeightingYearRegex()
    {
        return '/^(?<Year>(?:Year (?:One|Two|Three|Four|Five|Six|\d))|(?:First|1st|Second|2nd|Third|3rd|Fourth|Fifth|Sixth|[4-6]th|Final) Year)\s+(?<Weighting>[\d\.]+ ?%)\s+(?<Element>Examination|Coursework)(?<Rest>.*)$/';
    }
}
