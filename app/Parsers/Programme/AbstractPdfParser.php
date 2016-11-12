<?php

namespace Imperial\Simp\Parsers\Programme;

use Imperial\Simp\Parsers\AbstractPdfParser as BaseParser;

abstract class AbstractPdfParser extends BaseParser
{
    public function read()
    {
        // TODO
        return $this;
    }

    public static function identifyParser($text, $details = [])
    {
        return preg_match('/^Programme Specification/', $text);
    }

    public function tidyText($text)
    {
        $replacements = [
            '/ \./' => '.',
            '/^\d*Page +\d+ +of +\d+\s*$/m' => PHP_EOL,
            '/(?<!^)(\x{2022})/uim' => PHP_EOL.'$1',
            '/(\x{2022} .*)\n([a-z])/um' => '$1 $2',
            '/\x{2019}/u' => '\'',
            '/\x{2013}/u' => '-',
            '/ {2,}/' => ' ',
            '/ ([,-])/' => '$1',
            '/ at ?:\s*(http:|www)/' => ' at: $1',
            '/Relevant QAA Benchmark Statement\(s\) and\/or\s*\nother external reference points/' => 'Relevant QAA Benchmark Statement(s)',
            '/\n([a-z])/' => '$1',
            '/pro gramme/i' => 'programme',
            '/under graduate/i' => 'undergraduate',
            '/Student cohorts cov ered by specification/' => 'Student cohorts covered by specification',
            '/ (and|in|or|of|to|with|at|the|an?|for|including|by|using|according|prior|ensure|within|if|do|did|is|has) ?$\n/m' => ' $1 ',
            '/\n^(\([A-Z]+\))/m' => '$1',
            '/(([A-Z])[a-z]+)\s+(([A-Z])[a-z]+) \(([A-Z]*\2\4)\)/m' => '$1 $3 ($5)',
            '/^This document provides a definitive record.*$/m' => '',
            '/^((?:Year (?:One|Two|Three|Four|Five|Six|\d))|(?:First|1st|Second|2nd|Third|3rd|Fourth|Fifth|Sixth|[4-6]th|Final) Year) ([\d\.]+%) /m' => '$1 $2'.PHP_EOL,
        ];

        $text = preg_replace(array_keys($replacements), array_values($replacements), $text);

        $headerReplacements = [
            'Code Title Core/ Elective Year L&T Hours Ind. Study Hours Place-? ment Hours Total Hours % Written Exam % Course-? work % Practical FHEQ Level ECTS' =>
            'Module Table Header',
            'Qualifications Framework of the European Higher Education Area' => 'Qualifications Framework of the European Higher Education Area',
            'Year % Year Weighting Module % Module Weighting' => 'Module Weighting',
            'Module % Module Weighting' => 'Module Weighting',
        ];

        foreach ($headerReplacements as $headerFind => $headerReplace) {
            $text = preg_replace('#'.str_replace(' ', '\s*', $headerFind).'#m', $headerReplace, $text);
        }

        return $text;
    }

    abstract function getSections();

}
