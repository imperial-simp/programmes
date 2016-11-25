<?php

namespace Imperial\Simp\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use RuntimeException;
use File;
use Storage;

class PdfParserJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $path;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!Storage::disk('specs')->exists('pdf/'.$this->path)) {
            throw new RuntimeException(sprintf('PDF file "%s" does not exist.', $this->path));
        }

        // $path = base_path(Storage::url('app/specs/pdf/'.$this->path)); //FIXME
        $path = Storage::disk('specs')->url('pdf/'.$this->path); //FIXME

        $pdf = app('parser.pdf')->parseFile($path);

        $details = $pdf->getDetails();
        $text = $pdf->getText();
        $hash = md5($text);

        $replacements = [
//             '/ *\n/' => PHP_EOL,
//             '/\n\s*\n/m' => PHP_EOL,
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
            //'\s[b-zA-HJ-Z]\s' -- single letters floating (excl. a or I) -- but what about capitals
        ];

        $text = preg_replace(array_keys($replacements), array_values($replacements), $text);

        $headerReplacements = [
            'Code Title Core/ Elective Year L&T Hours Ind. Study Hours Place-? ment Hours Total Hours % Written Exam % Course-? work % Practical FHEQ Level ECTS' =>
            'Module Table Header',
            'Qualifications Framework of the European Higher Education Area' => 'Qualifications Framework of the European Higher Education Area',
            'Year % Year Weighting Module % Module Weighting' => 'Module Weighting',
        ];

        foreach ($headerReplacements as $headerFind => $headerReplace) {
            $text = preg_replace('#'.str_replace(' ', '\s*', $headerFind).'#m', $headerReplace, $text);
        }

        Storage::disk('specs')->put('txt/'.File::name($path).'_'.$hash.'.txt', $text);

        $definitions = [
            'Programme Specification (Undergraduate)' => [
                'Programme Information' => [
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
                ],
                'Specification Details' => [
                    'Student cohorts covered by specification',
                    'Person responsible for the specification',
                    'Date of introduction of programme',
                    'Date of programme specification\/revision',
                ],
                'Description of Programme Contents' => '',
                'Learning Outcomes' => '',
                'Entry Requirements' => [
                    'Academic Requirements?',
                    '(?:Non ?-academic|Additional) Requirements?',
                    'English Requirements?',
//                     'The programme\'s competency standards documents can be found at:',
                ],
                'Learning & Teaching Strategy' => [
                    'Scheduled Learning & Teaching Methods',
                    'E-learning & Blended Learning Methods',
                    'Project and Placement Learning Methods',
                ],
                'Assessment Strategy' => [
                    'Assessment Methods',
                    'Academic Feedback Policy',
//                     'The College\'s Policy on Re-sits is available at:',
//                     'The College\'s Policy on Mitigating Circumstances is available at:',
                ],
                'Assessment Structure' => [
                    'Marking Scheme',
                ],
                'Module Weighting' => '@ModuleWeighting',
                'Module List' => '@ModuleList',
                'Supporting Information' => '@SupportingInformation',
            ],
            'Programme Specification (Master\'s Level)' => [
            ]
        ];

        $lines = explode(PHP_EOL, trim($text));

        $headings = $this->identifyDocument($lines, $definitions);

        $sections = $this->readDocument($lines, array_keys($headings));

        foreach ($sections as $heading => &$lines) {
            foreach ($headings as $key => $possibleHeadings) {
                if ($this->slug($key) == $heading) {
                    $sectionHeadings = $possibleHeadings;
                    break;
                }
            }
            if (isset($sectionHeadings)) {
                if (is_array($sectionHeadings)) {
                    $lines = $this->readSection($lines, $sectionHeadings, $heading);
                }
                elseif (str_is('@*', $sectionHeadings)) {
//                     dd($sectionHeadings, $lines);
                    $method = 'read'.ltrim($sectionHeadings,'@');
                    $lines = $this->$method($lines);
                }
            }
        }

        $sections['Document_Details'] = $details;

        Storage::disk('specs')->put('json/'.File::name($path).'_'.$hash.'.json', json_encode($sections, JSON_PRETTY_PRINT));

    }

    public function identifyDocument(array &$lines, array $types)
    {
        $fileKey = str_slug(array_shift($lines), '');

        foreach ($types as $type => $headings) {
            if (str_slug($type, '') == $fileKey) {
                return $headings;
            }
        }

        throw new RuntimeException(sprintf('Unrecognised file format: "%s".', $fileKey));
    }

    public function readDocument(array $lines, array $sectionHeadings)
    {
        $sections = [];
        $buffer = [];
        $lastHeading = null;

        $sectionHeading = array_shift($sectionHeadings);

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line) {

                if (!isset($sections['Document_Title'])) {
                    if (preg_match('/^[BM](Eng|Sc ?i?).*/', $line)) {
                        $sections['Document_Title'] = $line;
                        continue;
                    }
                }

//                 if (!str_is('This document provides a definitive record*', $line)) {

                    if (preg_match('#^('.$sectionHeading.')(.*)$#i', $line, $matches)) {

                        if ($lastHeading) {
                            $sections[$this->slug($lastHeading)] = $buffer;
                            $buffer = [];
                        }

                        if ($matches[2]) {
                            $sections[$this->slug($sectionHeading)][] = $matches[2];
                        }

                        $lastHeading = $sectionHeading;
                        $sectionHeading = array_shift($sectionHeadings);
                    }
                  else {
                      $buffer[] = $line;
                  }
//               }
            }
        }

        if ($lastHeading) {
            $sections[$this->slug($lastHeading)] = $buffer;
            $buffer = [];
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

                try {
                   preg_match('#^('.$sectionField.')(.+)$#', $line);
                }
                catch (\Exception $e)
                {
                    dd($sectionField);
                }

                if ($sectionField && preg_match('#^('.$sectionField.')$#', $line)) {

                    $lastField = $sectionField;
                    $sectionField = array_shift($sectionFields);
                }
                elseif ($sectionField && preg_match('#^('.$sectionField.')(.+)$#', $line, $matches)) {

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
            if (is_array($values)) {
                $values = array_flatten($values);
            }
        }

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

    protected function slug($title)
    {
        $title = preg_replace(['/ ?& ?/', '% ?[/|] ?%'], [' and ', ' or '], $title);

        $title = \Illuminate\Support\Str::ascii($title);
//         $title = preg_replace('![/\\\\]+!u', ' ', $title);
        $title = preg_replace('![^\pL\pN\s]+!u', '', $title);
        $title = ucwords($title);
        $title = preg_replace('![\s]+!u', '_', $title);

        $smallWords = ['Of', 'And', 'The', 'An?', 'For', 'By', 'With', 'To', 'Or'];

        foreach ($smallWords as $word) {
            $title = preg_replace('/_('.$word.')_/', '_'.strtolower($word).'_', $title);
        }

        $title = str_replace('Elearning', 'eLearning', $title);

        return trim($title, '_');
    }

    protected function readModuleList($lines)
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
        (?<Elective>CORE|ELECTIVE(?:\s\([A-Z]\))|ELECTIVE\*+)\s
        (?<Year>(?:\d|\d\sor\s\d))\s
        (?:(?<Learning_Hours>[\d.]+)\s
        (?<Indiv_Study_Hours>[\d.]+)\s
        (?<Placement_Hours>(?:[\d.]+|See\sbelow))|(?<Various_Hours>Various))\s
        (?<Total_Hours>[\d.]+)\s
        (?:(?<Written_Exam>\d+%)\s
        (?<Coursework>\d+%)\s
        (?<Practical>\d+%)\s
        (?<FHEQ>\d)|(?<Various_Assessment>Various))\s
        (?<ECTS>[\d.]+)
        $';
//         return '^
//         (?<Code>[^\s]+)\s
//         (?<Title>.*?)\s
//         (?<Elective>CORE|ELECTIVE(?:\s\([A-Z]\))|ELECTIVE\*)\s
//         (?<Year>(?:\d|\d\sor\s\d))\s
//         (?<Learning_Hours>[\d.]+)\s
//         (?<Indiv_Study_Hours>[\d.]+)\s
//         (?<Placement_Hours>[\d.]+)\s
//         (?<Total_Hours>[\d.]+)\s
//         (?<Written_Exam>\d+%)\s
//         (?<Coursework>\d+%)\s
//         (?<Practical>\d+%)\s
//         (?<FHEQ>\d)\s
//         (?<ECTS>[\d.]+)
//         $';
    }

    protected function readModule($line)
    {
        $regex = '/'.$this->getModuleRegex().'/x';

        if (preg_match($regex, $line, $matches)) {
            return $this->getModuleKeys($matches);
        }
    }

    protected function readBrokenModules(&$text)
    {
        $regex = '/'.$this->getModuleRegex().'/smx';
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

    protected function readSupportingInformation($lines)
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
                    $info[ucwords($line[0])] = $line[1];
                }
                else {
                    $info[] = implode(PHP_EOL, $line);
                }

            }
            else {
                $unknown[] = $line;
            }
        }

//                 dd($info, $unknown);

        return [
            'Links' => $info,
            'Unknown' => $unknown,
        ];
    }

    protected function readModuleWeighting($lines)
    {
        $weightings = [];
        $buffer = [];
        $moduleBuffer = [];
        $unknown = [];

        $lastYear = null;

        $yearRegex = '/^(?<year>(?:Year (?:One|Two|Three|Four|Five|Six|\d))|(?:First|1st|Second|2nd|Third|3rd|Fourth|Fifth|Sixth|[4-6]th|Final) Year) (?<weighting>[\d\.]+ ?%)$/';
        $moduleRegex = '/^(?<module>(?:[A-Z]|[0-9]+ (?:day|hour|week|month))[^%]+?)(?<weighting>[\d\.]+ ?%) ?$/sm';

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

        dd($unknown);

        return $weightings;
    }

    protected function readProgramme_Information_Awards($text)
    {
        return explode(' ', $text);
    }
    protected function readProgramme_Information_Total_Credits($text)
    {
        return $text;
    }

}
