<?php

namespace App\Parsers\Module;

use App\Parsers\GenericParser;

abstract class Parser extends GenericParser
{
    public function read()
    {
        return $this;
    }
    
}