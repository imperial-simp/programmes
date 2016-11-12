<?php

use App\Jobs\PdfParserJob;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

// Artisan::command('parse:pdf {path}', function ($path) {

//     dispatch(new PdfParserJob($path));

// });

Artisan::command('specs:retrieve', function () {
    $url = 'https://www.imperial.ac.uk/staff/tools-and-reference/quality-assurance-enhancement/programme-information/programme-specifications/';

    $programmes = [];
    $codes = require storage_path('app/specs/codes.php');

    $client = new Goutte\Client();
    $crawler = $client->request('GET', $url);

    $crawler->filter('.module .fake-h3')->each(function ($node) use($codes, &$programmes) {
        $faculty = $node->text();
        $facultyCode = array_get($codes, 'Faculties.'.$faculty.'.Code');

        $node->nextAll()->filter('.panel-group')->first()->filter('.item')->each(function($node) use($codes, &$programmes, $faculty, $facultyCode) {
            $department = $node->filter('.item-header')->text();
            $departmentCode = array_get($codes, 'Faculties.'.$faculty.'.Departments.'.$department.'.Code');

            $node->filter('.item-content table')->each(function($node) use($codes, &$programmes, $faculty, $facultyCode, $department, $departmentCode) {
               $level = $node->filter('caption')->text();

                $entries = $node->filter('thead tr:first-child th')->each(function($node) {
                   return str_replace(' entry', '', $node->text());
                });

                $node->filter('tbody tr')->each(function($node) use($codes, $entries, &$programmes, $level, $faculty, $facultyCode, $department, $departmentCode) {
                    $node->filter('td')->each(function($node, $i) use($codes, $entries, &$programmes, $level, $faculty, $facultyCode, $department, $departmentCode) {

                        $title = $node->text();
                        $title = str_replace(['[pdf]', '*'], '', $title);
                        $title = preg_replace('/\s+/u', ' ', $title);
                        $title = trim($title);

                        if ($title != 'N/A') {

                            $award = explode(' ', $title);

                            if (last($award) == 'MBA') {
                                $award = 'MBA';
                            }
                            else {
                                $award = head($award);
                            }

                            $programmeEntry = [
                                'Faculty'         => $faculty,
                                'Faculty_Code'    => $facultyCode,
                                'Department'      => $department,
                                'Department_Code' => $departmentCode,
                                'Level'           => $level,
                                'Programme_Title' => $title,
                                'Programme_Code'  => null,
                                'Award'           => $award,
                                'Entry_Year'      => @$entries[$i],
                                'URL'             => null,
                                'Downloaded'      => null,
                                'Imported'        => null,
                            ];

                            try {
                                $url = $node->filter('a[href]')->first()->link()->getUri();

                                $programmeEntry['URL'] = $url;
                                $file = basename($url);

                                $path = storage_path('app/specs/pdf/'.$file);

                                if (file_exists($path)) {
                                    $programmeEntry['Downloaded'] = date('Y-m-d H:i:s', filectime($path));
                                }
                                else {
                                    $pdf = file_get_contents($url);
                                    file_put_contents($path, $pdf);
                                    $programmeEntry['Downloaded'] = date('Y-m-d H:i:s');
                                }

                            }
                            catch (\InvalidArgumentException $e) { }

                            $programmes[] = $programmeEntry;

                        }
                    });
                });

            });

        });

    });

    Storage::put('specs/list_'.date('Ymd').'.json', json_encode($programmes, JSON_PRETTY_PRINT));

});


Artisan::command('parse:pdf {path}', function ($path) {

    $source = storage_path('app/specs/pdf/'.$path);

    if (!file_exists($source)) {
        throw new Exception('File does not exist.');
    }

    $pdf = app('parser.pdf')->parseFile($source);
    $text = $pdf->getText();
    $details = $pdf->getDetails();

    $parsers = [
        'Programme' => [
            'Postgrad' => [
                'NewFormat' => 'Parser',
                'OldFormat' => 'Parser',
            ],
            'Undergrad' => [
                'NewFormat' => 'Parser',
                'OldFormat' => 'Parser',
            ],
        ],
        'Module' => [
            'Module' => [
                'NewFormat' => 'Parser',
            ],
            'Project' => [
                'NewFormat' => 'Parser',
            ],
        ],
    ];

    $parsers = array_dot($parsers, '\App\Parsers\\');

    foreach ($parsers as $namespace => $class) {
        $class = str_replace('.', '\\', $namespace.'\\'.$class);

        if ($class::identify($text, $details)) {
            $parser = new $class($text);
            break;
        }
    }

    dd(get_class($parser));

});

// Artisan::command('parse:docx {path}', function ($path) {

//     $source = storage_path('app/specs/docx/'.$path);
//     if (!file_exists($source)) {
//         throw new Exception('File does not exist.');
//     }

//     $phpWord = \PhpOffice\PhpWord\IOFactory::load($source);

//     $phpWord->save(str_replace('docx','html', $source), 'HTML');

// });

// Artisan::command('parse:doc {path}', function ($path) {

//     $source = storage_path('app/specs/doc/'.$path);
//     $phpWord = \PhpOffice\PhpWord\IOFactory::load($source, 'MsDoc');



// });
