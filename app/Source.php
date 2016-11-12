<?php

namespace Imperial\Simp;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    protected $fillable = [
        'title',
        'etag',
        'url',
        'client',
    ];

    protected $casts = [
        'retrieved_at' => 'datetime',
    ];

    public function retrieve($etag)
    {
        $this->etag = $etag;
        $this->retrieved_at = $this->freshTimestamp();
        $this->save();
    }

    public function shouldRetrieve()
    {
        return $this->url && is_null($this->retrieved_at);
    }

    public function specifications()
    {
        return $this->hasMany(Specification::class);
    }
}
