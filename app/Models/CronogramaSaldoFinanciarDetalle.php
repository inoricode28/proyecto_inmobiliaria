<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CronogramaSaldoFinanciarDetalle extends Model
{
    use HasFactory;

    protected $table = 'cronograma_saldo_financiar_detalles';

    protected $fillable = [
        'cronograma_sf_id',
        'numero_cuota',
        'fecha_pago',
        'monto',
        'motivo',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'monto' => 'decimal:2',
    ];

    // Relaciones
    public function cronogramaSaldoFinanciar(): BelongsTo
    {
        return $this->belongsTo(CronogramaSaldoFinanciar::class, 'cronograma_sf_id');
    }
}
