<?php

namespace Imperial\Simp\Parsers\Module\Module;

use Imperial\Simp\Parsers\Module\Parser as ModuleParser;

class NewFormat extends ModuleParser
{
    public function read()
    {
        return $this;
    }
    
    public static function identifyParser($text, $details = [])
    {
        return preg_match('/^Module Outline/', $text);
    }
    
}