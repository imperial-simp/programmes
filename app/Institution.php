<?php

namespace Imperial\Simp;

use Illuminate\Database\Eloquent\Model;
use Imperial\Simp\Traits\SearchNamesTrait;

class Institution extends Model
{
    use SearchNamesTrait;
    
    public $fillable = [
        'name',
        'abbrev',
        'oss_code',
        'banner_code',
    ];

    public function faculties()
    {
        return $this->hasMany(Faculty::class);
    }
}
