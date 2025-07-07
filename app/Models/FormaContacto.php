<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormaContacto extends Model
{
    protected $table = 'formas_contacto';

    protected $fillable = [
        'nombre',
        'descripcion'
    ];

    public $timestamps = false;

    // Relación con Tareas
    public function tareas()
    {
        return $this->hasMany(Tarea::class);
    }
}