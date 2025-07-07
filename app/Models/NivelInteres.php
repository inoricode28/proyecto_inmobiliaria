<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NivelInteres extends Model
{
    protected $table = 'niveles_interes';

    protected $fillable = [
        'nombre',
        'descripcion',
        'color' 
    ];

    public $timestamps = false;

    // Relación con Tareas
    public function tareas()
    {
        return $this->hasMany(Tarea::class);
    }
}