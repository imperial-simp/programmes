<?php

namespace Imperial\Simp\Loaders;

class PdfLoader extends AbstractLoader
{

    public function loadPath($path)
    {
        $this->path = storage_path('app/'.$path);
        $pdf = app('parser.pdf')->parseFile($this->path);
        $this->setDetails($pdf->getDetails());
        $this->setText($pdf->getText());
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
