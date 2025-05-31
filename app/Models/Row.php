<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Row extends Model
{
    protected $fillable = [
        'id',
        'name',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
