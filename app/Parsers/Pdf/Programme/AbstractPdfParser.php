<?php

namespace Imperial\Simp\Parsers\Pdf\Programme;

use Imperial\Simp\Parsers\AbstractPdfParser as BaseParser;

abstract class AbstractPdfParser extends BaseParser
{
    public function read()
    {
        $this->getDocumentTitle();

        $this->lines = $this->splitSections($this->lines, $this->getSections());

        foreach ($this->lines as $heading => &$lines) {

            if ($methodHeading = ucfirst(str_replace('_', '', $heading))) {

                $method = 'get'.$methodHeading.'Headings';

                if (method_exists($this, $method)) {
                    $sectionHeadings = $this->$method($lines);
                    $lines = $this->readSection($lines, $sectionHeadings, $heading);
                }

                $method = 'read'.$methodHeading.'Section';

                if (method_exists($this, $method)) {
                    $lines = $this->$method($lines);
                }
            }
        }

        return $this;
    }

    protected function getDocumentTitle()
    {
        if (!count($this->documentTitle)) {
            foreach ($this->lines as $i => &$line) {
                if ($i >= 5) {
                    break;
                }
                if (preg_match('/^[BM](Eng|Sc ?i?).*/', $line)) {
                    $this->documentTitle[] = trim($line);
                    $line = null;
                }
            }
        }
    }

    protected function splitSections(array $lines, array $sectionHeadings)
    {
        $sections = [];
        $lastHeading = null;

        $sectionHeading = array_shift($sectionHeadings);

        foreach ($lines as $i => $line) {
            $line = trim($line);

            if ($line) {

                if ($sectionHeading && preg_match('#^('.$sectionHeading.')(.*)$#i', $line, $matches)) {

                    if ($matches[2]) {
                        $sections[$this->slug($sectionHeading)][] = $matches[2];
                    }

                    $lastHeading = $sectionHeading;
                    $sectionHeading = array_shift($sectionHeadings);
                }
                else {
                    $sections[$this->slug($lastHeading)][] = $line;
                }
            }
        }

        if(empty(@$sections[$this->slug($sectionHeading)])) {
            array_unshift($sectionHeadings, $this->slug($sectionHeading));
        }

        $sectionHeadings = array_values(array_filter($sectionHeadings));

        if (count($sectionHeadings)) {
            $this->reportMissing('Sections', $sectionHeadings);
        }

        return $sections;
    }

    public function readSection(array $lines, array $sectionFields, $heading = null)
    {
        $fields = [];
        $lastField = null;

        $sectionField = array_shift($sectionFields);

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line) {

                if ($sectionField && preg_match('#^('.$sectionField.')$#i', $line)) {

                    $lastField = $sectionField;
                    $sectionField = array_shift($sectionFields);
                }
                elseif ($sectionField && preg_match('#^('.$sectionField.')(.+)$#i', $line, $matches)) {

                    $fields[$this->slug($sectionField)][] = $this->transformValue(trim($matches[2]), $heading, $this->slug($sectionField));
                    $lastField = $sectionField;
                    $sectionField = array_shift($sectionFields);

                }
                else {
                    if ($lastField) {
                        $fields[$this->slug($lastField)][] = $this->transformValue($line, $heading, $this->slug($lastField));
                    }
                    else {
                        $fields[] = $line;
                    }
                }

            }
        }

        foreach ($fields as $field => &$values) {

            if ($fieldHeading = ucfirst(str_replace('_', '', $field))) {
                $method = 'read'.$fieldHeading.'Field';

                if (method_exists($this, $method)) {
                    $values = $this->$method($values);
                    continue;
                }
            }

            if (is_array($values)) {
                $values = array_flatten($values);
                if (count($values) == 1) {
                    $values = head($values);
                }
            }
        }

        $this->reportMissing($heading, $sectionFields);

        return $fields;
    }

    protected function transformValue($value, $heading, $section)
    {
        $method = 'read'.$heading.'_'.$section;

        if (method_exists($this, $method)) {
            return $this->$method($value);
        }

        return $value;
    }

    public static function identifyParser($text, $details = [])
    {
        return preg_match('/^Programme Specification/', $text);
    }

    public function tidyText($text)
    {
        $replacements = [
            '/ at ?:\s*(http:|www)/' => ' at: $1',
            '/Relevant QAA Benchmark Statement\(s\) and\/or\s*\n?other external reference points/' => 'Relevant QAA Benchmark Statement(s)',
            '/pro gramme/i' => 'programme',
            '/under graduate/i' => 'undergraduate',
            '/Student cohorts cov ered by specification/' => 'Student cohorts covered by specification',
            '/^This document provides a definitive record.*$/m' => '',
            '/^((?:Year (?:One|Two|Three|Four|Five|Six|\d))|(?:First|1st|Second|2nd|Third|3rd|Fourth|Fifth|Sixth|[4-6]th|Final) Year) ([\d\.]+%) /m' => '$1 $2'.PHP_EOL,
        ];

        if (isset($this->tidyReplacements)) {
            $replacements = array_merge($replacements, $this->tidyReplacements);
        }

        $text = preg_replace(array_keys($replacements), array_values($replacements), $text);

        $headerReplacements = [
            'Code Title Core/ Elective Year L&T Hours Ind. Study Hours Place-? ment Hours Total Hours % Written Exam % Course-? work % Practical FHEQ Level ECTS' =>
            'Module Table Header',
            'Qualifications Framework of the European Higher Education Area' => 'Qualifications Framework of the European Higher Education Area',
            'Year % Year Weighting Total Marks Available Module Total Marks Available' => 'Module Total Marks Weighting',
            'Year % Year Weighting Module % Module Weighting' => 'Module Weighting',
            'Module % Module Weighting' => 'Module Weighting',
            'The (?:programme\'s )?competency standards .* at:' => 'Competency Standards',
            '(?:Re-sit Policy )?The College\'s Policy on Re-sits is available at:' => 'Resit Policy',
            '(?:Mitigating Circumstances Policy )?The College\'s Policy on Mitigating Circumstances is available at:' => PHP_EOL.'Mitigating Circumstances Policy',
        ];

        foreach ($headerReplacements as $headerFind => $headerReplace) {
            $headerFind = str_replace(' ', '\s*', $headerFind);
            $text = preg_replace('#'.$headerFind.'#m', $headerReplace, $text);
        }

        $text = trim($text);

        return $text;
    }

    abstract function getSections();

}
