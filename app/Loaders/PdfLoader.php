<?php

namespace Imperial\Simp\Loaders;

class PdfLoader extends AbstractLoader
{

    public function loadPath($path)
    {
        $this->path = storage_path('app/'.$path);
        $pdf = app('parser.pdf')->parseFile($this->path);
        $this->setDetails($pdf->getDetails());
        $this->setText($this->tidyText($pdf->getText()));
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
            '/ ([,-])/' => '$1',
            '/\n([a-z])/' => '$1',
            '/ (and|in|or|of|to|with|at|the|an?|for|including|by|using|according|prior|ensure|within|if|do|did|is|has) ?$\n/m' => ' $1 ',
            '/\( ?$\n/m' => '(',
            '/\n^\(/m' => '(',
            '/ \)/' => ')',
            '/\n^(\([A-Z]+\))/m' => '$1',
            '/(([A-Z])[a-z]+)\s+(([A-Z])[a-z]+) \(([A-Z]*\2\4)\)/m' => '$1 $3 ($5)',
            '/ {2,}/' => ' ',
            '/^\s+$/m' => '',
            '/%+/' => '%',
            '/\( /' => '(',
            '%(?<!http://)www\.%s' => 'http://www.',
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
                        'OldFormat',
                    ],
                    'Undergrad' => [
                        'NewFormat',
                        'BscLfsFormat',
                        'BEngBiomedFormat',
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
