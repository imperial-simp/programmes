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
        return preg_match('/^Programme Specification \(Undergraduate\).*(Year\s+Weighting)/s', $text);
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
            'Supporting Information',
        ];
    }

    public function getProgrammeInformationHeadings(array $lines = [])
    {
        $headings = [
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

        return $this->sortHeadings($headings, $lines);
    }

    protected function sortHeadings(array $headings, array $lines)
    {
        $order = [];

        foreach ($lines as $ln => $line) {
            foreach ($headings as $heading) {
                if (preg_match('#^('.$heading.').*#', $line)) {
                    $order[$heading] = $ln;
                }
            }
        }

        asort($order);

        return array_keys($order);

    }

    public function getSpecificationDetailsHeadings(array $lines = [])
    {
        return [
            'Student cohorts covered by specification',
            'Person responsible for the specification',
            'Date of introduction of programme',
            'Date of programme specification\/revision',
        ];
    }

    public function getEntryRequirementsHeadings(array $lines = [])
    {
        return [
            'Academic Requirements?',
            '(?:Non ?-academic|Additional) Requirements?',
            'English (?:Language )?Requirements?',
            'Competency Standards',
        ];
    }

    public function getLearningAndTeachingStrategyHeadings(array $lines = [])
    {
        return [
            'Scheduled Learning & Teaching Methods',
            'E-learning & Blended Learning Methods',
            'Project and Placement Learning Methods',
        ];
    }

    public function getAssessmentStrategyHeadings(array $lines = [])
    {
        return [
            'Assessment Methods',
            'Academic Feedback Policy',
            'Re-?sits? Policy',
            'Mitigating Circumstances Policy',
        ];
    }

    public function getAssessmentStructureHeadings(array $lines = [])
    {
        return [
            'Marking Scheme',
        ];
    }

    public function readSupportingInformationSection(array $lines = [])
    {

        $info = [];
        $unknown = [];

        foreach ($lines as &$line) {
            $line = trim($line);

            if (preg_match('/( (at|see): )?(http:|www\.)/', $line)) {
                $line = preg_replace('/^The ((College|programme)\'s |programme is consistent with the )?/', '', $line);
                $line = preg_replace('/((which )?(can be found|is( available)?) at| see):/', '__AT__', $line);

                $line = explode('__AT__', $line);

                $line = array_map('trim', $line);

                 if (count($line) == 2) {
                    $info[$this->slug($line[0])] = $line[1];
                }
                else {
                    $info[] = implode(PHP_EOL, $line);
                }

            }
            else {
                $unknown[] = $line;
            }
        }

        $return = [
            'Links' => $info,
        ];

        if (count($unknown)) {
            $this->reportUnknown('Supporting_Information', $unknown);
            $return['Other'] = $unknown;
        }

        return $return;
    }

    public function readTotalCreditsField($value)
    {
        $value = implode(' ', $value);
        $value = str_replace('UK Credit', 'UK_Credit', $value);

        $credits = [];

        if (preg_match_all('/([^ :]+): ?([^ :]+)/', $value, $matches, PREG_SET_ORDER)) {

            foreach ($matches as &$match) {
                $value = str_replace($match[0], null, $value);
                $credits[trim($match[1])] = $this->splitOr(trim($match[2]));
            }
        }

        $value = trim($value);

        if ($value) {
            $credits['Unknown'] = $value;
        }

        return $credits;
    }

    protected function readIndicativeModuleListSection(array $lines = [])
    {
        $modules = [];
        $unknown = [];

        $lines = implode(PHP_EOL, $lines);

        $lines = preg_replace('/\n([\.\d\s]+)\n/m', ' $1 ', $lines);
        $lines = preg_replace('/\n *(\w+) *\n/m', ' $1 ', $lines);

        $lines = explode(PHP_EOL, $lines);

        foreach ($lines as $i => &$line) {
            if (!in_array($line, ['Module List', 'Module Table Header', 'Indicative Module List'], true)) {

                if (isset($lines[$i+1]) && !$this->readModule($line) && $module = $this->readModule($line.PHP_EOL.$lines[$i+1])) {
                    $modules[] = $module;
                    $lines[$i+1] = null;
                }
                elseif ($module = $this->readModule($line)) {
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

        $notes = [];

        foreach ($unknown as $id => &$line) {
            if (preg_match('/^(\*+)(.*)$/', $line, $matches)) {
                $notes[] = [
                    'Marker' => $matches[1],
                    'Note'   => $matches[2],
                ];
                $line = null;
            }
        }

        $unknown = array_values(array_filter($unknown));

        $return = [
            'Modules' => $modules,
        ];

        if (count($unknown)) {
            $this->reportUnknown('Indicative_Module_List', $unknown);
            $return['Unknown'] = $unknown;
        }

        if (!empty($notes)) {
            $return['Notes'] = $notes;
        }

        return $return;
    }

    public function getModuleRegex()
    {
        return '^
        (?<Code>[^\s]+)\s
        (?<Title>.{5,80}?)\s
        (?<Elective>CORE\*?|ELECTIVE(?:\s\([^)]+\))|ELECTIVE\**)\s
        (?<Year>(?:\d|\d(?:\sor\s|/)\d))\s
        (?:(?<Learning_Hours>[\d.]+)\s
        (?<Indiv_Study_Hours>[\d.]+)\s
        (?<Placement_Hours>(?:[\d.]+|See\sbelow))|(?<Various_Hours>Various))\s
        (?<Total_Hours>[\d.]+)\s
        (?:(?:(?<Written_Exam>[\d.]+%?)\s
        (?<Coursework>[\d.]+%?)\s
        (?<Practical>[\d.]+%?)|(?<Various_Assessment>Various))\s
        (?<FHEQ>\d)|(?<Various_Assessment_FHEQ>Various))\s
        (?<ECTS>[\d.]+)
        $';
    }

    protected function readModule($line)
    {
        $regex = '@'.$this->getModuleRegex().'@smx';

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
                $text = str_replace($match[0], null, $text);
                $match = $this->getModuleKeys($match);
                $match = array_map(function($item) {
                    return trim(preg_replace('/\s+/', ' ', $item));
                }, $match);
                $modules[] = $match;
            }

            $text = trim(preg_replace('/\n+/', PHP_EOL, $text));
        }

        return $modules;
    }

    protected function getModuleKeys(array $array)
    {
        $fields = array_filter(array_keys($array), 'is_string');

        $fields = array_only($array, $fields);

        foreach ($fields as &$value) {
            $value = is_string($value) ? trim(preg_replace('/\s+/', ' ', $value)) : $value;
        }

        if (@$fields['Various_Hours'] == 'Various') {
            $fields['Learning_Hours'] = '(various)';
            $fields['Placement_Hours'] = '(various)';
            $fields['Indiv_Study_Hours'] = '(various)';
        }

        if (@$fields['Various_Assessment'] == 'Various') {
            $fields['Written_Exam'] = '(various)';
            $fields['Coursework'] = '(various)';
            $fields['Practical'] = '(various)';
        }

        if (@$fields['Various_Assessment_FHEQ'] == 'Various') {
            $fields['Written_Exam'] = '(various)';
            $fields['Coursework'] = '(various)';
            $fields['Practical'] = '(various)';
            $fields['FHEQ'] = '(various)';
        }

        if (preg_match('/^(.*)(\*+)$/', $fields['Elective'], $matches)) {
            $fields['Elective'] = $matches[1];
            $fields['Note'] = $matches[2];
        }

        if ($fields['Elective'] == 'CORE') {
            $fields['Core'] = true;
            unset($fields['Elective']);
        }
        elseif (preg_match('/ELECTIVE \((.+)\)/', $fields['Elective'], $match)) {
            $fields['Elective_Group'] = $this->splitOr($match[1]);
            $fields['Elective'] = true;
            $fields['Core'] = false;
        }

        $fields['Year'] = $this->splitOr($fields['Year']);

        unset($fields['Various_Hours']);
        unset($fields['Various_Assessment']);
        unset($fields['Various_Assessment_FHEQ']);

        return $fields;
    }

    public function getModuleWeightingRegex()
    {
        return '/^(?<Module>(?:(?<Count>(?:[0-9]+|[Oo]ne|[Tt](?:wo|hree)|[Ff](?:our|ive)|[Ss](?:ix|even)|[Ee]ight))(?: [x×])? (?<Unit>[Dd]ay|[Hh]our|[Ww]eek|[Mm]onth|[Mm]odule)s?|[A-Z])(?<Options>[^%]+?))(?<Weighting>[\d\.]+ ?r? ?%)(?<Each>\s*(?:each)?) ?$/sm';
    }

    public function getModuleWeightingYearRegex()
    {
        return '/^(?<Year>(?:Year (?:One|Two|Three|Four|Five|Six|\d))|(?:First|1st|Second|2nd|Third|3rd|Fourth|Fifth|Sixth|[4-6]th|Final) Year) (?<Weighting>[\d\.]+ ?%)$/';
    }

    public function getListSeparatorsRegex()
    {
        return '/\s*\b(either|or|and|,|;)\b:?\s*/i';
    }

    protected function readModuleWeightingSection(array $lines = [])
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
                    $weightings[$this->slug($lastYear)]['Modules'] = $buffer;
                    $buffer = [];
                }

                $lastYear = $matches['Year'];
                $weightings[$this->slug($lastYear)]['Weighting'] = $matches['Weighting'];

            }
            else {
                if ($line != 'Module Weighting') {
                    $buffer[] = $line;
                }
            }
        }

        if ($lastYear && !empty($buffer)) {
            $weightings[$this->slug($lastYear)]['Modules'] = $buffer;
        }

        foreach ($weightings as $yr => &$year) {
            $modules = implode(PHP_EOL, $year['Modules']);
            $year['Modules'] = [];

            if (preg_match_all($moduleRegex, $modules, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {

                    $module = [
                      'Module' => trim(preg_replace('/\s+/', ' ', $match['Module'])),
                      'Weighting' => $match['Weighting'],
                    ];

                    if (preg_match('/([\d.]+)r%/', $module['Weighting'], $found)) {
                        $module['Weighting'] = $found[1].'%';
                        $module['Approximate'] = true;
                    }
                    elseif (preg_match('/\.(3{2,}|6{2,})%/', $module['Weighting'])) {
                        $module['Approximate'] = true;
                    }

                    // if (preg_match($this->getListSeparatorsRegex(), $module['Module'])) {
                    //     $module['Options'] = array_values(array_filter(preg_split($this->getListSeparatorsRegex(), $module['Module'])));
                    // } //TODO

                    if ($match['Each']) {
                        $module['Each'] = true;
                    }

                    if ($match['Count']) {
                        $module['Count'] = $this->numberFromWord($match['Count']);
                        $module['Unit'] = ucfirst($match['Unit']);
                        $module['Options'] = trim($match['Options']);

                        if (preg_match('/elective group \((.+)\)/i', $module['Options'], $match)) {
                            $module['Elective_Group'] = $match[1];
                        }
                    }

                    $year['Modules'][] = $module;
                }
            }

            $unknown[$yr] = preg_replace($moduleRegex, '', $modules);

        }

        unset($year);

        foreach ($unknown as $year => &$modules) {
            $modules = array_filter(explode(PHP_EOL, trim($modules)));
        }

        unset($modules);

        $unknown = array_filter($unknown);

        foreach ($unknown as $year => &$lines) {
            $notes = [];

            foreach ($lines as &$line) {
                if (preg_match('/^(\*+)(.*)$/', $line, $matches)) {
                    $notes[$year] = [
                        'Marker' => $matches[1],
                        'Note'   => $matches[2],
                    ];
                    $line = null;
                }
            }

            $lines = array_values(array_filter($lines));

            if (count($lines)) {
                $weightings[$year]['Unknown'] = $lines;
            }

            if (count($notes)) {
                $weightings[$year]['Notes'] = $notes;
            }

        }

        $unknown = array_filter($unknown);

        if (!empty($unknown)) {
            $this->reportUnknown('Module_Weighting', $unknown);
        }

        return $weightings;
    }

}
