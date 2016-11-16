<?php

namespace Imperial\Simp\Parsers\Pdf\Programme\Undergrad;

use Imperial\Simp\Parsers\Pdf\Programme\Undergrad\OldFormat as BaseParser;

class StreamsOldFormat extends BaseParser
{
    public static function identifyParser($text, $details = [])
    {
        if (parent::identifyParser($text) && preg_match('/(Indicative Module List[^\n]+Stream)/s', $text)) {
            throw new \Exception('Streams not implemented yet.');
        }

        return false;
    }
}
