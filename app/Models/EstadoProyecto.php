<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoProyecto extends Model
{

    protected $table = 'estados_proyecto';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    // Elimina esta línea si no necesitas desactivar timestamps
     public $timestamps = false;


    public function proyectos()
    {
        return $this->hasMany(Proyecto::class, 'estado_proyecto_id');
    }
}