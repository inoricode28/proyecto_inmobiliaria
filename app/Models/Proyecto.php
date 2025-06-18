<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'ubicacion',
        'fecha_inicio',
        'fecha_entrega',
        'estado_proyecto_id',
        'empresa_constructora_id',
    ];

    public function estado()
    {
        return $this->belongsTo(EstadoProyecto::class, 'estado_proyecto_id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_constructora_id');
    }

    public function edificios()
    {
        return $this->hasMany(Edificio::class);
    }
}
