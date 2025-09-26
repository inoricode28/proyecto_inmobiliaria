<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoCuota extends Model
{
    protected $table = 'tipos_cuota';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    /**
     * Scope para obtener solo tipos activos
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
        return $this->hasMany(CronogramaCuotaInicial::class, 'tipo_cuota_id');
    }
}