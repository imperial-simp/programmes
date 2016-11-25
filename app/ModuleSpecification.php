<?php

namespace Imperial\Simp;

Illuminate\Database\Eloquent\Relations\Pivot;

class ModuleSpecification extends Pivot
{
    public function setExamWeightAttribute($weight)
    {
        dd($weight);
    }

    public function setCourseworkWeightAttribute($weight)
    {
        dd($weight);
    }

    public function setPracticalWeightAttribute($weight)
    {
        dd($weight);
    }
}
