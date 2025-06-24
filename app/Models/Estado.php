<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $table = 'estado';

    protected $fillable = [
        'activo',
    ];

    public $timestamps = false;

    protected $casts = [
        'activo' => 'boolean',
    ];
}
