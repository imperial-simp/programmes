<?php

namespace Imperial\Simp;

use Illuminate\Database\Eloquent\Model;

class AwardType extends Model
{
    public $fillable = [
        'name',
        'short_name',
        'abbrev',
        'fheq',
        'ehea',
        'oss_code',
        'banner_code',
    ];

    protected $casts = [
        'fheq' => 'int',
        'ehea' => 'int',
    ];

    public function awards()
    {
        return $this->hasMany(Award::class);
    }
}
