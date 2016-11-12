<?php

namespace Imperial\Simp\Parsers\Pdf\Programme\Undergrad;

use Imperial\Simp\Parsers\Pdf\Programme\AbstractPdfParser as BaseParser;

class OldFormat extends BaseParser
{
    protected $tidyReplacements = [
        '/Programme Specification \(Undergraduate\)/' => '',
    ];

    public static function identifyParser($text, $details = [])
    {
        return preg_match('/^Programme Specification \(Undergraduate\)/', $text);
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
            '(Indicative )?Module List',
            'Supporting Information',
        ];
    }

    public function getProgrammeInformationHeadings()
    {
        return [
            'Award\(s\)',
            'Associateship',
            'Programme Title',
            'Programme code',
            'Awarding Institution',
            'Teaching Institution',
            'Faculty',
            'Department',
            'Mode and Period of Study',
            'Cohort Entry Points',
            'Relevant QAA Benchmark Statement\(s\)',
            'Total Credits',
            'FHEQ Level',
            'EHEA Level',
            'External Accreditor\(s\)',
        ];
    }

    public function getSpecificationDetailsHeadings()
    {
        return [
            'Student cohorts covered by specification',
            'Person responsible for the specification',
            'Date of introduction of programme',
            'Date of programme specification\/revision',
        ];
    }

    public function getEntryRequirementsHeadings()
    {
        return [
            'Academic Requirements?',
            '(?:Non ?-academic|Additional) Requirements?',
            'English Requirements?',
            'Competency Standards',
        ];
    }

    public function getLearningAndTeachingStrategyHeadings()
    {
        return [
            'Scheduled Learning & Teaching Methods',
            'E-learning & Blended Learning Methods',
            'Project and Placement Learning Methods',
        ];
    }

    public function getAssessmentStrategyHeadings()
    {
        return [
            'Assessment Methods',
            'Academic Feedback Policy',
            'Resit Policy',
            'Mitigating Circumstances Policy',
        ];
    }

    public function getAssessmentStructureHeadings()
    {
        return [
            'Marking Scheme',
        ];
    }

    public function readSupportingInformationSection()
    {

    }

    public function readTotalCreditsField($value)
    {
        $value = implode(' ', $value);

        if (preg_match_all('/([^ :]+): ?([^ :]+)/', $value, $matches, PREG_SET_ORDER)) {
            $value = [];

            foreach ($matches as &$match) {
                $value[trim($match[1])] = trim($match[2]);
            }
        }

        return $value;
    }

    protected function readIndicativeModuleListSection($lines)
    {
        $modules = [];
        $unknown = [];

        foreach ($lines as $line) {
            if (!in_array($line, ['Module List', 'Module Table Header'], true)) {

                if ($module = $this->readModule($line)) {
                    $modules[] = $module;
                } else {
                    $unknown[] = $line;
                }

            }
        }

        $unknown = implode(PHP_EOL, $unknown);

        $newModules = $this->readBrokenModules($unknown);

        $modules = array_merge($modules, $newModules);

        $unknown = array_filter(explode(PHP_EOL, $unknown));

        $return = [
            'Modules' => $modules,
        ];

        if (!empty($unknown)) {
            $return['Unknown'] = $unknown;
        }

        return $return;
    }

    public function getModuleRegex()
    {
        return '^
        (?<Code>[^\s]+)\s
        (?<Title>.{5,50}?)\s
        (?<Elective>CORE\*?|ELECTIVE(?:\s\([A-Z]\))|ELECTIVE\*+)\s
        (?<Year>(?:\d|\d(?:\sor\s|/)\d))\s
        (?:(?<Learning_Hours>[\d.]+)\s
        (?<Indiv_Study_Hours>[\d.]+)\s
        (?<Placement_Hours>(?:[\d.]+|See\sbelow))|(?<Various_Hours>Various))\s
        (?<Total_Hours>[\d.]+)\s
        (?:(?<Written_Exam>[\d.]+%?)\s
        (?<Coursework>[\d.]+%?)\s
        (?<Practical>[\d.]+%?)\s
        (?<FHEQ>\d)|(?<Various_Assessment>Various))\s
        (?<ECTS>[\d.]+)
        $';
    }

    protected function readModule($line)
    {
        $regex = '@'.$this->getModuleRegex().'@x';

        if (preg_match($regex, $line, $matches)) {
            return $this->getModuleKeys($matches);
        }
    }

    protected function readBrokenModules(&$text)
    {
        $regex = '@'.$this->getModuleRegex().'@smx';
        $modules = [];

        if (preg_match_all($regex, $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $match = $this->getModuleKeys($match);
                $match = array_map(function($item) {
                    return trim(preg_replace('/\s+/', ' ', $item));
                }, $match);
                $modules[] = $match;
            }

            $text = preg_replace($regex, null, $text);
            $text = trim(preg_replace('/\n+/', PHP_EOL, $text));
        }

        return $modules;
    }

    protected function getModuleKeys(array $array)
    {
        $fields = array_filter(array_keys($array), 'is_string');

        $fields = array_only($array, $fields);

        if (@$fields['Various_Hours'] == 'Various') {
            $fields['Learning_Hours'] = '(various)';
            $fields['Placement_Hours'] = '(various)';
            $fields['Indiv_Study_Hours'] = '(various)';
        }

        if (@$fields['Various_Assessment'] == 'Various') {
            $fields['Written_Exam'] = '(various)';
            $fields['Coursework'] = '(various)';
            $fields['Practical'] = '(various)';
            $fields['FHEQ'] = '(various)';
        }

        if (preg_match('/^(.*)(\*+)$/', $fields['Elective'], $matches)) {
            $fields['Elective'] = $matches[1];
            $fields['Note'] = $matches[2];
        }

        unset($fields['Various_Hours']);
        unset($fields['Various_Assessment']);

        return $fields;
    }

    public function getModuleWeightingRegex()
    {
        return '/^(?<module>(?:[A-Z]|[0-9]+ (?:day|hour|week|month))[^%]+?)(?<weighting>[\d\.]+ ?r? ?%) ?$/sm';
    }

    public function getModuleWeightingYearRegex()
    {
        return '/^(?<year>(?:Year (?:One|Two|Three|Four|Five|Six|\d))|(?:First|1st|Second|2nd|Third|3rd|Fourth|Fifth|Sixth|[4-6]th|Final) Year) (?<weighting>[\d\.]+ ?%)$/';
    }

    protected function readModuleWeightingSection($lines)
    {
        $weightings = [];
        $buffer = [];
        $moduleBuffer = [];
        $unknown = [];

        $lastYear = null;

        $yearRegex = $this->getModuleWeightingYearRegex();
        $moduleRegex = $this->getModuleWeightingRegex();

        foreach ($lines as $line) {
            if (preg_match($yearRegex, $line, $matches)) {

                if ($lastYear) {
                    $weightings[$lastYear]['modules'] = $buffer;
                    $buffer = [];
                }

                $lastYear = $matches['year'];
                $weightings[$lastYear]['weighting'] = $matches['weighting'];
            }
            else {
                $buffer[] = $line;
            }
        }

        if ($lastYear && !empty($buffer)) {
            $weightings[$lastYear]['modules'] = $buffer;
        }

        foreach ($weightings as $yr => &$year) {
            $modules = implode(PHP_EOL, $year['modules']);
            $year['modules'] = [];

            if (preg_match_all($moduleRegex, $modules, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $year['modules'][] = [
                      'module' => trim(preg_replace('/\s+/', ' ', $match['module'])),
                      'weighting' => $match['weighting'],
                    ];
                }
            }

            $unknown[$yr] = preg_replace($moduleRegex, '', $modules);

        }

        unset($year);

        foreach ($unknown as $year => &$modules) {
            $modules = trim($modules);
        }

        unset($modules);

        $unknown = array_filter($unknown);

        $return = [
            'Weightings' => $weightings,
        ];

        if (count($unknown)) {
            $return['Unknown'] = $unknown;
        }

        return $return;
    }

}
