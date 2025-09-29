<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoComprobante extends Model
{
    protected $table = 'tipos_comprobante';

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
     * Relación con cronograma saldo a financiar
     */
    public function cronogramaSaldoFinanciar()
    {
        return $this->hasMany(CronogramaSaldoFinanciar::class, 'tipo_comprobante_id');
    }
}