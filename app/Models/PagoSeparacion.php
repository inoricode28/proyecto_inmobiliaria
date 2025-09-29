<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagoSeparacion extends Model
{
    use HasFactory;

    protected $table = 'pagos_separacion';

    protected $fillable = [
        'separacion_id',
        'proforma_id',
        'moneda_id',
        'medio_pago_id',
        'cuenta_bancaria_id',
        'monto',
        'tipo_cambio',
        'monto_pago',
        'monto_convertido',
        'fecha_pago',
        'numero_operacion',
        'numero_documento',
        'agencia_bancaria',
        'archivo_comprobante',
        'observaciones',
        'registrado_por'
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'monto' => 'decimal:2',
        'tipo_cambio' => 'decimal:4',
        'monto_pago' => 'decimal:2',
        'monto_convertido' => 'decimal:2'
    ];

    /**
     * Relación con Separacion
     */
    public function separacion(): BelongsTo
    {
        return $this->belongsTo(Separacion::class);
    }

    /**
     * Relación con Moneda
     */
    public function moneda(): BelongsTo
    {
        return $this->belongsTo(Moneda::class);
    }

    /**
     * Relación con MedioPago
     */
    public function medioPago(): BelongsTo
    {
        return $this->belongsTo(MedioPago::class);
    }

    /**
     * Relación con CuentaBancaria
     */
    public function cuentaBancaria(): BelongsTo
    {
        return $this->belongsTo(CuentaBancaria::class);
    }
}