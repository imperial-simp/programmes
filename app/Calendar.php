<?php

namespace Imperial\Simp;

use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    public $fillable = [
        'name',
        'year',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];
}
