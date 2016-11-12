<?php

namespace Imperial\Simp\Parsers\Module\Project;

use Imperial\Simp\Parsers\Module\Module\NewFormat as ModuleParser;

class NewFormat extends ModuleParser
{
    public function read()
    {
        return $this;
    }
    
    public static function identifyParser($text, $details = [])
    {
        return preg_match('/^Project Outline/', $text);
    }
    
}