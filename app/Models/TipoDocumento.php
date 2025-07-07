<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoDocumento extends Model
{
    use SoftDeletes;

    protected $table = 'tipo_documento';

    protected $fillable = [
        'nombre',
        'descripcion'
    ];

    protected $dates = [
        'deleted_at'
    ];
}