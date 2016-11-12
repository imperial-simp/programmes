<?php

namespace Imperial\Simp\Parsers\Pdf\Programme;

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
            '/ at ?:\s*(http:|www)/' => ' at: $1',
            '/Relevant QAA Benchmark Statement\(s\) and\/or\s*\nother external reference points/' => 'Relevant QAA Benchmark Statement(s)',
            '/pro gramme/i' => 'programme',
            '/under graduate/i' => 'undergraduate',
            '/Student cohorts cov ered by specification/' => 'Student cohorts covered by specification',
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

        $text = trim($text);

        return $text;
    }

    abstract function getSections();

}
