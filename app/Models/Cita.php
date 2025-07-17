<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cita extends Model
{
    protected $table = 'citas';

    protected $fillable = [
        'tarea_id',
        'proyecto_id',
        'responsable_id',
        'fecha_cita',
        'hora_cita',
        'modalidad',
        'lugar',
        'comentarios',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'fecha_cita',
        'created_at',
        'updated_at',
    ];

    // Relación con la tarea
    public function tarea()
    {
        return $this->belongsTo(Tarea::class);
    }

    // Relación con el proyecto
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    // Relación con el usuario responsable
    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    // Relación con el usuario que creó el registro
    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relación con el usuario que actualizó el registro
    public function actualizador()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
