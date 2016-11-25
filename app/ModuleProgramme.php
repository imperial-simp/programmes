<?php

namespace Imperial\Simp;

Illuminate\Database\Eloquent\Relations\Pivot;

class ModuleSpecification extends Pivot
{
    protected $casts = [
        'years'          => 'json',
        'elective_group' => 'json',
        'core'           => 'boolean',
    ];

}
