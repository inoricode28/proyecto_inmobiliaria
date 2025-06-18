<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'departamentos';

    protected $fillable = [
        'edificio_id',
        'numero',
        'piso',
        'area_total',
        'area_construida',
        'numero_habitaciones',
        'numero_banos',
        'tiene_balcon',
        'tipo_departamento_id',
        'estado_departamento_id',
        'precio'
    ];

    public $timestamps = true;

    public function edificio()
    {
        return $this->belongsTo(Edificio::class);
    }

    public function tipo()
    {
        return $this->belongsTo(TipoDepartamento::class, 'tipo_departamento_id');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoDepartamento::class, 'estado_departamento_id');
    }
}
