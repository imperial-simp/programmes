<?php

namespace Imperial\Simp\Parsers\Pdf\Programme\Undergrad;

use Imperial\Simp\Parsers\Pdf\Programme\Undergrad\OldFormat as BaseParser;

class MultiProgrammeFormat extends BaseParser
{

    public static function identifyParser($text, $details = [])
    {
        return parent::identifyParser($text) && preg_match('/(.*Programme +Title +Programme +Code.*)/s', $text);
    }

    public function getProgrammeInformationHeadings(array $lines = []) {

        $headings = parent::getProgrammeInformationHeadings($lines);

        foreach ($headings as &$heading) {
            if ($heading == 'Programme Title') {
                $heading = 'Programme Title Programme Code';
                break;
            }
        }

        unset($heading);

        $headings = array_values(array_filter($headings));

        if (isset($this->errors['Missing']['Headings'])) {
            $errors = $this->errors;
            foreach ($errors['Missing']['Headings'] as &$heading) {
                if ($heading == 'Programme code') {
                    $heading = null;
                    break;
                }
            }

            unset($heading);

            $errors['Missing']['Headings'] = array_filter($errors['Missing']['Headings']);
            $errors['Missing'] = array_filter($errors['Missing']);
            $errors = array_filter($errors);

            $this->errors = $errors;
        }

        return $headings;
    }

    public function readProgrammeTitleProgrammeCodeField($text)
    {
        $programmes = [];
        $unknown = [];

        foreach ($text as $programme) {
            if (preg_match('/^(?<Title>.+) (?<Code>[^ ]+)$/', $programme, $match)) {
                $programmes[] = $this->stringKeys($match);
            }
            else {
                $programmes[] = $programme;
            }
        }

        return $programmes;
    }

}
