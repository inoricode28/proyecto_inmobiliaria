<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoCuota extends Model
{
    protected $table = 'estados_cuota';

    protected $fillable = [
        'nombre',
        'descripcion',
        'color',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    /**
     * Scope para obtener solo estados activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Relación con cronograma cuota inicial
     */
    public function cronogramaCuotaInicial()
    {
        return $this->hasMany(CronogramaCuotaInicial::class, 'estado_id');
    }
}