<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedioPago extends Model
{
    protected $table = 'medios_pago';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Relación con pagos de separación
     */
    public function pagosSeparacion()
    {
        return $this->hasMany(PagoSeparacion::class, 'medio_pago_id');
    }

    /**
     * Scope para obtener solo medios de pago activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}