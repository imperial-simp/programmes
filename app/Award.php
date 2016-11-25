<?php

namespace Imperial\Simp;

use Illuminate\Database\Eloquent\Model;
use Imperial\Simp\Traits\SearchNamesTrait;

class Award extends Model
{
    use SearchNamesTrait;

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

    public function programmes()
    {
        return $this->hasMany(Programme::class);
    }

    public function jointProgrammes()
    {
        return $this->hasMany(Programme::class, 'joint_award_id');
    }
}
