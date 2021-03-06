<?php

namespace Imperial\Simp\Parsers\Pdf\Programme\Undergrad;

use Imperial\Simp\Parsers\Pdf\Programme\AbstractPdfParser as BaseParser;

class OldFormat extends BaseParser
{
    protected $tidyReplacements = [
        '/Programme Specification \(Undergraduate\)/' => '',
        '/Person\(s\) responsible/' => 'Person responsible',
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
                if (preg_match('#^('.$heading.').*#i', $line)) {
                    $order[$heading] = $ln;
                }
            }
        }

        asort($order);

        $order = array_keys($order);

        $missing = array_diff($headings, $order);

        if (count($missing)) {
            $this->reportMissing('Headings', $missing);
        }

        return $order;

    }

    public function readRelevantQAABenchmarkStatementsField(array $lines = [])
    {
        $lines = array_filter($lines, function($line) {
            return 'and/or other external reference points' != $line;
        });

        return array_values($lines);
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
            'Project (?:and Placement )?Learning Methods',
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
            ['Marking Scheme', 'Rules (or|for) Progression'],
            // 'Detailed Programme Structure'
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
                $line = preg_replace('/Imperial College is an independent corporation.*www\./', 'College Charter__AT__http://www.', $line);
                $line = preg_replace('/.*Higher Education Funding Council for England \(HEFCE\)/', 'HEFCE__AT__', $line);

                $line = explode('__AT__', $line);

                $line = array_map('trim', $line);

                 if (count($line) == 2) {
                    $info[$this->slug($line[0])] = str_replace(' ', '', $line[1]);
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
            // $this->reportUnknown('Supporting_Information', $unknown); // Other, not Unknown
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
            if (preg_match('/^(\*+)(.*)$/', $value, $matches)) {
                $credits['Note'] = [
                    'Marker' => $matches[1],
                    'Note'   => $matches[2],
                ];
            }
            else {
                $credits['Unknown'] = $value;
                $this->reportUnknown('Total_Credits', $value);
            }
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
        $lines = preg_replace('/\n *(ELECTIVE|CORE)/m', ' $1', $lines);
        $lines = preg_replace('/^ *([A-Z0-9-]+) *\n/mi', '$1 ', $lines);
        $lines = str_replace(['Module Table Header', 'Indicative Module List', 'Module List'], '', $lines);

        $lines = explode(PHP_EOL, $lines);

        $pathway = null;

        foreach ($lines as $i => &$line) {
            if (preg_match('/\((.*)Pathways?\)/', $line, $match)) {
                $pathway = trim($match[1]);
                $line = null;
            }
            elseif (isset($lines[$i+1]) && !preg_match($this->getModuleFiguresRegex(), $line) && !$this->readModule($line) && $module = $this->readModule($line.PHP_EOL.$lines[$i+1])) {
                if ($pathway) $module['Pathway'] = $pathway;
                $modules[] = $module;
                $lines[$i+1] = null;
            }
            elseif ($module = $this->readModule($line)) {
                if ($pathway) $module['Pathway'] = $pathway;
                $modules[] = $module;
            } else {
                if ($line) $unknown[] = $line;
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
            elseif(preg_match('/^•/', trim($line))) {
                $last = array_pop($notes);
                $last['Note'] = (array) $last['Note'];
                $last['Note'][] = $line;
                $notes[] = $last;
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
        (?<Code>(?:[A-Z]{3,}\s[0-9]{3,}|[^\s]+))\s
        (?<Title>(?:[^%]{5,100}?)|IDX)\s
        (?<Elective>CORE\**|ELECTIVE(?:\s\([^)]+\))|ELECTIVE\**)\s
        (?<Year>(?:\d|(?:\d,\s*)?\d(?:\sor\s|\s*/\s*)\d))\s
        (?:(?<Learning_Hours>[\d.]+)\s
        (?<Indiv_Study_Hours>[\d.]+)\s
        (?<Placement_Hours>(?:[\d.]+|See\s+[Bb]elow))|(?<Various_Hours>Various|Variable))\s
        (?<Total_Hours>[\d.]+)\s
        (?:(?:(?<Written_Exam>[\d.]+\s?%?)\s
        (?<Coursework>[\d.]+\s?%?)\s
        (?<Practical>[\d.]+\s?%?)|(?<Various_Assessment>Various|Variable))\s
        (?<FHEQ>\d)|(?<Various_Assessment_FHEQ>Various|Variable))\s
        (?<ECTS>[\d.]+)
        $';
    }

    public function getVariousAllModuleRegex()
    {
        return '^
        (?<Code>(?:[A-Z]{3,}\s[0-9]{3,}|[^\s]+))\s
        (?<Title>(?:[^%]{5,100}?)|IDX)\s
        (?<Elective>CORE\**|ELECTIVE(?:\s\([^)]+\))|ELECTIVE\**)\s
        (?<Year>(?:\d|(?:\d,\s*)?\d(?:\sor\s|\s*/\s*)\d))\s
        (?<Various_All>Various|Variable)\s
        (?<FHEQ>\d)\s
        (?<ECTS>[\d.]+)
        $';
    }

    public function getNotAssessedModuleRegex()
    {
        return '^
        (?<Code>(?:[A-Z]{3,}\s[0-9]{3,}|[^\s]+))\s
        (?<Title>(?:[^%]{5,100}?)|IDX)\s
        (?<Elective>CORE\**|ELECTIVE(?:\s\([^)]+\))|ELECTIVE\**)\s
        (?<Year>(?:\d|(?:\d,\s*)?\d(?:\sor\s|\s*/\s*)\d))\s
        (?:(?<Learning_Hours>[\d.]+)\s
        (?<Indiv_Study_Hours>[\d.]+)\s
        (?<Placement_Hours>(?:[\d.]+|See\sbelow))|(?<Various_Hours>Various))\s
        (?<Total_Hours>[\d.]+)\s
        (?<Not_Assessed>(?:Not\sassessed|N/A))
        (?:\sN/A|\s0(?:\.00)?)*
        $';
    }

    public function getModuleFiguresRegex()
    {
        return '/(\d+\s?%? ){6,}/';
    }

    protected function readModule($line)
    {

        $regexes = [
            $this->getModuleRegex(),
            $this->getVariousAllModuleRegex(),
            $this->getNotAssessedModuleRegex(),
        ];

        foreach ($regexes as $regex) {
            $regex = '@'.$regex.'@simx';

            if (preg_match($regex, $line, $matches)) {
                return $this->getModuleKeys($matches);
            }
        }
    }

    protected function readBrokenModules(&$text)
    {
        $regex = '@'.$this->getModuleRegex().'@simx';
        $modules = [];

        if (preg_match_all($regex, $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $text = str_replace($match[0], null, $text);
                $match = $this->getModuleKeys($match);
                $match = array_map(function($item) {
                    if (is_string($item)) {
                        return trim(preg_replace('/\s+/', ' ', $item));
                    }
                }, $match);
                $modules[] = $match;
            }

            $text = trim(preg_replace('/\n+/', PHP_EOL, $text));
        }

        return $modules;
    }

    protected function getModuleKeys(array $array)
    {
        $string = $array[0];

        $fields = $this->stringKeys($array);

        foreach ($fields as &$value) {
            $value = is_string($value) ? trim(preg_replace('/\s+/', ' ', $value)) : $value;
        }

        if (trim(@$fields['Various_All'])) {
            $fields['Learning_Hours'] = '(various)';
            $fields['Placement_Hours'] = '(various)';
            $fields['Indiv_Study_Hours'] = '(various)';
            $fields['Total_Hours'] = '(various)';
            $fields['Written_Exam'] = '(various)';
            $fields['Coursework'] = '(various)';
            $fields['Practical'] = '(various)';
        }
        elseif (trim(@$fields['Not_Assessed'])) {
            $fields['Written_Exam'] = '(not assessed)';
            $fields['Coursework'] = '(not assessed)';
            $fields['Practical'] = '(not assessed)';
            $fields['FHEQ'] = '(not assessed)';
            $fields['ECTS'] = '(not assessed)';
        }
        else {
            if (@$fields['Various_Hours']) {
                $fields['Learning_Hours'] = '(various)';
                $fields['Placement_Hours'] = '(various)';
                $fields['Indiv_Study_Hours'] = '(various)';
            }
            if (@$fields['Various_Assessment']) {
                $fields['Written_Exam'] = '(various)';
                $fields['Coursework'] = '(various)';
                $fields['Practical'] = '(various)';
            }
            if (@$fields['Various_Assessment_FHEQ']) {
                $fields['Written_Exam'] = '(various)';
                $fields['Coursework'] = '(various)';
                $fields['Practical'] = '(various)';
                $fields['FHEQ'] = '(various)';
            }

                $fields['Written_Exam'] = str_replace(' ', '', $fields['Written_Exam']);
                $fields['Coursework'] = str_replace(' ', '', $fields['Coursework']);
                $fields['Practical'] = str_replace(' ', '', $fields['Practical']);

        }

        if (preg_match('/^([^\*]+)(\*+)$/', $fields['Elective'], $matches)) {
            $fields['Elective'] = $matches[1];
            $fields['Note'] = $matches[2];
        }
        elseif ($fields['Elective'] == 'CORE') {
            $fields['Core'] = true;
            unset($fields['Elective']);
        }
        elseif (preg_match('/ELECTIVE \((.+)\)/', $fields['Elective'], $match)) {
            $fields['Elective_Group'] = $this->splitOr($match[1]);

            // if (is_string($fields['Elective_Group']) && strlen($fields['Elective_Group']) > 1 && preg_match('/^[A-G]+$/', $fields['Elective_Group'])) {
            //     $fields['Elective_Group'] = str_split($fields['Elective_Group']); // Two-char group name is a single group
            // }

            $fields['Elective'] = true;
            $fields['Core'] = false;
        }

        if (isset($fields['Year'])) {
            $fields['Year'] = $this->splitOr($fields['Year']);
        }

        unset($fields['Various_All']);
        unset($fields['Various_Hours']);
        unset($fields['Various_Assessment']);
        unset($fields['Various_Assessment_FHEQ']);

        if (in_array($fields['Code'],  ['N/A', 'TBC', 'TBA'], true)) {
            $fields['Code'] = null;
            $fields['No_Code'] = true;
        }
        else {
            $fields['Code'] = $this->splitOr($fields['Code']);
        }

        if ($this->debug) $fields['_String'] = $string;

        return $fields;
    }

    public function getModuleWeightingRegex()
    {
        return '@^
        (?<Module>
        (?:(?<Count>(?:[0-9]+|one|t(?:wo|hree)|f(?:our|ive)|s(?:ix|even)|eight|nine|ten))
        (?:\s[x×])?\s(?<Unit>day|hour|week|month|module)s?\s+(?:of)?)?
        (?<Options>[^%]+?)
        )
        (?<Weighting>(?:[\d\.]+\s?r?\s?%|N/A|\*))
        (?<Each>\s*(?:each)?)
        (?<Note>\**)
        $@ismx';
    }

    public function getModuleWeightingYearRegex()
    {
        return '/^(?<Year>(?:Year (?:One|Two|Three|Four|Five|Six|\d))|(?:First|1st|Second|2nd|Third|3rd|Fourth|Fifth|Sixth|[4-6]th|Final) Year)(?:\s+\((?<Year_Remark>[^)]+)\))? (?<Weighting>[\d\.]+ ?%)(?<Rest>.*)$/';
    }

    public function getListSeparatorsRegex()
    {
        return '/(?<!(?-i:[\dA-Z]))\s*+(\b(either|or|and|plus|to include|including)\b:?|[;,•])\s*+(?!(?-i:[\dA-Z]))/i';
    }

    protected function readModuleWeightingSection(array $lines = [])
    {
        $weightings = [];
        $moduleBuffer = [];
        $unknown = [];

        $lastYear = null;

        $yearRegex = $this->getModuleWeightingYearRegex();
        $moduleRegex = $this->getModuleWeightingRegex();

        $lines = implode(PHP_EOL, $lines);
        $lines = preg_replace('/\n ?([\d\.]+ ?%?)(?=\s+\d\s+)/m', ' $1'.PHP_EOL, $lines);
        $lines = str_replace(['Module Weighting', 'Module Element Total Marks Weighting', 'Module Total Marks Weighting'], '', $lines);
        $lines = explode(PHP_EOL, $lines);

        foreach ($lines as $i => &$line) {
            $lastLine = false;

            if (isset($lines[$i+1]) && !preg_match($yearRegex, $line, $matches) && preg_match($yearRegex, $line.' '.$lines[$i+1], $matches)) {
                $lines[$i+1] = null;
                $lastLine = true;
            }

            if ($lastLine || preg_match($yearRegex, $line, $matches)) {

                if ($rest = trim(@$matches['Rest'])) {
                    $lines[$i+1] = $rest . ' ' . $lines[$i+1];
                }

                $lastYear = $matches['Year'];

                if (@$matches['Year_Remark']) {
                    $weightings[$this->slug($lastYear)]['Remark'] = $matches['Year_Remark'];
                }

                if (@$matches['Weighting']) {
                    $weightings[$this->slug($lastYear)]['Weighting'] = $matches['Weighting'];
                }

                if (@$matches['Total_Marks']) {
                    $weightings[$this->slug($lastYear)]['Total_Marks'] = $matches['Total_Marks'];
                }

                if (@$matches['Element']) {
                    $weightings[$this->slug($lastYear)]['Element'] = $matches['Element'];
                }
            }
            else {
                $weightings[$this->slug($lastYear)]['Modules'][] = $line;
            }
        }
        // dd($weightings);

        foreach ($weightings as $yr => &$year) {

            $modules = implode(PHP_EOL, $year['Modules']);
            $year['Modules'] = [];

            if (preg_match_all($moduleRegex, $modules, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {

                    $string = $match[0];

                    $module = [
                      'Module' => trim(preg_replace('/\s+/', ' ', $match['Module'])),
                    ];

                    if (isset($match['Weighting'])) {
                        if ($match['Weighting'] == '*') {
                            $module['Note'] = '*';
                            $module['Weighting'] = '(See note)';
                        }
                        else {
                            $module['Weighting'] = $match['Weighting'];
                        }
                    }

                    if (isset($match['Total_Marks'])) {
                        $module['Total_Marks'] = $match['Total_Marks'];
                    }

                    if ($match['Note']) {
                        $module['Note'] = $match['Note'];
                    }
                    elseif (preg_match('/(?<Module>.*)(?<Note>\*+)$/', $module['Module'], $noteMatch)) {
                        $module['Module'] = $noteMatch['Module'];
                        $module['Note'] = $noteMatch['Note'];
                    }

                    if (isset($module['Weighting'])) {
                        if (preg_match('/([\d.]+)r\s?%/', $module['Weighting'], $found)) {
                            $module['Weighting'] = $found[1].'%';
                            $module['Approximate'] = true;
                        }
                        elseif (preg_match('/\.(3{2,}|6{2,})%/', $module['Weighting'])) {
                            $module['Approximate'] = true;
                        }
                    }

                    if (preg_match('/(^EITHER: .*|\beither.*or\b|•|including:|to include:)/', $module['Module'])) {
                        $module['Either_Or'] = true;
                        $module['Options'] = array_values(array_filter(preg_split($this->getListSeparatorsRegex(), $module['Module'])));
                    }

                    if (trim($match['Each'])) {
                        $module['Each'] = true;
                    }

                    if ($match['Count']) {
                        $module['Count'] = $this->numberFromWord($match['Count']);
                        $module['Unit'] = ucfirst($match['Unit']);

                        if (!isset($module['Options'])) {
                            $module['Options'] = ucfirst(trim($match['Options']));
                        }

                        $options = is_array($module['Options']) ? implode(' ', $module['Options']) : $module['Options'];

                        if (preg_match('/elective group \((.+)\)/i', $options, $match)) {
                            $module['Elective_Group'] = $this->splitOr($match[1]);
                        }
                    }

                    if ($this->debug) $module['_String'] = $string;

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

        $notes = [];

        foreach ($unknown as $year => &$lines) {
            foreach ($lines as &$line) {
                if (preg_match('/^(\*+)(.*)$/', $line, $matches)) {
                    $notes[$year][] = [
                        'Marker' => $matches[1],
                        'Note'   => $matches[2],
                    ];
                    $line = null;
                }
                elseif(preg_match('/^•/', trim($line))) {
                    $last = array_pop($notes[$year]);
                    $last['Note'] = (array) $last['Note'];
                    $last['Note'][] = $line;
                    $notes[$year][] = $last;
                    $line = null;
                }
            }

            $lines = array_values(array_filter($lines));

            if (count($lines)) {
                $weightings[$year]['Unknown'] = $lines;
            }

            foreach ($notes as $year => $note) {
                $weightings[$year]['Notes'] = $note;
            }

        }

        $unknown = array_filter($unknown);

        if (!empty($unknown)) {
            $this->reportUnknown('Module_Weighting', $unknown);
        }

        return $weightings;
    }

    public function readExternalAccreditorsField(array $lines = [])
    {
        $lines = implode(', ', $lines);

        return explode(', ', $lines);
    }

}
