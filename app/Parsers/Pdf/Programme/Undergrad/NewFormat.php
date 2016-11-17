<?php

namespace Imperial\Simp\Parsers\Pdf\Programme\Undergrad;

use Imperial\Simp\Parsers\Pdf\Programme\Undergrad\OldFormat as BaseParser;

class NewFormat extends BaseParser
{
    protected $tidyReplacements = [
        '/Programme Specification \(Master\'s Level\)/' => '',
        // '/Programme Component ECTS % Weighting/' => 'Programme Component Weightings',
    ];

    public static function identifyParser($text, $details = [])
    {
        return parent::identifyParser($text) && preg_match('/(Programme Structure).*(Assessment Dates & Deadlines)/s', $text);
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
            'Programme Structure',
            'Assessment Dates & Deadlines',
            'Assessment Structure',
            'Indicative Module List',
            'Supporting Information',
        ];
    }

    public function getProgrammeStructureStartHeadings()
    {
        return [
            '(Year (?:One|1)|(?:Fir|1)st Year)',
            '(Year (?:Two|2)|(?:Seco|2)nd Year)',
            '(Year (?:Three|3)|(?:Thi|3)rd Year)',
            '(Year (?:Four|4)|(?:Four|4)th Year)',
            '(Year (?:Five|5)|(?:Fif|5)th Year)',
            '(Year (?:Six|6)|(?:Six|6)th Year)',
        ];
    }

    public function getAssessmentStructureHeadings(array $lines = [])
    {
        return [
            'Programme Component ECTS % Weighting',
            'Marking Scheme',
        ];
    }

    public function getComponentWeightingRegex()
    {
        return '^
        (?<Component>.*?)
        (?<ECTS>[\d.]+)\s
        (?<Weighting>[\d.]+ ?%)
        $';
    }

    public function readProgrammeComponentECTSWeightingField(array $lines = [])
    {
        $lines = implode(PHP_EOL, $lines);

        //TODO $this->getComponentWeightingRegex();

        return $components;
    }

    public function getProgrammeStructureYearHeadings(array $lines = [])
    {
        $lines = implode(PHP_EOL, $lines);

        $startHeadings = $this->getProgrammeStructureStartHeadings();

        $headings = [];

        foreach ($startHeadings as $heading) {
            if (preg_match('/^'.$heading.'/m', $lines, $match)) {
                $headings[] = $match[1];
            }
        }

        return $headings;
    }

    public function readProgrammeStructureSection(array $lines = [])
    {
        $string = $lines;

        $findLines = str_replace(' ', '\s+', implode(' ', $lines));

        if (preg_match('/'.$findLines.'/', $this->rawText, $match)) {
            $lines = explode(PHP_EOL, $match[0]);
        }

        $sectionFields = $this->getProgrammeStructureYearHeadings($lines);

        if (count($sectionFields)) {
            $lines = $this->readSection($lines, $sectionFields, 'Programme_Structure');
        }
        else {
            $lines['Year_One'] = $lines;
        }

        $table = [];

        foreach ($lines as $year => &$yearLines) {

        $yearLines = implode(PHP_EOL, $yearLines);

        $yearLines = preg_replace('/(Spring|Summer|Autumn|Winter)\s+(Term|Period|Session)/mx', '$1_$2', $yearLines);
        $yearLines = preg_replace('/(Term|Period|Session)\s+(One|Two|Three|Four|Five|Six|Seven|Eight\d+)/mx', '$1_$2', $yearLines);
        $yearLines = preg_replace('/([^\s]+)\s(Module)/mx', '$1_$2', $yearLines);

        $yearLines = explode(PHP_EOL, $yearLines);

        $headers = [];
        $values = [];

        foreach ($yearLines as $line) {
            if (preg_match('/(Term|Period|Session|Pre-session)/', $line)) {
                $headers[] = $line;
            }
            else {
                $values[] = $line;
            }
        }

        $limit = count($headers);

        foreach ($values as $line) {
            $rowHeader = strstr($line.' ', ' ', true);
            $rowValues = substr(strstr($line, ' '), 1);
            $rowValues = preg_split('/ /', $rowValues, $limit);

            $line = array_combine($headers, array_pad($rowValues, $limit, null));
            $table[$year][$rowHeader][] = $line;
        }

        unset($line);

        }

        $return = [
            'Table'   => $table,
            '_String' => $string,
        ];

        if (!count($table)) {
            $this->reportMissing('Programme_Structure', ['Table']);
        }

        return $return;
    }

    public function getAssessmentDatesAndDeadlinesHeadings(array $lines = [])
    {
        return [
            'Written Examinations',
            'Coursework Assessments',
            'Project Deadlines',
            'Practical Assessments',
        ];
    }

}
