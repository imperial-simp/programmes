<?php

namespace Imperial\Simp\Parsers\Pdf\Programme\Postgrad;

use Imperial\Simp\Parsers\Pdf\Programme\Postgrad\NewFormat as BaseFormat;

class MEdFormat extends BaseFormat
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
