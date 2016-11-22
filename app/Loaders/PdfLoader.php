<?php

namespace Imperial\Simp\Loaders;

use Smalot\PdfParser\Document as PdfDocument;

class PdfLoader extends AbstractLoader
{

    public function loadPath($path)
    {
        $this->path = storage_path('app/'.$path);
        $pdf = app('parser.pdf')->parseFile($this->path);
        $this->setDetails($pdf->getDetails());
        $this->setText($this->tidyText($pdf->getText()));
        $this->setLinks($this->findLinks($pdf));
    }

    public function findLinks($pdf)
    {
        $links = [];

        if ($pdf instanceof PdfDocument) {

            $objs = $pdf->getObjects();
            foreach ($objs as $name => $obj) {
                if ($obj->getHeader()->has('A')) {
                    $a = $obj->getHeader()->get('A');
                    if ($a->has('URI')) {
                        $link = $a->get('URI');
                        $links[] = $link->getContent();
                    }
                }
            }

            return array_values(array_unique($links));
        }

        return $links;
    }

    public function tidyText($text)
    {
        $replacements = [
            '/ \./' => '.',
            '/^(.*Programme Specification\s+)?Page +\d+ +of +\d+\s*$/m' => PHP_EOL,
            '/(?<!^)(\x{2022})/uim' => PHP_EOL.'$1',
            '/(\x{2022} .*)\n([a-z])/um' => '$1 $2',
            '/\x{2019}/u' => '\'',
            '/&#34/' => '"',
            '/&#39/' => '\'',
            '/\x{2013}/u' => '-',
            '/ ([,-])/' => '$1',
            '/\n([a-z])/' => '$1',
            '/ (and|in|or|of|to|with|at|the|an?|for|including|by|using|according|prior|ensure|within|if|do|did|is|has|&) ?$\n/m' => ' $1 ',
            '@(?<![a-z])/ ?$\n@m' => '/',
            '/\( ?$\n/m' => '(',
            '/\n^([(,])/m' => '$1',
            '/ \)/' => ')',
            '/\(([^\)\n]+)$\n/m' => '($1 ',
            '/\n^([^\(\n\d]+)\)/m' => ' $1)',
            '/\n^(\([A-Z]+\))/m' => '$1',
            '/(([A-Z])[a-z]+)\s+(([A-Z])[a-z]+) \(([A-Z]*\2\4)\)/m' => '$1 $3 ($5)',
            // '/ {2,}/' => ' ',
            '/^\s+$/m' => '',
            '/(^\n+)/m' => '',
            '/%+/' => '%',
            '/\( /' => '(',
            '/-\n/' => '-',
            '%(?<!http://)www\.%s' => 'http://www.',
            '%https://http://%s' => 'https://',
        ];

        $text = preg_replace(array_keys($replacements), array_values($replacements), $text);

        $text = trim($text);

        return $text;
    }

    protected function getParsers()
    {
        return [
            'Pdf' => [
                'Programme' => [
                    'Postgrad' => [
                        'MEdFormat',
                        'NewFormat',
                        'StreamsOldFormat',
                        'OldFormat',
                    ],
                    'Undergrad' => [
                        'NewFormat',
                        'MultiProgrammeFormat',
                        'ElementTotalMarksFormat',
                        'TotalMarksFormat',
                        'BscLfsFormat',
                        'BEngBiomedFormat',
                        'StreamsOldFormat',
                        'TeacherTrainingOldFormat',
                        'OldFormat',
                    ],
                ],
                'Module' => [
                    'Module' => [
                        'NewFormat',
                    ],
                    'Project' => [
                        'NewFormat',
                    ],
                ],
            ],
        ];
    }

}
