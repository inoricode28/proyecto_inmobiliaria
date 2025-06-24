<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Moneda extends Model
{
    protected $table = 'moneda';

    protected $fillable = [
        'nombre',
    ];

    public $timestamps = false;
}
