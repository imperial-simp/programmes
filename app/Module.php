<?php

namespace Imperial\Simp;

use Illuminate\Database\Eloquent\Model;

use Imperial\Simp\Traits\SearchNamesTrait;

class Module extends Model
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
    ];

    protected $casts = [
        'contents'     => 'json',
    ];

    public function programmes()
    {
        return $this->belongsToMany(Programme::class, 'module_programme')
        ->withTimestamps()
        ->withPivot([
            'years',
            'core',
            'elective_group',
        ]);
    }

    public function specifications()
    {
        return $this->belongsToMany(Specification::class, 'module_specification')
        ->withTimestamps()
        ->withPivot([
            'ects',
            'fheq',
            'learning_hours',
            'study_hours',
            'placement_hours',
            'total_hours',
            'exam_weight',
            'coursework_weight',
            'practical_weight',
        ]);
    }

    public function newPivot(Model $parent, array $attributes, $table, $exists)
    {
        if ($parent instanceof Specification) {
            return new ModuleSpecification($parent, $attributes, $table, $exists);
        }
        elseif ($parent instanceof Programme) {
            return new ModuleProgramme($parent, $attributes, $table, $exists);
        }
        return parent::newPivot($parent, $attributes, $table, $exists);
    }
}
