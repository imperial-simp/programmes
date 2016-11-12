<?php

namespace Imperial\Simp\Parsers;

use Imperial\Simp\Specification;

class AbstractPdfParser extends AbstractParser
{
    protected $lines = [];

    protected function setText($text)
    {
        $text = $this->tidyText($text);
        $this->text = $text;
        $this->lines = explode(PHP_EOL, $text);
    }

    public static function getParsers()
    {
        return [
            'Pdf' => [
                'Programme' => [
                    'Postgrad' => [
                        'MEdFormat' => 'Parser',
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
            ],
        ];
    }

}
