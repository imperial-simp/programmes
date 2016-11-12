<?php

namespace Imperial\Simp\Loaders;

class PdfLoader
{
    protected $path;
    protected $details = [];
    protected $contents;

    public static function load($path)
    {
        $instance = new static();

        $instance->path = storage_path('app/'.$path);
        $pdf = app('parser.pdf')->parseFile($this->path);
        $instance->setDetails($pdf->getDetails());
        $instance->setContents($pdf->getText());
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function setDetails(array $details)
    {
        $this->details = $details;
    }

    public function setContents($contents)
    {
        $this->contents = $contents;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getDetails()
    {
        return $this->details;
    }

    public function getContents()
    {
        return $this->contents;
    }
}
