<?php

namespace Imperial\Simp\Loaders;

abstract class AbstractLoader
{
    protected $path;
    protected $details = [];
    protected $text;

    public static function load($path)
    {
        $instance = new static();
        $instance->loadPath($path);
        return $instance;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function setDetails(array $details)
    {
        $this->details = $details;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getDetails()
    {
        return $this->details;
    }

    public function getText()
    {
        return $this->text;
    }

    public function parsers()
    {
        $parsers = array_dot($this->getParsers(), '\\Imperial\\Simp\Parsers\\');

        foreach ($parsers as $namespace => &$class) {
            $namespace = trim(trim($namespace, '0123456789'), '.');
            $class = str_replace('.', '\\', $namespace.'\\'.$class);
        }

        return $parsers;
    }

    abstract protected function getParsers();
}
