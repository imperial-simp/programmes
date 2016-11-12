<?php

namespace Imperial\Simp;

use Illuminate\Database\Eloquent\Model;

class Specification extends Model
{
    protected $fillable = [
        'title',
        'file',
        'mime',
        'etag',
        'url',
        'hash',
        'parser',
        'details',
        'contents',
    ];

    protected $casts = [
        'details'      => 'json',
        'contents'     => 'json',
        'retrieved_at' => 'datetime',
    ];

    public function retrieve($mime = null, $etag = null)
    {
        $this->mime = $mime;
        $this->etag = $etag;
        $this->retrieved_at = $this->freshTimestamp();
        $this->save();
    }

    public function shouldRetrieve()
    {
        return $this->url && is_null($this->retrieved_at);
    }

    public function getPathAttribute()
    {
        return 'specs/'.$this->extension.'/'.$this->file;
    }

    public function getExtensionAttribute()
    {
        return last(explode('.', $this->file)) ?: 'html';
    }

    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function award()
    {
        return $this->belongsTo(Award::class);
    }

    public function jointAward()
    {
        return $this->belongsTo(Award::class, 'joint_award_id');
    }

    public function calendar()
    {
        return $this->belongsTo(Calendar::class);
    }

    public function identifyParser()
    {
        $parsers = [
            'Pdf' => [
                'Programme' => [
                    'Postgrad' => [
                        'MEdFormat' => 'Parser',
                        'NewFormat' => 'Parser',
                        'OldFormat' => 'Parser',
                    ],
                    'Undergrad' => [
                        'NewFormat' => 'Parser',
                        'OldFormat' => 'Parser',
                    ],
                ],
                'Module' => [
                    'Module' => [
                        'NewFormat' => 'Parser',
                    ],
                    'Project' => [
                        'NewFormat' => 'Parser',
                    ],
                ],
            ],
        ];

        $parsers = array_dot($parsers, '\Imperial\Simp\Parsers\\');

        foreach ($parsers as $namespace => $class) {
            $class = str_replace('.', '\\', $namespace.'\\'.$class);

            if ($class::identify($text, $details)) {
                $parser = new $class($text);
                break;
            }
        }
    }
}
