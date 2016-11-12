<?php

namespace Imperial\Simp\Parsers\Pdf\Programme\Postgrad\MEdFormat;

use Imperial\Simp\Parsers\Pdf\Programme\Postgrad\NewFormat\Parser as NewFormat;

class Parser extends NewFormat
{
    public function read()
    {
        return $this;
    }

    public static function identifyParser($text, $details = [])
    {
        return parent::identifyParser($text) && preg_match('/(University Learning and Teaching)/s', $text);
    }

}
