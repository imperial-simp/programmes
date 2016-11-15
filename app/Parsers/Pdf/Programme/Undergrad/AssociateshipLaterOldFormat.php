<?php

namespace Imperial\Simp\Parsers\Pdf\Programme\Undergrad;

use Imperial\Simp\Parsers\Pdf\Programme\Undergrad\OldFormat as BaseParser;

class AssociateshipLaterOldFormat extends BaseParser
{

    public static function identifyParser($text, $details = [])
    {
        return preg_match('/^Programme Specification \(Undergraduate\).*(Programme Title).*(Associateship).*(Year\s+Weighting).*/s', $text);
    }

    public function getProgrammeInformationHeadings()
    {
        return [
            'Award\(s\)',
            'Programme Title',
            'Programme code',
            'Awarding Institution',
            'Teaching Institution',
            'Faculty',
            'Department',
            'Associateship',
            'Mode and Period of Study',
            'Cohort Entry Points',
            'Relevant QAA Benchmark Statement\(s\)',
            'Total Credits',
            'FHEQ Level',
            'EHEA Level',
            'External Accreditor\(s\)',
        ];
    }

}
