<?php

namespace Imperial\Simp\Parsers\Module;

use Imperial\Simp\Parsers\GenericParser;

abstract class Parser extends GenericParser
{
    public function read()
    {
        return $this;
    }
    
}