<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaBancaria extends Model
{
    protected $table = 'cuentas_bancarias';

    protected $fillable = [
        'banco',
        'numero_cuenta',
        'tipo_cuenta',
        'moneda',
        'titular',
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
        return $this->hasMany(PagoSeparacion::class, 'cuenta_bancaria_id');
    }

    /**
     * Scope para obtener solo cuentas bancarias activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para filtrar por moneda
     */
    public function scopePorMoneda($query, $moneda)
    {
        return $query->where('moneda', $moneda);
    }

    /**
     * Scope para filtrar por banco
     */
    public function scopePorBanco($query, $banco)
    {
        return $query->where('banco', 'like', '%' . $banco . '%');
    }
}