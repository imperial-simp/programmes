<?php

namespace Imperial\Simp;

use Illuminate\Database\Eloquent\Model;
use Imperial\Simp\Traits\SearchNamesTrait;

class Faculty extends Model
{
    use SearchNamesTrait;

    public $fillable = [
        'name',
        'abbrev',
        'oss_code',
        'banner_code',
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function departments()
    {
        return $this->hasMany(Department::class);
    }
}
