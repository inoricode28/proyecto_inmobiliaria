<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoDepartamento extends Model
{
    protected $table = 'estados_departamento';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public $timestamps = false;

    public function departamentos()
    {
        return $this->hasMany(Departamento::class, 'estado_departamento_id');
    }

    public function estado()
{
    return $this->belongsTo(EstadoDepartamento::class);
}
}
