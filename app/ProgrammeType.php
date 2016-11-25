<?php

namespace Imperial\Simp;

use Illuminate\Database\Eloquent\Model;

class ProgrammeType extends Model
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

    public function programmes()
    {
        return $this->hasMany(Programme::class);
    }
}
