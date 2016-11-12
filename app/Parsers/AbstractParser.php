<?php

namespace Imperial\Simp\Parsers;

use Imperial\Simp\Specification;

use Exception;

abstract class AbstractParser
{
    protected $specification;
    protected $text;
    protected $details = [];

    public function __construct(Specification $specification, $text, array $details)
    {
        $this->specification = $specification;
        $this->setText($text);
        $this->setDetails($details);
    }

    protected function setText($text)
    {
        $this->text = $this->tidyText($text);
    }

    public function setDetails(array $details)
    {
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

    abstract public static function identifyParser($text, $details = []);

    public function read()
    {
        return $this;
    }

    public function output()
    {
        return $this->lines;
    }

    protected function slug($title)
    {
        $title = preg_replace(['/ ?& ?/', '% ?[/|] ?%'], [' and ', ' or '], $title);

        $title = \Illuminate\Support\Str::ascii($title);
        $title = preg_replace('![^\pL\pN\s]+!u', '', $title);
        $title = ucwords($title);
        $title = preg_replace('![\s]+!u', '_', $title);

        // $smallWords = ['Of', 'And', 'The', 'An?', 'For', 'By', 'With', 'To', 'Or'];
        //
        // foreach ($smallWords as $word) {
        //     $title = preg_replace('/_('.$word.')_/', '_'.strtolower($word).'_', $title);
        // }

        $title = str_replace('Elearning', 'eLearning', $title);

        return trim($title, '_');
    }

}
