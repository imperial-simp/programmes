<?php

namespace Imperial\Simp;

use Illuminate\Database\Eloquent\Model;

class Award extends Model
{
    public $fillable = [
        'name',
        'short_name',
        'abbrev',
        'oss_code',
        'banner_code',
    ];

    public function awardType()
    {
        return $this->belongsTo(AwardType::class);
    }
}
