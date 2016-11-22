<?php

namespace Imperial\Simp\Parsers;

use Imperial\Simp\Specification;

use Exception;

abstract class AbstractParser
{
    protected $specification;
    protected $text;
    protected $documentTitle = [];
    protected $details = [];
    protected $links = [];
    protected $errors = [];
    protected $rawText;

    public $debug = false;

    public function __construct(Specification $specification, $text, array $details, array $links)
    {
        $this->specification = $specification;
        $this->setText($text);
        $this->setDetails($details);
        $this->setLinks($links);
    }

    protected final function setText($text)
    {
        $this->rawText = $text;
        $this->text = $this->tidyText($text);
        $this->afterSetText();
    }

    protected function afterSetText()
    {
        //
    }

    public function setDetails(array $details)
    {
        $this->details = $details;
    }

    public function setLinks(array $links)
    {
        $this->links = $links;
    }

    protected function tidyText($text)
    {
        return $text;
    }

    protected function reportMissing($key, $missing)
    {
        foreach ((array) $missing as $value) {
            $this->errors['Missing'][$key][] = $value;
        }
    }

    protected function reportError($key, $value)
    {
        $this->errors['Errors'][$key] = $value;
    }

    protected function reportUnknown($key, $value)
    {
        $this->errors['Unknown'][$key] = $value;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getLinks()
    {
        return $this->links;
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
        $return = [
            'Document_Title' => $this->documentTitle,
            'Specification' => $this->lines,
        ];

        if (count($this->getLinks())) {
            $return['URLs'] = $this->getLinks();
        }

        if (count($this->getErrors())) {
            $return['Errors'] = $this->getErrors();
        }

        return $return;
    }

    public function getRawText()
    {
        return $this->rawText;
    }

    protected function slug($title)
    {
        $title = preg_replace(['/ ?& ?/', '% ?[/|] ?%'], [' and ', ' or '], $title);
        $title = str_replace(['-', '_'], ' ', $title);

        $title = \Illuminate\Support\Str::ascii($title);
        $title = preg_replace('![^\pL\pN\s]+!u', '', $title);
        if (is_array($title)) $title = head($title);
        $title = ucwords($title);
        $title = preg_replace('![\s]+!u', '_', $title);

        // $smallWords = ['Of', 'And', 'The', 'An?', 'For', 'By', 'With', 'To', 'Or'];
        //
        // foreach ($smallWords as $word) {
        //     $title = preg_replace('/_('.$word.')_/', '_'.strtolower($word).'_', $title);
        // }

        $replacements = [
            'Re_Sit' => 'Resit',
            'Elearning' => 'eLearning',
        ];

        $title = str_replace(array_keys($replacements), array_values($replacements), $title);

        return trim($title, '_');
    }

    protected function splitOr($text)
    {
        $text = preg_split('@(\s*(?:\b(?:or|and)\b|[/&])\s*|,\s*)@', $text);

        if (count($text) == 1) {
            $text = head($text);
        }

        return $text;
    }

    public function numberFromWord($val)
    {
        if (is_numeric($val)) {
            return (float) $val;
        }

        $val = strtolower($val);

        $words = [
            'zero',
            'one',
            'two',
            'three',
            'four',
            'five',
            'six',
            'seven',
            'eight',
            'nine',
            'ten',
        ];

        foreach ($words as $number => $word) {
            if ($val == $word) {
                return $number;
            }
        }

        $words = [
            'zeroth',
            'first',
            'second',
            'third',
            'fourth',
            'fifth',
            'sixth',
            'seventh',
            'eighth',
            'ninth',
            'tenth',
        ];

        foreach ($words as $number => $word) {
            if ($val == $word) {
                return $number;
            }
        }

        return $val;
    }

    protected function stringKeys(array $array)
    {
        return array_only($array, array_filter(array_keys($array), 'is_string'));
    }

}
