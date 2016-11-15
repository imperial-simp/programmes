<?php

namespace Imperial\Simp\Parsers;

use Imperial\Simp\Specification;

use Exception;

abstract class AbstractParser
{
    protected $specification;
    protected $text;
    protected $details = [];
    protected $errors = [];
    protected $rawText;

    public function __construct(Specification $specification, $text, array $details)
    {
        $this->specification = $specification;
        $this->setText($text);
        $this->setDetails($details);
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

    public function getErrors()
    {
        return $this->errors;
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
            'Specification' => $this->lines,
        ];

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

}
