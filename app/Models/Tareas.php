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
        'tarea_padre_id',
        'forma_contacto_id',
        'nivel_interes_id',
        'usuario_asignado_id',
        'fecha_realizar',
        'hora',
        'nota',
        'respuesta',
        'created_by',
        'updated_by'
    ];

    protected $dates = [
        'fecha_realizar',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

  
    public function usuarioAsignado()
    {
        return $this->belongsTo(User::class, 'usuario_asignado_id');
    }

    public function nivelInteres()
    {
        return $this->belongsTo(NivelInteres::class);
    }

    public function formaContacto()
    {
        return $this->belongsTo(FormaContacto::class);
    }


    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }
    // En app/Models/Tarea.php
    public function prospecto()
    {
        return $this->belongsTo(Prospecto::class);
    }

    public function cita()
    {
        return $this->hasOne(Cita::class);
    }

    // Accessor para fecha_contacto - obtiene la fecha del último contacto efectivo
    public function getFechaContactoAttribute()
    {
        // Buscar la última tarea efectiva del prospecto
        $ultimaTareaEfectiva = $this->prospecto->tareas()
            ->where('respuesta', 'efectiva')
            ->orderBy('created_at', 'desc')
            ->first();
        
        // Si existe una tarea efectiva, devolver su fecha de creación
        if ($ultimaTareaEfectiva) {
            return $ultimaTareaEfectiva->created_at;
        }
        
        return null;
    }

    // Método estático para obtener la fecha de contacto de un prospecto específico
    public static function getFechaContactoProspecto($prospectoId)
    {
        $ultimaTareaEfectiva = static::where('prospecto_id', $prospectoId)
            ->where('respuesta', 'efectiva')
            ->orderBy('created_at', 'desc')
            ->first();
        
        return $ultimaTareaEfectiva ? $ultimaTareaEfectiva->created_at : null;
    }

}