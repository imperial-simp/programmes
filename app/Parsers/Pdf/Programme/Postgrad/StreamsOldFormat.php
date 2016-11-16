<?php

namespace Imperial\Simp\Parsers\Pdf\Programme\Postgrad;

use Imperial\Simp\Parsers\Pdf\Programme\Postgrad\OldFormat as BaseParser;

class StreamsOldFormat extends BaseParser
{
    public static function identifyParser($text, $details = [])
    {
        if (parent::identifyParser($text) && preg_match('/(Indicative Module List[^\n]+Stream)/s', $text)) {
            throw new \Exception('Streams not implemented yet.');
        }

        return false;
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
            '(^.*Stream)[^\n]*\n.*Module Weighting', //TODO
            '(?:Indicative )?Module List',
            'Supporting Information',
        ];
    }

    public function readModuleWeightingSection(array $lines = [])
    {

        return $lines; //TODO
    }

    public function readIndicativeModuleListSection(array $lines = [])
    {
        return $lines; //TODO
    }


}
