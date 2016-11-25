<?php

namespace Imperial\Simp;

use Illuminate\Database\Eloquent\Model;

use Imperial\Simp\Traits\SearchNamesTrait;

class Programme extends Model
{
    use SearchNamesTrait;

    protected $fillable = [
        'oss_code',
        'banner_code',
        'long_title',
        'oss_title',
        'banner_title',
        'level',
        'url',
        'hash',
        'parser',
        'details',
        'contents',
        'duration',
        'measure',
        'mode',
        'joint_duration',
        'joint_measure',
        'joint_mode',
        'entry',
        'flags',
    ];

    protected $casts = [
        'contents' => 'json',
        'flags'    => 'json',
    ];

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'programme_department')->withTimestamps();
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

    public function programmeType()
    {
        return $this->belongsTo(ProgrammeType::class);
    }

    public function specifications()
    {
        return $this->belongsToMany(Specification::class, 'programme_specification')->withTimestamps();
    }

    public function newPivot(Model $parent, array $attributes, $table, $exists)
    {
        if ($parent instanceof Module) {
            return new ModuleProgramme($parent, $attributes, $table, $exists);
        }
        return parent::newPivot($parent, $attributes, $table, $exists);
    }
}
