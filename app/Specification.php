<?php

namespace Imperial\Simp;

use Illuminate\Database\Eloquent\Model;
use Imperial\Simp\Loaders\PdfLoader;
// use Imperial\Simp\Loaders\HtmlLoader;

use Exception;

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
        'parsed_at' => 'datetime',
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

    public function parse()
    {
        $parser = $this->getParser();
        $this->contents = $parser->read();
        $this->parsed_at = $this->freshTimestamp();
        $this->save();
    }

    public function getParser()
    {
        if ($this->mime == 'application/pdf') {
            $loader = PdfLoader::load($this->path);
        }
        elseif ($this->mime == 'text/html') {
        $loader = HtmlLoader::load($this->path);
        }
        else {
            throw new Exception(sprintf('Cannot identify parser for MIME type %s.', $this->mime));
        }

        if ($this->parser) {
            $parser = $this->parser;
            if (class_exists($parser)) {
                if ($parser::identify($loader->getText(), $loader->getDetails())) {
                    return new $parser($this, $loader->getText(), $loader->getDetails());
                }
            }
        }

        $parsers = $loader->parsers();

        foreach ($parsers as $parser) {
            if (class_exists($parser)) {
                if ($parser::identify($loader->getText(), $loader->getDetails())) {
                    $this->parser = $parser;
                    $this->save();
                    return new $parser($this, $loader->getText(), $loader->getDetails());
                }
            }
        }

        $this->parser = null;
        $this->save();

        throw new Exception(sprintf('Cannot identify parser for specification [%s].', $this->id));
    }
}
