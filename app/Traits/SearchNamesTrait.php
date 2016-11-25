<?php

namespace Imperial\Simp\Traits;

trait SearchNamesTrait
{
    public function scopeWhereNames($query, $names)
    {
        $names = (array) $names;

        return $query->where(function($query) use ($names) {
            return $query->whereIn('name', $names)
            ->orWhereIn('short_name', $names)
            ->orWhereIn('abbrev', $names);
        });
    }

    public function scopeWhereNamesOrCodes($query, $names)
    {
        $names = (array) $names;

        return $query->where(function($query) use ($names) {
            return $query->whereIn('name', $namess)
            ->orWhereIn('short_name', $names)
            ->orWhereIn('abbrev', $names)
            ->orWhereIn('oss_code', $names)
            ->orWhereIn('banner_code', $names);
        });
    }

    public function scopeWhereCodes($query, $names)
    {
        $names = (array) $names;

        return $query->where(function($query) use ($names) {
            return $query->whereIn('oss_code', $names)
            ->orWhereIn('banner_code', $names);
        });
    }
}
