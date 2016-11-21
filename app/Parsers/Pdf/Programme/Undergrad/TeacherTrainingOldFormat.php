<?php

namespace Imperial\Simp\Parsers\Pdf\Programme\Undergrad;

use Imperial\Simp\Parsers\Pdf\Programme\Undergrad\OldFormat as BaseParser;

class TeacherTrainingOldFormat extends BaseParser
{
    public static function identifyParser($text, $details = [])
    {
        return parent::identifyParser($text) && preg_match('/(.*with Science Education.*)/', $text);
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
            '(?:Indicative )?Module List',
            'Teacher Training Placement Timing',
            'Supporting Information',
        ];
    }

    public function readTeacherTrainingPlacementTimingSection(array $lines = [])
    {
        $this->reportUnknown('Teacher_Training_Placement_Timing', $lines);
        
        return $lines; //TODO
    }

}
