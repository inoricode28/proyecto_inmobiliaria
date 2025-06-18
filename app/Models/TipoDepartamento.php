<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDepartamento extends Model
{
    protected $table = 'tipos_departamento';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public $timestamps = false;

    public function departamentos()
    {
        return $this->hasMany(Departamento::class, 'tipo_departamento_id');
    }
}
