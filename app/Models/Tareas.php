<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tarea extends Model
{
    use SoftDeletes;

    protected $table = 'tareas';

    protected $fillable = [
        'prospecto_id',
        'forma_contacto_id',
        'nivel_interes_id',
        'usuario_asignado_id',
        'fecha_realizar',
        'hora',
        'nota',
        'created_by',
        'updated_by'
    ];

    protected $dates = [
        'fecha_realizar',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Relación con Prospecto
    public function prospecto()
    {
        return $this->belongsTo(Prospecto::class);
    }

    // Relación con Usuario Asignado
    public function usuarioAsignado()
    {
        return $this->belongsTo(User::class, 'usuario_asignado_id');
    }

    // Relación con Usuario Creador
    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relación con Usuario Actualizador
    public function actualizador()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Relación con Forma de Contacto
    public function formaContacto()
    {
        return $this->belongsTo(FormaContacto::class);
    }

    // Relación con Nivel de Interés
    public function nivelInteres()
    {
        return $this->belongsTo(NivelInteres::class);
    }
}