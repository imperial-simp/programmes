<?php

namespace Imperial\Simp\Parsers;

use Imperial\Simp\Specification;

abstract class AbstractParser
{
    protected $specification;
    protected $lines = [];
    protected $details = [];
    protected $output = [];

    public function __construct(Specification $specification)
    {
        $this->specification = $specification;
        $this->load();
    }

    public function setText($text)
    {
        $this->lines = $this->tidyText($text);
    }

    public function setDetails(array $details)
    {
        $this->details = $details;
    }

    public function tidyText($text)
    {
        return $text;
    }

    public static function identify($text, $details)
    {
        if (is_array($text)) {
            $text = implode(PHP_EOL, $text);
        }

        return static::identifyParser($text, $details);
    }

    abstract public static function identifyParser($text, $details = []);

    abstract public function load();

    public function read()
    {
        return $this;
    }

    public function output()
    {
        return $this->output;
    }

}
