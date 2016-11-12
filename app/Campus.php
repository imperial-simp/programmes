<?php

namespace Imperial\Simp;

use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
    public $fillable = [
        'name',
        'short_name',
        'abbrev',
        'oss_code',
        'banner_code',
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }
}
