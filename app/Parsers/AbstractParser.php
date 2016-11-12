<?php

namespace Imperial\Simp\Parsers;

use Imperial\Simp\Specification;
use Imperial\Simp\Loaders\PdfLoader;
// use Imperial\Simp\Loaders\HtmlLoader;

use Exception;

abstract class AbstractParser
{
    protected $specification;
    protected $text;
    protected $details = [];
    protected $output = [];

    public function __construct(Specification $specification, $text, array $details)
    {
        $this->specification = $specification;
        $this->text = $this->tidyText($text);
        $this->details = $details;
    }

    protected function tidyText($text)
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

    public static function guessParser(Specification $specification)
    {

        if ($specification->mime_type == 'application/pdf') {
            $loader = PdfLoader::make($specification->path);
        }
        // elseif ($specification->mime_type == 'text/html') {
            // $loader = new HtmlLoader($specification->path);
        // }
        else {
            throw new Exception('Cannot identify parser for file.');
        }

        $parsers = static::prepareParsers($parsers);

        foreach ($parsers as $class) {
            if ($class::identify($loader->getText(), $loader->getDetails())) {
                return new $class($specification, $text, $details);
                break;
            }
        }
    }

    abstract public static function identifyParser($text, $details = []);

    abstract public static function getParsers();

    public static function prepareParsers(array $parsers)
    {
        $parsers = array_dot($parsers, '\\Imperial\\Simp\Parsers\\');

        foreach ($parsers as $namespace => &$class) {
            $class = str_replace('.', '\\', $namespace.'\\'.$class);
        }

        return $parsers;
    }

    abstract protected function load();

    public function read()
    {
        return $this;
    }

    public function output()
    {
        return $this->output;
    }

}
