<?php

namespace Imperial\Simp;

use Illuminate\Database\Eloquent\Model;
use Imperial\Simp\Traits\SearchNamesTrait;

class Department extends Model
{
    use SearchNamesTrait;
    
    public $fillable = [
        'name',
        'abbrev',
        'oss_code',
        'banner_code',
    ];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }
}
