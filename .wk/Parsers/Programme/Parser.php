<?php

namespace Imperial\Simp\Parsers\Programme;

use Imperial\Simp\Parsers\GenericParser;

abstract class Parser extends GenericParser
{
    public function read()
    {
        return $this;
    }
    
    public static function identifyParser($text, $details = [])
    {
        return preg_match('/^Programme Specification/', $text);
    }
    
    abstract function getSections();

}